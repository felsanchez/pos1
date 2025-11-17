<?php

require_once "../controladores/estados-actividades.controlador.php";
require_once "../modelos/estados-actividades.modelo.php"; 

class AjaxEstadosActividades{ 

	/*=============================================
	EDITAR ESTADO
	=============================================*/ 

	public $idEstado; 

	public function ajaxEditarEstado(){ 

		$item = "id";
		$valor = $this->idEstado; 

		$respuesta = ControladorEstadosActividades::ctrMostrarEstadosActividades($item, $valor); 

		echo json_encode($respuesta);
	}

}
 

/*=============================================
EDITAR ESTADO
=============================================*/

if(isset($_POST["idEstado"])){ 

	$editar = new AjaxEstadosActividades();
	$editar -> idEstado = $_POST["idEstado"];
	$editar -> ajaxEditarEstado();
}