<?php
require_once "../../../modelos/conexion.php";
$conn = Conexion::conectar();

// Obtener valores del formulario
$tipo = $_POST['tipo'] ?? null;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;
$id_categoria = $_POST['id_categoria'] ?? null;

// Validación básica
if (!$tipo) {
  http_response_code(400);
  echo json_encode(["error" => "Tipo de fecha no especificado"]);
  exit;
}

// Construir la condición de fecha
$condicionFechaVentas = "";
$condicionFechaGastos = "";
$paramsVentas = [];
$paramsGastos = [];
$paramIndexVentas = 1;
$paramIndexGastos = 1;

switch ($tipo) {
  case 'todo':
    $condicionFechaVentas = "1=1";
    $condicionFechaGastos = "1=1";
    break;
  case 'hoy':
    $condicionFechaVentas = "DATE(fecha) = CURDATE()";
    $condicionFechaGastos = "DATE(fecha) = CURDATE()";
    break;
  case 'ayer':
    $condicionFechaVentas = "DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
    $condicionFechaGastos = "DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
    break;
  case 'mes':
    $condicionFechaVentas = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
    $condicionFechaGastos = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
    break;
  case 'personalizado':
    if (!$fecha_inicio || !$fecha_fin) {
      http_response_code(400);
      echo json_encode(["error" => "Fechas personalizadas incompletas"]);
      exit;
    }
    $condicionFechaVentas = "DATE(fecha) BETWEEN ? AND ?";
    $condicionFechaGastos = "DATE(fecha) BETWEEN ? AND ?";
    $paramsVentas[$paramIndexVentas++] = $fecha_inicio;
    $paramsVentas[$paramIndexVentas++] = $fecha_fin;
    $paramsGastos[$paramIndexGastos++] = $fecha_inicio;
    $paramsGastos[$paramIndexGastos++] = $fecha_fin;
    break;
  default:
    http_response_code(400);
    echo json_encode(["error" => "Tipo de filtro no válido"]);
    exit;
}

// =============================================
// TOTAL DE INGRESOS (VENTAS)
// =============================================
$sqlIngresos = "SELECT COALESCE(SUM(total), 0) as total FROM ventas WHERE estado = 'venta' AND $condicionFechaVentas";
$stmtIngresos = $conn->prepare($sqlIngresos);
if (!empty($paramsVentas)) {
  foreach ($paramsVentas as $key => $value) {
    $stmtIngresos->bindValue($key, $value);
  }
}
$stmtIngresos->execute();
$totalIngresos = (float) $stmtIngresos->fetch(PDO::FETCH_ASSOC)['total'];

// =============================================
// TOTAL DE GASTOS
// =============================================
$whereGastos = "estado = 'aprobado' AND $condicionFechaGastos";

// Filtro por categoría
if (!empty($id_categoria)) {
  $whereGastos .= " AND id_categoria_gasto = ?";
  $paramsGastos[$paramIndexGastos++] = $id_categoria;
}

$sqlGastos = "SELECT COALESCE(SUM(monto), 0) as total FROM gastos WHERE $whereGastos";
$stmtGastos = $conn->prepare($sqlGastos);
if (!empty($paramsGastos)) {
  foreach ($paramsGastos as $key => $value) {
    $stmtGastos->bindValue($key, $value);
  }
}
$stmtGastos->execute();
$totalGastos = (float) $stmtGastos->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular utilidad
$utilidad = $totalIngresos - $totalGastos;

// =============================================
// EVOLUCIÓN TEMPORAL (INGRESOS VS GASTOS POR DÍA)
// =============================================
$evolucion = [];

// Obtener ingresos por día
$sqlEvolucionIngresos = "
  SELECT DATE(fecha) as fecha, COALESCE(SUM(total), 0) as total
  FROM ventas
  WHERE estado = 'venta' AND $condicionFechaVentas
  GROUP BY DATE(fecha)
  ORDER BY fecha ASC
