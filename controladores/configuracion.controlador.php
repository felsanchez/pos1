<?php

class ControladorConfiguracion{

	/*=============================================
	OBTENER CONFIGURACIÓN
	=============================================*/

	static public function ctrObtenerConfiguracion(){

		$respuesta = ModeloConfiguracion::mdlObtenerConfiguracion();

		return $respuesta;

	}

	/*=============================================
	ACTUALIZAR CONFIGURACIÓN
	=============================================*/

	static public function ctrActualizarConfiguracion(){

		if(isset($_POST["nombreEmpresa"])){

			/*=============================================
			VALIDAR LOGO
			=============================================*/

			$rutaLogo = $_POST["logoActual"];

			if(isset($_FILES["nuevoLogo"]["tmp_name"]) && !empty($_FILES["nuevoLogo"]["tmp_name"])){

				list($ancho, $alto) = getimagesize($_FILES["nuevoLogo"]["tmp_name"]);

				$nuevoAncho = 500;
				$nuevoAlto = 500;

				/*=============================================
				CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR EL LOGO
				=============================================*/

				$directorio = "vistas/img/configuracion/";

				if(!file_exists($directorio)){
					mkdir($directorio, 0755, true);
				}

				/*=============================================
				ELIMINAR LOGO ANTERIOR SI EXISTE
				=============================================*/

				if(!empty($_POST["logoActual"]) && file_exists($_POST["logoActual"])){
					unlink($_POST["logoActual"]);
				}

				/*=============================================
				DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
				=============================================*/

				if($_FILES["nuevoLogo"]["type"] == "image/jpeg"){

					$aleatorio = mt_rand(100,999);

					$rutaLogo = $directorio.$aleatorio.".jpg";

					$origen = imagecreatefromjpeg($_FILES["nuevoLogo"]["tmp_name"]);

					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

					imagejpeg($destino, $rutaLogo);

				}

				if($_FILES["nuevoLogo"]["type"] == "image/png"){

					$aleatorio = mt_rand(100,999);

					$rutaLogo = $directorio.$aleatorio.".png";

					$origen = imagecreatefrompng($_FILES["nuevoLogo"]["tmp_name"]);

					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

					imagepng($destino, $rutaLogo);

				}

			}

			$tabla = "configuracion";

			// Procesar checkboxes de alertas

			$alertaStockBajo = isset($_POST["alertaStockBajo"]) ? 1 : 0;

			$alertaStockAgotado = isset($_POST["alertaStockAgotado"]) ? 1 : 0; 

			$alertaActividadesPendientes = isset($_POST["alertaActividadesPendientes"]) ? 1 : 0;

			$alertaGastosProximos = isset($_POST["alertaGastosProximos"]) ? 1 : 0;

 			$datos = array("nombre_empresa" => $_POST["nombreEmpresa"],
						   "nit" => $_POST["nitEmpresa"],
						   "direccion" => $_POST["direccionEmpresa"],
						   "telefono" => $_POST["telefonoEmpresa"],
						   "correo" => $_POST["correoEmpresa"],
						   "logo" => $rutaLogo,
						   "impuesto_defecto" => $_POST["impuestoDefecto"],
						   "moneda" => $_POST["moneda"],
						   "formato_codigo_venta" => $_POST["formatoCodigoVenta"],
						   "medios_pago" => $_POST["mediosPago"],
						   "tipo_codigo_producto" => $_POST["tipoCodigoProducto"],
						   "alerta_stock_bajo" => $alertaStockBajo,
						   "umbral_stock_minimo" => $_POST["umbralStockMinimo"],
						   "alerta_stock_agotado" => $alertaStockAgotado,
						   "alerta_actividades_pendientes" => $alertaActividadesPendientes,
						   "dias_antes_actividad" => $_POST["diasAntesActividad"],
						   "alerta_gastos_proximos" => $alertaGastosProximos,
						   "dias_antes_gasto" => $_POST["diasAntesGasto"],
						   "alerta_agente_ia" => $alertaAgenteIA,
						   "mensaje_ticket" => $_POST["mensajeTicket"],
							"color_principal" => $_POST["colorPrincipal"],
						   "color_secundario" => $_POST["colorSecundario"]);

			$respuesta = ModeloConfiguracion::mdlActualizarConfiguracion($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "La configuración ha sido actualizada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
							if (result.value) {

							window.location = "configuracion";

							}
						})

				</script>';

			}

		}

	}

}