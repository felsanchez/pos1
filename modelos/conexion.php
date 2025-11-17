<?php

// Cargar variables de entorno
require_once __DIR__ . '/../config.php';

class Conexion{

	static public function conectar(){

		// Obtener credenciales desde variables de entorno
		// Si no existen las variables, la conexión fallará
		$host = env('DB_HOST');
		$dbname = env('DB_NAME');
		$user = env('DB_USER');
		$pass = env('DB_PASS');

		if (!$host || !$dbname || !$user) {
			throw new Exception('Las variables de entorno de la base de datos no están configuradas. Revisa el archivo .env');
		}

		$link = new PDO("mysql:host={$host};dbname={$dbname}",
						$user,
						$pass);

		$link->exec("set names utf8");

		return $link;

	}
}

