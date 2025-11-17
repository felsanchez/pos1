<?php

class ControladorNotificaciones{

	/*=============================================
	CREAR NOTIFICACIÓN
	=============================================*/

	static public function ctrCrearNotificacion($tipo, $titulo, $mensaje, $referenciaTipo = null, $referenciaId = null){

		$datos = array(
			"tipo" => $tipo,
			"titulo" => $titulo,
			"mensaje" => $mensaje,
			"referencia_tipo" => $referenciaTipo,
			"referencia_id" => $referenciaId
		);

		$respuesta = ModeloNotificaciones::mdlCrearNotificacion($datos);

		return $respuesta;

	}

	/*=============================================
	OBTENER NOTIFICACIONES
	=============================================*/

	static public function ctrObtenerNotificaciones($cantidad = null, $soloNoLeidas = false){

		$respuesta = ModeloNotificaciones::mdlObtenerNotificaciones($cantidad, $soloNoLeidas);

		return $respuesta;

	}

	/*=============================================
	CONTAR NOTIFICACIONES NO LEÍDAS
	=============================================*/

	static public function ctrContarNoLeidas(){

		$respuesta = ModeloNotificaciones::mdlContarNoLeidas();

		return $respuesta;

	}

	/*=============================================
	MARCAR COMO LEÍDA
	=============================================*/

	static public function ctrMarcarComoLeida(){

		if(isset($_POST["idNotificacion"])){

			$id = $_POST["idNotificacion"];

			$respuesta = ModeloNotificaciones::mdlMarcarComoLeida($id);

			return $respuesta;

		}

	}

	/*=============================================
	MARCAR TODAS COMO LEÍDAS
	=============================================*/

	static public function ctrMarcarTodasComoLeidas(){

		if(isset($_POST["marcarTodasLeidas"])){

			$respuesta = ModeloNotificaciones::mdlMarcarTodasComoLeidas();

			return $respuesta;

		}

	}

	/*=============================================
	VERIFICAR STOCK DE PRODUCTOS Y GENERAR NOTIFICACIONES
	=============================================*/

	static public function ctrVerificarStockProductos(){

		// Obtener configuración
		$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();

		$alertaStockBajo = isset($configuracion["alerta_stock_bajo"]) ? $configuracion["alerta_stock_bajo"] : 1;
		$umbralStockMinimo = isset($configuracion["umbral_stock_minimo"]) ? $configuracion["umbral_stock_minimo"] : 5;
		$alertaStockAgotado = isset($configuracion["alerta_stock_agotado"]) ? $configuracion["alerta_stock_agotado"] : 1;

		// Si ninguna alerta está activa, no hacer nada
		if(!$alertaStockBajo && !$alertaStockAgotado){
			return;
		}

		// Obtener todos los productos
		$productos = ControladorProductos::ctrMostrarProductos(null, null, "id");

		foreach($productos as $producto){

			// Verificar stock agotado
			if($alertaStockAgotado && $producto["stock"] == 0){

				// Verificar si ya existe una notificación no leída para este producto
				$existe = ModeloNotificaciones::mdlExisteNotificacionStock("stock_agotado", $producto["id"]);

				if(!$existe){
					// Crear notificación
					ControladorNotificaciones::ctrCrearNotificacion(
						"stock_agotado",
						"Stock Agotado",
						"El producto \"".$producto["descripcion"]."\" (Código: ".$producto["codigo"].") se ha agotado.",
						"producto",
						$producto["id"]
					);
				}

			}
			// Verificar stock bajo (pero no agotado)
			else if($alertaStockBajo && $producto["stock"] > 0 && $producto["stock"] <= $umbralStockMinimo){

				// Verificar si ya existe una notificación no leída para este producto
				$existe = ModeloNotificaciones::mdlExisteNotificacionStock("stock_bajo", $producto["id"]);

				if(!$existe){
					// Crear notificación
					ControladorNotificaciones::ctrCrearNotificacion(
						"stock_bajo",
						"Stock Bajo",
						"El producto \"".$producto["descripcion"]."\" (Código: ".$producto["codigo"].") tiene stock bajo: ".$producto["stock"]." unidades.",
						"producto",
						$producto["id"]
					);
				}

			}

		}

	}

