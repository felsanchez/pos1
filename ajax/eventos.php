<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar variables de entorno
require_once __DIR__ . '/../config.php';

// Obtener credenciales desde variables de entorno
// Si no existen las variables, la conexión fallará
$host = env('DB_HOST');
$user = env('DB_USER');
$pass = env('DB_PASS');
$dbname = env('DB_NAME');

if (!$host || !$dbname || !$user) {
	die('Error: Las variables de entorno de la base de datos no están configuradas. Revisa el archivo .env');
}

$conexion = new mysqli($host, $user, $pass, $dbname);

// Incluir la conexión centralizada
//require_once "../modelos/conexion.php";

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}


// Consulta para obtener las actividades
$sql = "SELECT id, descripcion, fecha FROM actividades";
$resultado = $conexion->query($sql);

$eventos = array();

while ($fila = $resultado->fetch_assoc()) {
    $eventos[] = array(
        'id' => $fila['id'],
        'title' => $fila['descripcion'],
        'start' => $fila['fecha']
    );
}

// Devolver los eventos en formato JSON
header('Content-Type: application/json');
echo json_encode($eventos);
?>
