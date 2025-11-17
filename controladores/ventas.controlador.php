<?php

//date_default_timezone_set('America/Bogota');

class ControladorVentas{

	/*=============================================
	MOSTRAR VENTAS
	=============================================*/

	static public function ctrMostrarVentas($item, $valor){

		$tabla = "ventas";

		$respuesta = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);

		return $respuesta;
	}


	/*=============================================
	CREAR VENTAS
	=============================================*/

	static public function ctrCrearVenta(){

		if(isset($_POST["nuevaVenta"])){




			//No permitir ejecutar la venta si no hay productos añadidos
			if($_POST["listaProductos"] == ""){

					echo'<script>

				swal({
					  type: "error",
					  title: "La venta no se puede ejecutar si no hay productos",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
								if (result.value) {

								window.location = "ventas";

								}
							})

				</script>';

				return;
			}

			/*=============================================
			ACTUALIZAR LAS COMPRAS DEL CLIENTE Y REDUCIR EL STOCK Y AUMENTAR LAS VENTAS DE LOS PRODUCTOS
			=============================================*/

			$listaProductos = json_decode($_POST["listaProductos"], true);

			// 🔹 Usar el código que viene del formulario (ya calculado en la vista)
			$codigoVenta = $_POST["nuevaVenta"];
			$tabla = "ventas";

			// DEBUG: Ver qué productos llegan al crear orden/venta

			file_put_contents("debug_crear_orden.txt",

				"=== CREAR ORDEN/VENTA ===\n" .

				"Estado: " . $_POST["estado"] . "\n" .

				"Codigo Venta: " . $codigoVenta . "\n" .

				"Lista Productos RAW: " . $_POST["listaProductos"] . "\n" .

				"Lista Productos DECODED:\n" . print_r($listaProductos, true) . "\n",

				FILE_APPEND

			);

			//exit;

			$totalProductosComprados = array();


			if ($_POST["estado"] == "venta") {
								
					foreach ($listaProductos as $key => $value) { 

						array_push($totalProductosComprados, $value["cantidad"]); 

						// Verificar si es una variante
						if(isset($value["esVariante"]) && $value["esVariante"] == "1"){

							// Es una variante - restar stock de productos_variantes
							$tablaVariantes = "productos_variantes";
							$idVariante = $value["idVariante"]; 

							// Obtener datos actuales de la variante
							$traerVariante = ModeloProductos::mdlObtenerVariantePorId($idVariante);

							// Actualizar stock de la variante
							$nuevoStockVariante = $traerVariante["stock"] - $value["cantidad"];
							ModeloProductos::mdlActualizarStockVariante($tablaVariantes, $nuevoStockVariante, $idVariante);

							// 🟢 REGISTRAR MOVIMIENTO DE STOCK - VARIANTE
							ControladorMovimientos::ctrRegistrarMovimiento(
								"variante",
								$value["id"],
								$idVariante,
								$value["descripcion"],
								"venta",
								-$value["cantidad"],
								$traerVariante["stock"],
								$nuevoStockVariante,
								"Venta #".$codigoVenta,
								""
							);

							// Actualizar también el stock del producto base
							$tablaProductos = "productos";
							$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $value["id"], "id");
							$nuevoStockBase = $traerProducto["stock"] - $value["cantidad"];
							ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStockBase, $value["id"]);

							// 🟢 REGISTRAR MOVIMIENTO DE STOCK - PRODUCTO BASE
							ControladorMovimientos::ctrRegistrarMovimiento(
								"producto",
								$value["id"],
								null,
								$traerProducto["descripcion"],
								"venta",
								-$value["cantidad"],
								$traerProducto["stock"],
								$nuevoStockBase,
								"Venta #".$codigoVenta." (por variante)",
								""
							);

							// Actualizar ventas del producto base (estadística)
							$nuevasVentas = $value["cantidad"] + $traerProducto["ventas"];
							ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, $value["id"]); 

						} else {
							// Es un producto normal - restar stock de productos
							$tablaProductos = "productos";
							$item = "id";
							$valor = $value["id"];
							$orden = "id";

							$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, $item, $valor, $orden);

 							$item1a = "ventas";
							$valor1a = $value["cantidad"] + $traerProducto["ventas"]; 

							$nuevasVentas = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1a, $valor1a, $valor);

							$item1b = "stock";
							$valor1b = $value["stock"]; 

							$nuevoStock = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1b, $valor1b, $valor);

							// 🟢 REGISTRAR MOVIMIENTO DE STOCK - PRODUCTO NORMAL
							ControladorMovimientos::ctrRegistrarMovimiento(
								"producto",
								$value["id"],
								null,
								$traerProducto["descripcion"],
								"venta",
								-$value["cantidad"],
								$traerProducto["stock"],
								$valor1b,
								"Venta #".$codigoVenta,
								""
							);

						}
					}

			} //CIERRE IF ESTADO VENTA
						
			$tablaClientes = "clientes";

			$item = "id";
			$valor = $_POST["seleccionarCliente"];

			$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes, $item, $valor);

			$item1a = "compras";
			$valor1a = array_sum($totalProductosComprados) + $traerCliente["compras"];

			$comprasCliente = ModeloClientes::mdlActualizarCliente($tablaClientes, $item1a, $valor1a, $valor);

			$item1b = "ultima_compra";

			date_default_timezone_set('America/Bogota');

			$fecha = date('Y-m-d');
			$hora = date('H:i:s');
			$valor1b = $fecha.' '.$hora;

			$comprasCliente = ModeloClientes::mdlActualizarCliente($tablaClientes, $item1b, $valor1b, $valor);


			/*=============================================
			GUARDAR LA COMPRA
			=============================================*/

			// Ya se generó $codigoVenta y $tabla al inicio (antes del bucle de productos)

			date_default_timezone_set('America/Bogota');
			$fechaHoraActual = date('Y-m-d H:i:s');

			$datos = array("id_vendedor"=>$_POST["idVendedor"],
						   "id_cliente"=>$_POST["seleccionarCliente"],
						   //"codigo"=>$_POST["nuevaVenta"],
						    "codigo" => $codigoVenta,
						   "productos"=>$_POST["listaProductos"],
						   "impuesto"=>$_POST["nuevoPrecioImpuesto"],
						   "neto"=>$_POST["nuevoPrecioNeto"],
						   "total"=>$_POST["totalVenta"],
						   "notas" => $_POST["notas"],
						   "estado" => $_POST["estado"],
						   "imagen" => $_POST["nuevaimagen"],
						   "fecha" => $fechaHoraActual,
						   "metodo_pago"=>$_POST["listaMetodoPago"],
						   "tipo_descuento" => isset($_POST["tipoDescuento"]) ? $_POST["tipoDescuento"] : "",
						    "valor_descuento" => isset($_POST["valorDescuento"]) ? $_POST["valorDescuento"] : 0,
               				"monto_descuento" => isset($_POST["montoDescuento"]) ? $_POST["montoDescuento"] : 0,
               				"recibe" => isset($_POST["recibe"]) ? $_POST["recibe"] : null,
							"extra" => null);

			$respuesta = ModeloVentas::mdlIngresarVenta($tabla, $datos);

			if ($respuesta == "ok") {

				// 🔹 ACTUALIZAR EL CONSECUTIVO en la BD ahora que la venta se guardó correctamente
				ModeloVentas::mdlActualizarConsecutivo($tabla, $codigoVenta);

				// Verificar stock y generar notificaciones si es necesario
				ControladorNotificaciones::ctrVerificarStockProductos();

				// Verificar si la orden proviene de Agente IA (campo extra contiene n8n)
				if ($_POST["estado"] == "orden") {
					ControladorNotificaciones::ctrVerificarOrdenAgenteIA($codigoVenta);
				}	


				if ($_POST["estado"] == "orden") {
			    		echo '<script>
						localStorage.removeItem("rango");
						swal({
							type: "success",
							title: "¡La orden ha sido guardada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){
							if (result.value) {
								window.location = "ordenes";
							}
						});
					</script>';
				}

					else {
						echo '<script>
						localStorage.removeItem("rango");
						swal({
							type: "success",
							title: "!La venta ha sigo guardada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							}).then((result)=>{
								if(result.value){

								window.location = "ventas";
								}
							})
						</script>';
		         	}
			}
				 
		}

	}



