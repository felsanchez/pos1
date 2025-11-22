<?php

require_once "../controladores/estados-actividades.controlador.php";
require_once "../modelos/estados-actividades.modelo.php";

class AjaxEstadosActividades{

	/*=============================================
	EDITAR ESTADO DE ACTIVIDAD
	=============================================*/

	public $idEstado;

	public function ajaxEditarEstadoActividad(){

		$item = "id";
		$valor = $this->idEstado;

		$respuesta = ControladorEstadosActividades::ctrMostrarEstadosActividades($item, $valor);

		echo json_encode($respuesta);
	}

}

/*=============================================
EDITAR ESTADO DE ACTIVIDAD
=============================================*/

if(isset($_POST["idEstado"])){

	$estado = new AjaxEstadosActividades();
	$estado -> idEstado = $_POST["idEstado"];
	$estado -> ajaxEditarEstadoActividad();
}
