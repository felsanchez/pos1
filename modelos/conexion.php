<?php

// Cargar variables de entorno
require_once __DIR__ . '/../config.php';

class Conexion{

	static public function conectar(){

		// Obtener credenciales desde variables de entorno
		$host = env('DB_HOST', 'localhost');
		$dbname = env('DB_NAME', 'pos');
		$user = env('DB_USER', 'root');
		$pass = env('DB_PASS', '');

		$link = new PDO("mysql:host={$host};dbname={$dbname}",
						$user,
						$pass);

		$link->exec("set names utf8");

		return $link;

	}
}

