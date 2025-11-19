<?php
require_once "../../../modelos/conexion.php";
$conn = Conexion::conectar();

// Obtener valores del formulario
$tipo = $_POST['tipo'] ?? null;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;
$vendedor = $_POST['vendedor'] ?? null;
$cliente = $_POST['cliente'] ?? null;
$producto = $_POST['producto'] ?? null;
$metodo_pago = $_POST['metodo_pago'] ?? null;

// Validación básica
if (!$tipo) {
  http_response_code(400);
  echo json_encode(["error" => "Tipo de fecha no especificado"]);
  exit;
}

// Construir la condición de fecha
$condicionFecha = "";
$params = [];
$paramIndex = 1;

switch ($tipo) {
  case 'hoy':
    $condicionFecha = "DATE(fecha) = CURDATE()";
    break;
  case 'ayer':
    $condicionFecha = "DATE(fecha) = CURDATE() - INTERVAL 1 DAY";
    break;
  case 'mes':
    $condicionFecha = "MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
    break;
  case 'personalizado':
    if (!$fecha_inicio || !$fecha_fin) {
      http_response_code(400);
      echo json_encode(["error" => "Fechas personalizadas incompletas"]);
      exit;
    }
    $condicionFecha = "DATE(fecha) BETWEEN ? AND ?";
    $params[$paramIndex++] = $fecha_inicio;
    $params[$paramIndex++] = $fecha_fin;
    break;
  default:
    http_response_code(400);
    echo json_encode(["error" => "Tipo de filtro no válido"]);
    exit;
}

// Agregar condición del estado
$where = "estado = 'venta' AND $condicionFecha";

// Agregar filtro de vendedor
if ($vendedor && $vendedor !== '') {
  $where .= " AND id_vendedor = ?";
  $params[$paramIndex++] = $vendedor;
}

// Agregar filtro de cliente
if ($cliente && $cliente !== '') {
  $where .= " AND id_cliente = ?";
  $params[$paramIndex++] = $cliente;
}

// Agregar filtro de producto (buscar en el JSON de productos)
if ($producto && $producto !== '') {
  // Buscar el ID del producto dentro del JSON de productos
  // El JSON tiene formato: [{"id":"1",...}, {"id":"2",...}]
  $where .= " AND (productos LIKE ? OR productos LIKE ?)";
  $params[$paramIndex++] = '%"id":"' . $producto . '"%';
  $params[$paramIndex++] = '%"id":' . $producto . '%';
}

// Agregar filtro de método de pago
if ($metodo_pago && $metodo_pago !== '') {
  // El campo metodo_pago puede contener el valor directamente o en un JSON
  $where .= " AND (metodo_pago LIKE ? OR metodo_pago = ?)";
  $params[$paramIndex++] = '%' . $metodo_pago . '%';
  $params[$paramIndex++] = $metodo_pago;
}

// Consulta preparada
$sql = "
  SELECT
    DATE(fecha) as fecha,
    SUM(total) as total_ventas
  FROM ventas
  WHERE $where
  GROUP BY DATE(fecha)
  ORDER BY fecha ASC
";

$stmt = $conn->prepare($sql);

// Bind de parámetros
if ($params) {
  foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
  }
}

$stmt->execute();

// Procesar resultados
$datos = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $datos[] = [
    'fecha' => $row['fecha'],
    'total_ventas' => (float)$row['total_ventas']
  ];
}

// Total general
$totalVentas = array_sum(array_column($datos, 'total_ventas'));

// Respuesta JSON
echo json_encode([
  'datos' => $datos,
  'total' => $totalVentas
]);
