<?php
session_start();
$_SESSION["validarSesion"] = "ok";
$_SESSION["perfil"] = "Administrador";
$_SESSION["usuario"] = "admin-test";

echo "<h1>Prueba de Endpoint AJAX de Logs</h1>";
echo "<pre>";

// Simular llamada para obtener logs
echo "=== TEST 1: Obtener Logs ===\n";
$_POST = [
    'accion' => 'obtener_logs',
    'fecha' => date('Y-m-d'),
    'nivel' => '',
    'limite' => 10
];

ob_start();
include 'ajax/logs.ajax.php';
$resultado = ob_get_clean();

echo "Resultado:\n";
print_r(json_decode($resultado, true));

echo "\n\n";

// Simular llamada para obtener estadísticas
echo "=== TEST 2: Obtener Estadísticas ===\n";
$_POST = [
    'accion' => 'obtener_estadisticas',
    'fecha' => date('Y-m-d')
];

ob_start();
include 'ajax/logs.ajax.php';
$resultado = ob_get_clean();

echo "Resultado:\n";
print_r(json_decode($resultado, true));

echo "\n\n";

// Simular llamada para obtener archivos
echo "=== TEST 3: Obtener Archivos de Log ===\n";
$_POST = [
    'accion' => 'obtener_archivos'
];

ob_start();
include 'ajax/logs.ajax.php';
$resultado = ob_get_clean();

echo "Resultado:\n";
print_r(json_decode($resultado, true));

echo "</pre>";
?>