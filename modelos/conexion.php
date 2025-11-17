<?php

// Cargar variables de entorno
require_once __DIR__ . '/../config.php';

class Conexion{

	static public function conectar(){

		try {
			// Obtener credenciales desde variables de entorno
			// Si no existen las variables, la conexión fallará
			$host = env('DB_HOST');
			$dbname = env('DB_NAME');
			$user = env('DB_USER');
			$pass = env('DB_PASS');

			if (!$host || !$dbname || !$user) {
				$error = 'Las variables de entorno de la base de datos no están configuradas. Revisa el archivo .env';
				Logger::error($error, [
					'host' => $host ? 'configurado' : 'faltante',
					'dbname' => $dbname ? 'configurado' : 'faltante',
					'user' => $user ? 'configurado' : 'faltante'
				]);
				throw new Exception($error);
			}

			$link = new PDO("mysql:host={$host};dbname={$dbname}",
							$user,
							$pass);

			$link->exec("set names utf8");

			Logger::info('Conexión a base de datos establecida correctamente', [
				'database' => $dbname
			]);

			return $link;

		} catch (PDOException $e) {
			Logger::error('Error al conectar a la base de datos', [
				'exception' => $e,
				'database' => $dbname ?? 'desconocida'
			]);
			throw $e;
		}

	}
}