	/*=============================================
	VERIFICAR ACTIVIDADES PRÓXIMAS Y GENERAR NOTIFICACIONES
	=============================================*/

	static public function ctrVerificarActividadesProximas(){

		// Obtener configuración
		$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();

		$alertaActividades = isset($configuracion["alerta_actividades_pendientes"]) ? $configuracion["alerta_actividades_pendientes"] : 1;
		$diasAntes = isset($configuracion["dias_antes_actividad"]) ? $configuracion["dias_antes_actividad"] : 3;

		// Si la alerta está desactivada, no hacer nada
		if(!$alertaActividades){
			return;
		}

		// Calcular fecha objetivo (hoy + días antes)
		$fechaObjetivo = date('Y-m-d', strtotime("+$diasAntes days"));
		$fechaHoy = date('Y-m-d');

		// Obtener actividades que vencen dentro del rango
		$actividades = ControladorActividades::ctrMostrarActividades(null, null);

		if(!$actividades){
			return;
		}

		foreach($actividades as $actividad){

			// Solo alertar sobre actividades pendientes (no completadas)
			if(isset($actividad["fecha"]) && !empty($actividad["fecha"])){

				$fechaActividad = date('Y-m-d', strtotime($actividad["fecha"]));

				// Si la fecha de la actividad está dentro del rango de alerta
				if($fechaActividad >= $fechaHoy && $fechaActividad <= $fechaObjetivo){

					// Verificar si ya existe una notificación no leída para esta actividad
					$existe = ModeloNotificaciones::mdlExisteNotificacion("actividad_proxima", $actividad["id"], "actividad");

					if(!$existe){
						// Calcular días faltantes
						$diasFaltantes = (strtotime($fechaActividad) - strtotime($fechaHoy)) / 86400;
						$diasFaltantes = round($diasFaltantes);

						$mensajeDias = $diasFaltantes == 0 ? "hoy" : ($diasFaltantes == 1 ? "mañana" : "en $diasFaltantes días");

						// Crear notificación
						ControladorNotificaciones::ctrCrearNotificacion(
							"actividad_proxima",
							"Actividad Próxima",
							"La actividad \"".$actividad["descripcion"]."\" está programada para $mensajeDias (".$actividad["fecha"].").",
							"actividad",
							$actividad["id"]
						);
					}

				}

			}

		}

	}

	/*=============================================
	VERIFICAR GASTOS PRÓXIMOS A VENCER Y GENERAR NOTIFICACIONES
	=============================================*/