/*=============================================
EDITAR VENTAS
=============================================*/

static public function ctrEditarVenta(){

	if(isset($_POST["editarVenta"])){

		//No permitir ejecutar la venta si no hay productos añadidos
		if($_POST["listaProductos"] == ""){

			echo'<script>
				swal({
					  type: "error",
					  title: "Debe modificar los productos para guardar la  venta",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar"
					  }).then(function(result){
								if (result.value) {
									window.location = "ordenes";
								}
							})
				</script>';
			return;
		}

		$tabla = "ventas";
		$item = "codigo";
		$valor = $_POST["editarVenta"];

		$traerVenta = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);

		/*=============================================
		SI ERA ORDEN Y PASA A VENTA
		=============================================*/
		if($traerVenta["estado"] == "orden" && $_POST["estado"] == "venta"){

			$listaProductos = json_decode($_POST["listaProductos"], true);
			$totalProductosComprados = array();

			// DEBUG: Ver qué datos están llegando

			file_put_contents("debug_orden_a_venta.txt", "=== DATOS RECIBIDOS ===\n" . print_r($listaProductos, true)); 

			foreach ($listaProductos as $key => $value) {

				array_push($totalProductosComprados, $value["cantidad"]); 

				// DEBUG: Ver qué campos tiene cada producto
				file_put_contents("debug_orden_a_venta.txt",
					"\n=== PRODUCTO $key ===\n" .
					"ID: " . (isset($value["id"]) ? $value["id"] : "NO EXISTE") . "\n" .
					"esVariante: " . (isset($value["esVariante"]) ? $value["esVariante"] : "NO EXISTE") . "\n" .
					"idVariante: " . (isset($value["idVariante"]) ? $value["idVariante"] : "NO EXISTE") . "\n" .
					"Descripción: " . $value["descripcion"] . "\n",

					FILE_APPEND
				); 

				// Verificar si es una variante
				if(isset($value["esVariante"]) && $value["esVariante"] == "1"){ 

					file_put_contents("debug_orden_a_venta.txt", ">>> ES VARIANTE - Procesando...\n", FILE_APPEND);

					// Es una variante - descontar stock de productos_variantes
					$tablaVariantes = "productos_variantes";
					$idVariante = $value["idVariante"]; 

					file_put_contents("debug_orden_a_venta.txt", "ID Variante: $idVariante\n", FILE_APPEND);

 

					// Obtener datos actuales de la variante

					$traerVariante = ModeloProductos::mdlObtenerVariantePorId($idVariante);

 

					file_put_contents("debug_orden_a_venta.txt",

						"Stock actual variante: " . $traerVariante["stock"] . "\n" .

						"Cantidad a descontar: " . $value["cantidad"] . "\n",

						FILE_APPEND

					);

 
					// Actualizar stock de la variante
					$nuevoStockVariante = $traerVariante["stock"] - $value["cantidad"];

					file_put_contents("debug_orden_a_venta.txt", "Nuevo stock: $nuevoStockVariante\n", FILE_APPEND);

					$resultadoActualizacion = ModeloProductos::mdlActualizarStockVariante($tablaVariantes, $nuevoStockVariante, $idVariante);

					file_put_contents("debug_orden_a_venta.txt", "Resultado actualización: $resultadoActualizacion\n\n", FILE_APPEND);

					// 🟢 REGISTRAR MOVIMIENTO DE STOCK - VARIANTE (ORDEN → VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"variante",
						$value["id"],
						$idVariante,
						$value["descripcion"],
						"venta",
						-$value["cantidad"],
						$traerVariante["stock"],
						$nuevoStockVariante,
						"Venta #".$_POST["editarVenta"]." (orden convertida a venta)",
						""
					);

					// Actualizar también el stock del producto base
					$tablaProductos = "productos";
					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $value["id"], "id");
					$nuevoStockBase = $traerProducto["stock"] - $value["cantidad"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStockBase, $value["id"]);

					// 🟢 REGISTRAR MOVIMIENTO DE STOCK - PRODUCTO BASE (ORDEN → VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"producto",
						$value["id"],
						null,
						$traerProducto["descripcion"],
						"venta",
						-$value["cantidad"],
						$traerProducto["stock"],
						$nuevoStockBase,
						"Venta #".$_POST["editarVenta"]." (por variante - orden convertida)",
						""
					);

					// Actualizar ventas del producto base (estadística)
					$nuevasVentas = $value["cantidad"] + $traerProducto["ventas"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, $value["id"]);
					 

				} else {
					// Es un producto normal - descontar stock de productos
					$tablaProductos = "productos";
					$itemProd = "id";
					$valorProd = $value["id"];
					$orden = "id";

 					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, $itemProd, $valorProd, $orden);

					// Aumentar ventas
					$item1a = "ventas";
					$valor1a = $value["cantidad"] + $traerProducto["ventas"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, $item1a, $valor1a, $valorProd);

 					// Disminuir stock
					$item1b = "stock";
					$valor1b = $traerProducto["stock"] - $value["cantidad"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, $item1b, $valor1b, $valorProd);

					// 🟢 REGISTRAR MOVIMIENTO DE STOCK - PRODUCTO NORMAL (ORDEN → VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"producto",
						$value["id"],
						null,
						$traerProducto["descripcion"],
						"venta",
						-$value["cantidad"],
						$traerProducto["stock"],
						$valor1b,
						"Venta #".$_POST["editarVenta"]." (orden convertida a venta)",
						""
					);
				}
			}

			// Actualizar cliente
			$tablaClientes = "clientes";
			$itemCliente = "id";
			$valorCliente = $_POST["seleccionarCliente"];
			$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes, $itemCliente, $valorCliente);

			$item1a = "compras";
			$valor1a = array_sum($totalProductosComprados) + $traerCliente["compras"];
			ModeloClientes::mdlActualizarCliente($tablaClientes, $item1a, $valor1a, $valorCliente);

			$item1b = "ultima_compra";
			date_default_timezone_set('America/Bogota');
			$fecha = date('Y-m-d');
			$hora = date('H:i:s');
			$valor1b = $fecha.' '.$hora;
			ModeloClientes::mdlActualizarCliente($tablaClientes, $item1b, $valor1b, $valorCliente);
		}

		/*=============================================
		SI YA ERA VENTA Y SE EDITA
		=============================================*/
		if($traerVenta["estado"] == "venta" && $_POST["estado"] == "venta"){

			$productos = json_decode($traerVenta["productos"], true);
			$totalProductosComprados = array();

			// Revertir cantidades viejas
			foreach ($productos as $key => $value) {
				array_push($totalProductosComprados, $value["cantidad"]);
				
				// Verificar si es una variante
				if(isset($value["esVariante"]) && $value["esVariante"] == "1"){ 

					// Es una variante - devolver stock a la variante
					$tablaVariantes = "productos_variantes";
					$idVariante = $value["idVariante"]; 

					$traerVariante = ModeloProductos::mdlObtenerVariantePorId($idVariante); 

					// Devolver stock a la variante
					$nuevoStockVariante = $traerVariante["stock"] + $value["cantidad"];

					ModeloProductos::mdlActualizarStockVariante($tablaVariantes, $nuevoStockVariante, $idVariante);

					// 🟢 REGISTRAR MOVIMIENTO - REVERTIR VARIANTE (EDICIÓN VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"variante",
						$value["id"],
						$idVariante,
						$value["descripcion"],
						"edicion_stock",
						$value["cantidad"],
						$traerVariante["stock"],
						$nuevoStockVariante,
						"Edición de Venta #".$_POST["editarVenta"]." (revertir productos viejos)",
						"Devolución de stock por edición de venta"
					);

					// Revertir ventas del producto base
					$tablaProductos = "productos";
					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $value["id"], "id");
					$nuevasVentas = $traerProducto["ventas"] - $value["cantidad"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, $value["id"]);

				} else {
					// Es un producto normal - devolver stock al producto
					$tablaProductos = "productos";
					$item = "id";
					$valor = $value["id"];
					$orden = "id"; 

					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, $item, $valor, $orden); 

					$item1a = "ventas";
					$valor1a = $traerProducto["ventas"] - $value["cantidad"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, $item1a, $valor1a, $valor); 

					$item1b = "stock";
					$valor1b = $value["cantidad"] + $traerProducto["stock"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, $item1b, $valor1b, $valor);

					// 🟢 REGISTRAR MOVIMIENTO - REVERTIR PRODUCTO NORMAL (EDICIÓN VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"producto",
						$value["id"],
						null,
						$traerProducto["descripcion"],
						"edicion_stock",
						$value["cantidad"],
						$traerProducto["stock"],
						$valor1b,
						"Edición de Venta #".$_POST["editarVenta"]." (revertir productos viejos)",
						"Devolución de stock por edición de venta"
					);

				}

			} 

			// Revertir compras cliente
			$tablaClientes = "clientes";
			$itemCliente = "id";
			$valorCliente = $_POST["seleccionarCliente"];

			$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes, $itemCliente, $valorCliente);

			$item1a = "compras";
			$valor1a = $traerCliente["compras"] - array_sum($totalProductosComprados);

			ModeloClientes::mdlActualizarCliente($tablaClientes, $item1a, $valor1a, $valorCliente); 

			// Aplicar nuevas cantidades
			$listaProductos_2 = json_decode($_POST["listaProductos"], true);
			$totalProductosComprados_2 = array();

 			foreach ($listaProductos_2 as $key => $value) {
				array_push($totalProductosComprados_2, $value["cantidad"]); 

				// Verificar si es una variante
				if(isset($value["esVariante"]) && $value["esVariante"] == "1"){ 

					// Es una variante - descontar stock de la variante
					$tablaVariantes = "productos_variantes";
					$idVariante = $value["idVariante"];

					$traerVariante = ModeloProductos::mdlObtenerVariantePorId($idVariante); 

					// Descontar stock de la variante
					$nuevoStockVariante = $traerVariante["stock"] - $value["cantidad"];

					ModeloProductos::mdlActualizarStockVariante($tablaVariantes, $nuevoStockVariante, $idVariante);

					// 🟢 REGISTRAR MOVIMIENTO - APLICAR VARIANTE (EDICIÓN VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"variante",
						$value["id"],
						$idVariante,
						$value["descripcion"],
						"edicion_stock",
						-$value["cantidad"],
						$traerVariante["stock"],
						$nuevoStockVariante,
						"Edición de Venta #".$_POST["editarVenta"]." (aplicar productos nuevos)",
						"Descuento de stock por edición de venta"
					);

 					// Aumentar ventas del producto base
					$tablaProductos = "productos";
					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $value["id"], "id");
					$nuevasVentas = $value["cantidad"] + $traerProducto["ventas"];
					ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, $value["id"]);

				} else {
					// Es un producto normal - descontar stock del producto
					$tablaProductos_2 = "productos";
					$item_2 = "id";
					$valor_2 = $value["id"];
					$orden = "id"; 

					$traerProducto_2 = ModeloProductos::mdlMostrarProductos($tablaProductos_2, $item_2, $valor_2, $orden); 

					$item1a_2 = "ventas";
					$valor1a_2 = $value["cantidad"] + $traerProducto_2["ventas"];

					ModeloProductos::mdlActualizarProducto($tablaProductos_2, $item1a_2, $valor1a_2, $valor_2);
 
					$item1b_2 = "stock";
					$valor1b_2 = $traerProducto_2["stock"] - $value["cantidad"];

					ModeloProductos::mdlActualizarProducto($tablaProductos_2, $item1b_2, $valor1b_2, $valor_2);

					// 🟢 REGISTRAR MOVIMIENTO - APLICAR PRODUCTO NORMAL (EDICIÓN VENTA)
					ControladorMovimientos::ctrRegistrarMovimiento(
						"producto",
						$value["id"],
						null,
						$traerProducto_2["descripcion"],
						"edicion_stock",
						-$value["cantidad"],
						$traerProducto_2["stock"],
						$valor1b_2,
						"Edición de Venta #".$_POST["editarVenta"]." (aplicar productos nuevos)",
						"Descuento de stock por edición de venta"
					);
				}
			}

			// Actualizar cliente
			$tablaClientes_2 = "clientes";
			$item_2 = "id";
			$valor_2 = $_POST["seleccionarCliente"];
			$traerCliente_2 = ModeloClientes::mdlMostrarClientes($tablaClientes_2, $item_2, $valor_2);

			$item1a_2 = "compras";
			$valor1a_2 = array_sum($totalProductosComprados_2) + $traerCliente_2["compras"];
			ModeloClientes::mdlActualizarCliente($tablaClientes_2, $item1a_2, $valor1a_2, $valor_2);

			$item1b_2 = "ultima_compra";
			date_default_timezone_set('America/Bogota');
			$fecha = date('Y-m-d');
			$hora = date('H:i:s');
			$valor1b_2 = $fecha.' '.$hora;
			ModeloClientes::mdlActualizarCliente($tablaClientes_2, $item1b_2, $valor1b_2, $valor_2);
		}

		/*=============================================
		GUARDAR CAMBIOS DE LA COMPRA
		=============================================*/
		date_default_timezone_set('America/Bogota');
		$fechaHoraActual = date('Y-m-d H:i:s');

		$datos = array(
			"id_vendedor"=>$_POST["idVendedor"],
			"id_cliente"=>$_POST["seleccionarCliente"],
			"codigo"=>$_POST["editarVenta"],
			"productos"=>$_POST["listaProductos"],
			"impuesto"=>$_POST["nuevoPrecioImpuesto"],
			"neto"=>$_POST["nuevoPrecioNeto"],
			"total"=>$_POST["totalVenta"],
			"notas" => $_POST["notas"],
			"imagen" => $_POST["nuevaimagen"],
			"estado" => $_POST["estado"],
			"fecha" => $fechaHoraActual,
			"metodo_pago"=>$_POST["listaMetodoPago"],
        	"recibe" => isset($_POST["recibe"]) ? $_POST["recibe"] : null,
			"extra" => null
    	);

		$respuesta = ModeloVentas::mdlEditarVenta($tabla, $datos);

		/*if ($respuesta == "ok") {
			echo '<script>
				localStorage.removeItem("rango");
				swal({
					type: "success",
					title: "!La venta ha sigo CREADA correctamente!",
					showConfirmButton: true,
					confirmButtonText: "Cerrar",
					}).then((result)=>{
						if(result.value){
							window.location = "ordenes";
						}
					})
			</script>';
		}
		*/

			if ($respuesta == "ok") {

				// Verificar stock y generar notificaciones si es necesario
				ControladorNotificaciones::ctrVerificarStockProductos();

					//**************************************************
					// ENVIAR WEBHOOK A N8N
					//************************************************ */
				      // Obtener datos completos del cliente
							$tablaClientes = "clientes";
							$itemCliente = "id";
							$valorCliente = $_POST["seleccionarCliente"];
							$clienteCompleto = ModeloClientes::mdlMostrarClientes($tablaClientes, $itemCliente, $valorCliente);							
							// Preparar datos para el webhook
							$datosWebhook = array(
								"origen" => "ventas",								
								"id_vendedor" => $_POST["idVendedor"],								
								// Datos del cliente
								"cliente" => array(
									"id" => $clienteCompleto["id"],
									"nombre" => $clienteCompleto["nombre"],
									"documento" => $clienteCompleto["documento"],
									"email" => $clienteCompleto["email"],
									"telefono" => $clienteCompleto["telefono"],
									"departamento" => $clienteCompleto["departamento"],
									"ciudad" => $clienteCompleto["ciudad"],
									"direccion" => $clienteCompleto["direccion"],
									"estatus" => $clienteCompleto["estatus"],
									"fecha_nacimiento" => $clienteCompleto["fecha_nacimiento"],
									"notas" => $clienteCompleto["notas"]
								),								
								// Datos de la venta
								"codigo" => $_POST["editarVenta"],
								"productos" => json_decode($_POST["listaProductos"], true),
								"impuesto" => $_POST["nuevoPrecioImpuesto"],
								"neto" => $_POST["nuevoPrecioNeto"],
								"total" => $_POST["totalVenta"],
								"metodo_pago" => $_POST["listaMetodoPago"],
								"notas_venta" => $_POST["notas"],
								"imagen" => $_POST["nuevaimagen"],
								"estado" => $_POST["estado"],
								"fecha" => $fechaHoraActual
							);							
							// Enviar webhook con cURL
							$ch = curl_init('https://dd99f8f867ae.ngrok-free.app/webhook/mipos');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosWebhook));
							curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
							curl_setopt($ch, CURLOPT_TIMEOUT, 10);							
							$resultado = curl_exec($ch);
							$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
							curl_close($ch);							
							// Log del resultado (opcional)
							if($httpCode != 200) {
								error_log("Error al enviar webhook: HTTP " . $httpCode . " - " . $resultado);
							} else {
								error_log("Webhook enviado exitosamente para venta: " . $_POST["editarVenta"]);
							}



			echo '<script>
				localStorage.removeItem("rango");
				swal({
					type: "success",
					title: "!La venta ha sigo editada correctamente!",
					showConfirmButton: true,
					confirmButtonText: "Cerrar",
					}).then((result)=>{
						if(result.value){
							window.location = "ordenes";
						}
					})
			</script>';
		}


	}
}


