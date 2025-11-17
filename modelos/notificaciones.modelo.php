<?php

require_once "conexion.php";

class ModeloNotificaciones{

	/*=============================================
	CREAR NOTIFICACIÓN
	=============================================*/

	static public function mdlCrearNotificacion($datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO notificaciones(tipo, titulo, mensaje, referencia_tipo, referencia_id)
												VALUES (:tipo, :titulo, :mensaje, :referencia_tipo, :referencia_id)");

		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":mensaje", $datos["mensaje"], PDO::PARAM_STR);
		$stmt->bindParam(":referencia_tipo", $datos["referencia_tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":referencia_id", $datos["referencia_id"], PDO::PARAM_INT);

		if($stmt->execute()){
			return "ok";
		}else{
			return "error";
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	OBTENER NOTIFICACIONES
	=============================================*/

	static public function mdlObtenerNotificaciones($cantidad = null, $soloNoLeidas = false){

		$sql = "SELECT * FROM notificaciones";

		if($soloNoLeidas){
			$sql .= " WHERE leida = 0";
		}

		$sql .= " ORDER BY fecha DESC";

		if($cantidad){
			$sql .= " LIMIT :cantidad";
		}

		$stmt = Conexion::conectar()->prepare($sql);

		if($cantidad){
			$stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
		}

		$stmt->execute();

		if($cantidad == 1){
			return $stmt->fetch();
		} else {
			return $stmt->fetchAll();
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	CONTAR NOTIFICACIONES NO LEÍDAS
	=============================================*/

	static public function mdlContarNoLeidas(){

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE leida = 0");

		$stmt->execute();

		$resultado = $stmt->fetch();

		return $resultado["total"];

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	MARCAR NOTIFICACIÓN COMO LEÍDA
	=============================================*/

	static public function mdlMarcarComoLeida($id){

		$stmt = Conexion::conectar()->prepare("UPDATE notificaciones SET leida = 1 WHERE id = :id");

		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if($stmt->execute()){
			return "ok";
		}else{
			return "error";
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	MARCAR TODAS COMO LEÍDAS
	=============================================*/

	static public function mdlMarcarTodasComoLeidas(){

		$stmt = Conexion::conectar()->prepare("UPDATE notificaciones SET leida = 1 WHERE leida = 0");

		if($stmt->execute()){
			return "ok";
		}else{
			return "error";
		}

		$stmt->close();
		$stmt = null;

	}

	/*=============================================
	ELIMINAR NOTIFICACIÓN
	=============================================*/

	static public function mdlEliminarNotificacion($id){

		$stmt = Conexion::conectar()->prepare("DELETE FROM notificaciones WHERE id = :id");

		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if($stmt->execute()){
			return "ok";
		}else{
			return "error";
		}

		$stmt->close();
		$stmt = null;

	}


    /*=============================================
	ELIMINAR MÚLTIPLES NOTIFICACIONES
	=============================================*/ 

	static public function mdlEliminarNotificaciones($idsJson){ 

		$ids = json_decode($idsJson, true);

		if(empty($ids) || !is_array($ids)){
			return "error";
		}

		// Crear placeholders para el query
		$placeholders = implode(',', array_fill(0, count($ids), '?')); 

		$stmt = Conexion::conectar()->prepare("DELETE FROM notificaciones WHERE id IN ($placeholders)"); 

		// Bind de cada ID
		foreach($ids as $index => $id){
			$stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
		} 

		if($stmt->execute()){
			return "ok";

		}else{
			return "error";
		} 

		$stmt->close();
		$stmt = null;
	}

    
	/*=============================================
	VERIFICAR SI YA EXISTE NOTIFICACIÓN DE STOCK
	=============================================*/

	static public function mdlExisteNotificacionStock($tipo, $idProducto){

		$stmt = Conexion::conectar()->prepare("SELECT id FROM notificaciones
												WHERE tipo = :tipo
												AND referencia_tipo = 'producto'
												AND referencia_id = :referencia_id
												AND leida = 0
												LIMIT 1");

		$stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
		$stmt->bindParam(":referencia_id", $idProducto, PDO::PARAM_INT);

		$stmt->execute();

		$resultado = $stmt->fetch();

		return $resultado ? true : false;

		$stmt->close();
		$stmt = null;

	}


	/*=============================================
	VERIFICAR SI EXISTE NOTIFICACIÓN (GENÉRICA)
	=============================================*/

	static public function mdlExisteNotificacion($tipo, $referenciaId, $referenciaTipo){

		$stmt = Conexion::conectar()->prepare("SELECT id FROM notificaciones
												WHERE tipo = :tipo
												AND referencia_tipo = :referencia_tipo
												AND referencia_id = :referencia_id
												AND leida = 0
												LIMIT 1");

		$stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
		$stmt->bindParam(":referencia_tipo", $referenciaTipo, PDO::PARAM_STR);
		$stmt->bindParam(":referencia_id", $referenciaId, PDO::PARAM_INT);

		$stmt->execute();

		$resultado = $stmt->fetch();

		return $resultado ? true : false;

		$stmt->close();
		$stmt = null;

	}
}