";
$stmtEvolucionIngresos = $conn->prepare($sqlEvolucionIngresos);
if (!empty($paramsVentas)) {
  $idx = 1;
  foreach ($paramsVentas as $key => $value) {
    $stmtEvolucionIngresos->bindValue($idx++, $value);
  }
}
$stmtEvolucionIngresos->execute();
$ingresosEvolucion = [];
while ($row = $stmtEvolucionIngresos->fetch(PDO::FETCH_ASSOC)) {
  $ingresosEvolucion[$row['fecha']] = (float) $row['total'];
}

// Obtener gastos por día (sin filtro de categoría para la evolución)
$sqlEvolucionGastos = "
  SELECT DATE(fecha) as fecha, COALESCE(SUM(monto), 0) as total
  FROM gastos
  WHERE estado = 'aprobado' AND $condicionFechaGastos
  GROUP BY DATE(fecha)
  ORDER BY fecha ASC
";
$stmtEvolucionGastos = $conn->prepare($sqlEvolucionGastos);
if (!empty($paramsGastos)) {
  $idx = 1;
  // Solo usar los parámetros de fecha, no categoría
  if ($tipo === 'personalizado') {
    $stmtEvolucionGastos->bindValue(1, $fecha_inicio);
    $stmtEvolucionGastos->bindValue(2, $fecha_fin);
  }
}
$stmtEvolucionGastos->execute();
$gastosEvolucion = [];
while ($row = $stmtEvolucionGastos->fetch(PDO::FETCH_ASSOC)) {
  $gastosEvolucion[$row['fecha']] = (float) $row['total'];
}

// Combinar fechas únicas
$todasFechas = array_unique(array_merge(array_keys($ingresosEvolucion), array_keys($gastosEvolucion)));
sort($todasFechas);

foreach ($todasFechas as $fecha) {
  $evolucion[] = [
    'fecha' => $fecha,
    'ingresos' => $ingresosEvolucion[$fecha] ?? 0,
    'gastos' => $gastosEvolucion[$fecha] ?? 0
  ];
}

// =============================================
// GASTOS POR CATEGORÍA
// =============================================
$paramsCategoria = [];
$paramIdxCat = 1;

$sqlGastosCategoria = "
  SELECT c.nombre, c.color, COALESCE(SUM(g.monto), 0) as total
  FROM gastos g
  INNER JOIN categorias_gastos c ON g.id_categoria_gasto = c.id
  WHERE g.estado = 'aprobado' AND $condicionFechaGastos
  GROUP BY g.id_categoria_gasto
  ORDER BY total DESC
";

// Reemplazar la condición de fecha para la consulta de categorías
if ($tipo === 'personalizado') {
  $sqlGastosCategoria = str_replace($condicionFechaGastos, "DATE(g.fecha) BETWEEN ? AND ?", $sqlGastosCategoria);
  $paramsCategoria[$paramIdxCat++] = $fecha_inicio;
  $paramsCategoria[$paramIdxCat++] = $fecha_fin;
} else {
  $sqlGastosCategoria = str_replace($condicionFechaGastos, str_replace("fecha", "g.fecha", $condicionFechaGastos), $sqlGastosCategoria);
}

$stmtGastosCategoria = $conn->prepare($sqlGastosCategoria);
if (!empty($paramsCategoria)) {
  foreach ($paramsCategoria as $key => $value) {
    $stmtGastosCategoria->bindValue($key, $value);
  }
}
$stmtGastosCategoria->execute();
$gastosCategoria = $stmtGastosCategoria->fetchAll(PDO::FETCH_ASSOC);

// =============================================
// RESPUESTA JSON
// =============================================
echo json_encode([
  'totales' => [
    'ingresos' => $totalIngresos,
    'gastos' => $totalGastos,
    'utilidad' => $utilidad
  ],
  'evolucion' => $evolucion,
  'gastos_categoria' => $gastosCategoria
]);