/*=============================================
ELIMINAR VENTA
=============================================*/

static public function ctrEliminarVenta(){

	if(isset($_GET["idVenta"])){

		$tabla = "ventas";

		$item = "id";
		$valor = $_GET["idVenta"];

		$traerVenta = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);

		/*=============================================
		ACTUALIZAR FECHA ULTIMA COMPRA
		=============================================*/

		$tablaClientes = "clientes";
		
		$itemVentas = null;
		$valorVentas = null;

		$traerVentas = ModeloVentas::mdlMostrarVentas($tabla, $itemVentas, $valorVentas);

		$guardarFechas = array();

		foreach ($traerVentas as $key => $value) {
			
			if($value["id_cliente"] == $traerVenta["id_cliente"]){

				array_push($guardarFechas, $value["fecha"]);
			}

		}

		if(count($guardarFechas) > 1){

			if($traerVenta["fecha"] > $guardarFechas[count($guardarFechas)-2]){

				$item = "ultima_compra";
				$valor = $guardarFechas[count($guardarFechas)-2];
				$valorIdCliente = $traerVenta["id_cliente"];

				$comprasCliente = ModeloClientes::mdlActualizarCliente($tablaClientes, $item, $valor, $valorIdCliente);
			}
			else{
				$item = "ultima_compra";
				$valor = $guardarFechas[count($guardarFechas)-1];
				$valorIdCliente = $traerVenta["id_cliente"];

				$comprasCliente = ModeloClientes::mdlActualizarCliente($tablaClientes, $item, $valor, $valorIdCliente);
			}

		}
		else{

			$item = "ultima_compra";
			$valor = "0000-00-00 00:00:00";
			$valorIdCliente = $traerVenta["id_cliente"];

			$comprasCliente = ModeloClientes::mdlActualizarCliente($tablaClientes, $item, $valor, $valorIdCliente);
		}


		/*=============================================
		FORMATEAR LA TABLA DE PRODUCTOS Y CLIENTES
		=============================================*/

		$productos = json_decode($traerVenta["productos"], true);

			$totalProductosComprados = array();

			foreach ($productos as $key => $value) {

				array_push($totalProductosComprados, $value["cantidad"]);
				
				// Verificar si es una variante
				if(isset($value["esVariante"]) && $value["esVariante"] == "1"){

					// Es una variante - devolver stock a la variante Y al producto base
					$tablaVariantes = "productos_variantes";
					$idVariante = $value["idVariante"];

					// Obtener datos actuales de la variante
					$traerVariante = ModeloProductos::mdlObtenerVariantePorId($idVariante);

					// Devolver stock a la variante
					$nuevoStockVariante = $traerVariante["stock"] + $value["cantidad"];
					ModeloProductos::mdlActualizarStockVariante($tablaVariantes, $nuevoStockVariante, $idVariante);

					// 🟢 REGISTRAR MOVIMIENTO - DEVOLUCIÓN VARIANTE
					ControladorMovimientos::ctrRegistrarMovimiento(
						"variante",
						$value["id"],
						$idVariante,
						$value["descripcion"],
						"eliminacion_venta",
						$value["cantidad"],
						$traerVariante["stock"],
						$nuevoStockVariante,
						"Eliminación Venta #".$_GET["idVenta"],
						""
					);

					// Devolver stock al producto base
					$tablaProductos = "productos";
					$item = "id";
					$valor = $value["id"];
					$orden = "id";

					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, $item, $valor, $orden);

					// Devolver stock al producto base
					$item1b = "stock";
					$valor1b = $value["cantidad"] + $traerProducto["stock"];
					$nuevoStock = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1b, $valor1b, $valor);

					// 🟢 REGISTRAR MOVIMIENTO - DEVOLUCIÓN PRODUCTO BASE
					ControladorMovimientos::ctrRegistrarMovimiento(
						"producto",
						$value["id"],
						null,
						$traerProducto["descripcion"],
						"eliminacion_venta",
						$value["cantidad"],
						$traerProducto["stock"],
						$valor1b,
						"Eliminación Venta #".$_GET["idVenta"]." (por variante)",
						""
					);

					// Restar las ventas del producto base
					$item1a = "ventas";
					$valor1a = $traerProducto["ventas"] - $value["cantidad"];
					$nuevasVentas = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1a, $valor1a, $valor);

				} else {

					// Es un producto normal - devolver stock normal
					$tablaProductos = "productos";

					$item = "id";
					$valor = $value["id"];
					$orden = "id";

					$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, $item, $valor, $orden);

					$item1a = "ventas";
					$valor1a = $traerProducto["ventas"] - $value["cantidad"];

					$nuevasVentas = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1a, $valor1a, $valor);

					$item1b = "stock";
					$valor1b = $value["cantidad"] + $traerProducto["stock"];

					$nuevoStock = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1b, $valor1b, $valor);

					// 🟢 REGISTRAR MOVIMIENTO - DEVOLUCIÓN PRODUCTO NORMAL
					ControladorMovimientos::ctrRegistrarMovimiento(
						"producto",
						$value["id"],
						null,
						$traerProducto["descripcion"],
						"eliminacion_venta",
						$value["cantidad"],
						$traerProducto["stock"],
						$valor1b,
						"Eliminación Venta #".$_GET["idVenta"],
						""
					);
					
				}
			}

			$tablaClientes = "clientes";

			$itemCliente = "id";
			$valorCliente = $traerVenta["id_cliente"];

			$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes, $itemCliente, $valorCliente);

			$item1a = "compras";
			$valor1a = $traerCliente["compras"] - array_sum($totalProductosComprados);

			$comprasCliente = ModeloClientes::mdlActualizarCliente($tablaClientes, $item1a, $valor1a, $valorCliente);


			/*=============================================
			ELIMINAR VENTA
			=============================================*/
			
			$respuesta = ModeloVentas::mdlEliminarVenta($tabla, $_GET["idVenta"]);

			if ($respuesta == "ok") {


				if (isset($_GET["estado"]) && $_GET["estado"] == "orden") {
					echo '<script>
						localStorage.removeItem("rango");
						swal({
							type: "success",
							title: "¡La orden ha sido eliminada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){
							if (result.value) {
								window.location = "ordenes";
							}
						});
					</script>';
				}

				else {

			    	echo '<script>
						localStorage.removeItem("rango");
						swal({
							type: "success",
							title: "!La venta ha sigo borrada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then((result)=>{
								if(result.value){
								window.location = "ventas";
								}
							})
			     	</script>';
				}
					


		    }

	}

}


	/*=============================================
	RANGO FECHAS
	=============================================*/	
	
	static public function ctrRangoFechasVentas($fechaInicial, $fechaFinal){

		$tabla = "ventas";

		$respuesta = ModeloVentas::mdlRangoFechasVentas($tabla, $fechaInicial, $fechaFinal);

		return $respuesta;		
	}
	



	/*=============================================
	DESCARGAR EXCEL
	=============================================*/

	public function ctrDescargarReporte(){

		if(isset($_GET["reporte"])){

			$tabla = "ventas";

			if(isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])){

				$ventas = ModeloVentas::mdlRangoFechasVentas($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"]);

			}else{

				$item = null;
				$valor = null;

				$ventas = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);
			}

			/*=============================================
			CREAMOS EL ARCHIVO DE EXCEL
			=============================================*/
			$Name = $_GET["reporte"].'.xls';

			header('Expires: 0');
			header('Cache-control: private');
			header("Content-type: application/vnd.ms-excel"); // Archivo de Excel
			header("Cache-Control: cache, must-revalidate"); 
			header('Content-Description: File Transfer');
			header('Last-Modified: '.date('D, d M Y H:i:s'));
			header("Pragma: public"); 
			header('Content-Disposition:; filename="'.$Name.'"');
			header("Content-Transfer-Encoding: binary");
		
			echo utf8_decode("<table border='0'> 

				<tr> 
				<td style='font-weight:bold; border:1px solid #eee;'>CÓDIGO</td> 
				<td style='font-weight:bold; border:1px solid #eee;'>CLIENTE</td>
				<td style='font-weight:bold; border:1px solid #eee;'>VENDEDOR</td>
				<td style='font-weight:bold; border:1px solid #eee;'>CANTIDAD</td>
				<td style='font-weight:bold; border:1px solid #eee;'>PRODUCTOS</td>
				<td style='font-weight:bold; border:1px solid #eee;'>IMPUESTO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>NETO</td>		
				<td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td>		
				<td style='font-weight:bold; border:1px solid #eee;'>METODO DE PAGO</td	
				<td style='font-weight:bold; border:1px solid #eee;'>FECHA</td>		
				</tr>");

				foreach ($ventas as $row => $item){

					// Filtrar solo ventas con estado = 'venta'
						if (!isset($item["estado"]) || $item["estado"] != "venta") {
							continue;
						}

					$cliente = ControladorClientes::ctrMostrarClientes("id", $item["id_cliente"]);
					$vendedor = ControladorUsuarios::ctrMostrarUsuarios("id", $item["id_vendedor"]);

					echo utf8_decode("<tr>
				 			<td style='border:1px solid #eee;'>".$item["codigo"]."</td> 
				 			<td style='border:1px solid #eee;'>".$cliente["nombre"]."</td>
				 			<td style='border:1px solid #eee;'>".$vendedor["nombre"]."</td>
				 			<td style='border:1px solid #eee;'>");

				 	$productos =  json_decode($item["productos"], true);

				 	foreach ($productos as $key => $valueProductos) {
				 			
				 			echo utf8_decode($valueProductos["cantidad"]."<br>");
				 		}

				 	echo utf8_decode("</td><td style='border:1px solid #eee;'>");	

			 		foreach ($productos as $key => $valueProductos) {
				 			
			 			echo utf8_decode($valueProductos["descripcion"]."<br>");			 		
			 		}

			 		echo utf8_decode("</td>
					<td style='border:1px solid #eee;'>$ ".number_format($item["impuesto"],2)."</td>
					<td style='border:1px solid #eee;'>$ ".number_format($item["neto"],2)."</td>	
					<td style='border:1px solid #eee;'>$ ".number_format($item["total"],2)."</td>
					<td style='border:1px solid #eee;'>".$item["metodo_pago"]."</td>
					<td style='border:1px solid #eee;'>".substr($item["fecha"],0,10)."</td>		
		 			</tr>");
			 	
			 	}

			echo "</table>";
			
		}

	}


	/*=============================================
	SUMA TOTAL VENTAS
	=============================================*/

	static public function ctrSumaTotalVentas(){

		$tabla = "ventas";

		$respuesta = ModeloVentas::mdlSumaTotalVentas($tabla);

		return $respuesta;

	}


	/*=============================================
	DESCARGAR XML
	=============================================*/
	static public function ctrDescargarXML(){
		//http://php.net/manual/es/book.xmlwriter.php

		if(isset($_GET["xml"])){

			$tabla = "ventas";
			$item = "codigo";
			$valor = $_GET["xml"];

			$ventas = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);

			// PRODUCTOS
			$listaProductos = json_decode($ventas["productos"], true);

			// CLIENTE
			$tablaClientes = "clientes";
			$item = "id";
			$valor = $ventas["id_cliente"];
			$traerCliente = ModeloClientes::mdlMostrarClientes($tablaClientes, $item, $valor);

			// VENDEDOR
			$tablaVendedor = "usuarios";
			$item = "id";
			$valor = $ventas["id_vendedor"];
			$traerVendedor = ModeloUsuarios::mdlMostrarUsuarios($tablaVendedor, $item, $valor);


			$objetoXML = new XMLWriter();

			$objetoXML->openURI($_GET["xml"].".xml"); //Creación del archivo XML

			$objetoXML->setIndent(true); //recibe un valor booleano para establecer si los distintos niveles de nodos XML deben quedar indentados o no.

			$objetoXML->setIndentString("\t"); // carácter \t, que corresponde a una tabulación

			$objetoXML->startDocument('1.0', 'utf-8');// Inicio del documento


			/*$objetoXML->startElement("etiquetaPrincipal");// Inicio del nodo raíz

				 $objetoXML->writeAttribute("atributoEtiquetaPPal", "valor atributo etiqueta PPal"); // Atributo etiqueta principal

				$objetoXML->startElement("etiquetaInterna");// Inicio del nodo hijo

					$objetoXML->writeAttribute("atributoEtiquetaInterna", "valor atributo etiqueta Interna"); // Atributo etiqueta interna

					$objetoXML->text("Texto interno");

				$objetoXML->endElement(); // Final del nodo hijo
			
			$objetoXML->endElement(); // Final del nodo raíz */


			$objetoXML->writeRaw('<fe:Invoice xmlns:fe="http://www.dian.gov.co/contratos/facturaelectronica/v1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:clm54217="urn:un:unece:uncefact:codelist:specification:54217:2001" xmlns:clm66411="urn:un:unece:uncefact:codelist:specification:66411:2001" xmlns:clmIANAMIMEMediaType="urn:un:unece:uncefact:codelist:specification:IANAMIMEMediaType:2003" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sts="http://www.dian.gov.co/contratos/facturaelectronica/v1/Structures" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dian.gov.co/contratos/facturaelectronica/v1 ../xsd/DIAN_UBL.xsd urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2 ../../ubl2/common/UnqualifiedDataTypeSchemaModule-2.0.xsd urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2 ../../ubl2/common/UBL-QualifiedDatatypes-2.0.xsd">');

			$objetoXML->writeRaw('<ext:UBLExtensions>');


			foreach ($listaProductos as $key => $value) {
				
				$objetoXML->text($value["descripcion"].", ");
			}

			$objetoXML->text($ventas["codigo"]."--");

			$objetoXML->text($traerCliente["nombre"]." ");

			$objetoXML->text(number_format($ventas["impuesto"],2));


			$objetoXML->writeRaw('</ext:UBLExtensions>');

			$objetoXML->writeRaw('</fe:Invoice>');

			$objetoXML->endDocument(); // Final del documento

			return true;

		}
		
	}

	//Diferenciar entre venta y orden
	static public function ctrRangoFechasVentasPorEstado($fechaInicial, $fechaFinal, $estado){

		$tabla = "ventas";
	
		$respuesta = ModeloVentas::mdlRangoFechasVentasPorEstado($tabla, $fechaInicial, $fechaFinal, $estado);
	
		return $respuesta;
	}


	//Para los reportes
		public static function ctrMostrarVentasAsociativo($tabla, $item, $valor)
		{
			return ModeloVentas::mdlMostrarVentasAsociativo($tabla, $item, $valor);
		}

	
		//Guardar notas
	static public function ctrActualizarNotaVenta($datos) {
		return ModeloVentas::mdlActualizarNotaVenta("ventas", $datos);
	}


	
	/*=============================================
	EDITAR IMAGEN DE VENTA
	=============================================*/
	static public function ctrEditarImagenVenta($datos){

		$tabla = "ventas";
		$respuesta = ModeloVentas::mdlEditarImagenVenta($tabla, $datos);
		return $respuesta;

	}


}