	static public function ctrVerificarGastosProximos(){

		// Obtener configuración
		$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();

		$alertaGastos = isset($configuracion["alerta_gastos_proximos"]) ? $configuracion["alerta_gastos_proximos"] : 1;
		$diasAntes = isset($configuracion["dias_antes_gasto"]) ? $configuracion["dias_antes_gasto"] : 5;

		// Si la alerta está desactivada, no hacer nada
		if(!$alertaGastos){
			return;
		}

		// Calcular fecha objetivo (hoy + días antes)
		$fechaObjetivo = date('Y-m-d', strtotime("+$diasAntes days"));
		$fechaHoy = date('Y-m-d');

		// Obtener gastos que vencen dentro del rango
		$gastos = ControladorGastos::ctrMostrarGastos(null, null);

		if(!$gastos){
			return;
		}

		foreach($gastos as $gasto){

			// Solo alertar sobre gastos que tienen fecha de vencimiento
			if(isset($gasto["fecha"]) && !empty($gasto["fecha"])){

				$fechaVencimiento = date('Y-m-d', strtotime($gasto["fecha"]));

				// Si la fecha de vencimiento está dentro del rango de alerta
				if($fechaVencimiento >= $fechaHoy && $fechaVencimiento <= $fechaObjetivo){

					// Verificar si ya existe una notificación no leída para este gasto
					$existe = ModeloNotificaciones::mdlExisteNotificacion("gasto_proximo", $gasto["id"], "gasto");

					if(!$existe){
						// Calcular días faltantes
						$diasFaltantes = (strtotime($fechaVencimiento) - strtotime($fechaHoy)) / 86400;
						$diasFaltantes = round($diasFaltantes);

						$mensajeDias = $diasFaltantes == 0 ? "hoy" : ($diasFaltantes == 1 ? "mañana" : "en $diasFaltantes días");

						// Crear notificación
						ControladorNotificaciones::ctrCrearNotificacion(
							"gasto_proximo",
							"Gasto Próximo a Vencer",
							"El gasto \"".$gasto["concepto"]."\" vence $mensajeDias (".$gasto["fecha"]."). Monto: $".number_format($gasto["monto"], 2).".",
							"gasto",
							$gasto["id"]
						);
					}

				}

			}

		}

	}


    /*=============================================
    VERIFICAR SI ORDEN PROVIENE DE AGENTE IA Y GENERAR NOTIFICACIÓN
    =============================================*/

    static public function ctrVerificarOrdenAgenteIA($codigoVenta = null){ 

		// Obtener configuración
		$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion(); 

		$alertaAgenteIA = isset($configuracion["alerta_agente_ia"]) ? $configuracion["alerta_agente_ia"] : 1; 

		// Si la alerta está desactivada, no hacer nada
		if(!$alertaAgenteIA){
			return;
		} 

		$tabla = "ventas"; 

		// Si se proporciona un código específico, verificar solo esa orden
		if($codigoVenta !== null){ 

			$item = "codigo";
			$venta = ModeloVentas::mdlMostrarVentas($tabla, $item, $codigoVenta); 

			if(!$venta){
				return;
			} 

			// Verificar si el campo 'extra' contiene 'n8n'
			if(isset($venta["extra"]) && !empty($venta["extra"]) && stripos($venta["extra"], "n8n") !== false){ 

				// Verificar si ya existe una notificación para esta orden
				$existe = ModeloNotificaciones::mdlExisteNotificacion("orden_agente_ia", $venta["id"], "venta");

 				if(!$existe){ 

					// Crear notificación
					ControladorNotificaciones::ctrCrearNotificacion(
						"orden_agente_ia",
						"Orden desde Agente IA",
						"La orden #".$venta["codigo"]." fue creada desde el Agente IA.",
						"venta",
						$venta["id"]
					); 
				} 
			}

		} else {
			// Si no se proporciona código, verificar todas las órdenes
			$ordenes = ModeloVentas::mdlMostrarVentas($tabla, null, null); 

			if(!$ordenes){
				return;
			}

 			foreach($ordenes as $venta){ 

				// Solo verificar órdenes (no ventas)
				if($venta["estado"] != "orden"){
					continue;
				}

				// Verificar si el campo 'extra' contiene 'n8n'
				if(isset($venta["extra"]) && !empty($venta["extra"]) && stripos($venta["extra"], "n8n") !== false){ 

					// Verificar si ya existe una notificación para esta orden
					$existe = ModeloNotificaciones::mdlExisteNotificacion("orden_agente_ia", $venta["id"], "venta");

					if(!$existe){ 

						// Crear notificación
						ControladorNotificaciones::ctrCrearNotificacion(
							"orden_agente_ia",
							"Orden desde Agente IA",
							"La orden #".$venta["codigo"]." fue creada desde el Agente IA.",
							"venta",
							$venta["id"]
						);
					} 

				}

            }

        }

    }



}