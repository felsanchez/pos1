<?php

require_once "../controladores/gastos.controlador.php";
require_once "../modelos/gastos.modelo.php";

class AjaxGastos{

	/*=============================================
	EDITAR GASTO
	=============================================*/

	public $idGasto;

	public function ajaxEditarGasto(){

		$item = "id";
		$valor = $this->idGasto;

		$respuesta = ControladorGastos::ctrMostrarGastos($item, $valor);

		echo json_encode($respuesta);

	}

	/*=============================================
	OBTENER GASTOS FILTRADOS
	=============================================*/

	public $fechaInicio;
	public $fechaFin;
	public $categoria;
	public $proveedor;

	public function ajaxObtenerGastosFiltrados(){

		$respuesta = ControladorGastos::ctrMostrarGastosFiltrados(
			$this->fechaInicio,
			$this->fechaFin,
			$this->categoria,
			$this->proveedor
		);

		echo json_encode($respuesta);

	}

	/*=============================================
	OBTENER TOTAL DE GASTOS
	=============================================*/

	public function ajaxObtenerTotalGastos(){

		$respuesta = ControladorGastos::ctrSumarTotalGastos();

		echo json_encode($respuesta);

	}

	/*=============================================
	OBTENER GASTOS POR CATEGORÍA
	=============================================*/

	public function ajaxObtenerGastosPorCategoria(){

		$respuesta = ControladorGastos::ctrGastosPorCategoria();

		echo json_encode($respuesta);

	}

}

/*=============================================
EDITAR GASTO
=============================================*/

if(isset($_POST["idGasto"])){

	$gasto = new AjaxGastos();
	$gasto -> idGasto = $_POST["idGasto"];
	$gasto -> ajaxEditarGasto();
}

/*=============================================
OBTENER GASTOS FILTRADOS
=============================================*/

if(isset($_POST["accion"]) && $_POST["accion"] == "filtrarGastos"){

	$gastos = new AjaxGastos();
	$gastos -> fechaInicio = $_POST["fechaInicio"];
	$gastos -> fechaFin = $_POST["fechaFin"];
	$gastos -> categoria = $_POST["categoria"];
	$gastos -> proveedor = $_POST["proveedor"];
	$gastos -> ajaxObtenerGastosFiltrados();
}

/*=============================================
OBTENER TOTAL DE GASTOS
=============================================*/

if(isset($_POST["accion"]) && $_POST["accion"] == "obtenerTotal"){

	$gastos = new AjaxGastos();
	$gastos -> ajaxObtenerTotalGastos();
}

/*=============================================
OBTENER GASTOS POR CATEGORÍA
=============================================*/

if(isset($_POST["accion"]) && $_POST["accion"] == "obtenerPorCategoria"){

	$gastos = new AjaxGastos();
	$gastos -> ajaxObtenerGastosPorCategoria();
}