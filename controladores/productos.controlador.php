<?php

class ControladorProductos{

	/*=============================================
	MOSTRAR PRODUCTOS
	=============================================*/

	static public function ctrMostrarProductos($item, $valor, $orden, $productosExistentes = []){

		$tabla = "productos";

		$respuesta = ModeloProductos::mdlMostrarProductos($tabla, $item, $valor, $orden, $productosExistentes);

		return $respuesta;		
	} 


	/*=============================================
	CREAR PRODUCTO
	=============================================*/

	static public function ctrCrearProducto(){

		if(isset($_POST["nuevaDescripcion"])){			

			// Verificar si el producto tiene variantes

			$tieneVariantes = isset($_POST["tieneVariantes"]) ? 1 : 0; 

			// Si tiene variantes, los campos de precio y stock pueden ser opcionales

			$validarStock = $tieneVariantes ? true : preg_match('/^[0-9]+$/', $_POST["nuevoStock"]);

			$validarPrecioCompra = $tieneVariantes ? true : preg_match('/^[0-9]+$/', $_POST["nuevoPrecioCompra"]);

			$validarPrecioVenta = $tieneVariantes ? true : preg_match('/^[0-9,.]+$/', $_POST["nuevoPrecioVenta"]);
 

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaDescripcion"]) &&

				$validarStock && $validarPrecioCompra && $validarPrecioVenta){
 

				/*=============================================
				VALIDAR QUE EL CÓDIGO NO EXISTA
				=============================================*/ 

				$tabla = "productos";
				$item = "codigo";
				$valor = $_POST["nuevoCodigo"]; 

				$codigoExistente = ModeloProductos::mdlMostrarProductos($tabla, $item, $valor, "id");

				if($codigoExistente){ 

					echo '<script> 

						swal({ 
							type: "error",
							title: "El código del producto ya existe",
							text: "Por favor ingrese un código diferente. El código '.$_POST["nuevoCodigo"].' ya está siendo utilizado.",
							showConfirmButton: true,
							confirmButtonText: "Cerrar" 

						}).then(function(result){
							if(result.value){
								window.location = "productos";
							}
						});
					</script>';
					return;
				}

			    /*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = "vistas/img/productos/default/anonymous.png"; 

				if(isset($_FILES["nuevaImagen"]["tmp_name"]) && !empty($_FILES["nuevaImagen"]["tmp_name"])){ 

					list($ancho, $alto) = getimagesize($_FILES["nuevaImagen"]["tmp_name"]); 

					$nuevoAncho = 500;

					$nuevoAlto = 500;

					$directorio = "vistas/img/productos/".$_POST["nuevoCodigo"];

					mkdir($directorio, 0755);

					if($_FILES["nuevaImagen"]["type"] == "image/jpeg"){

						$aleatorio = mt_rand(100, 999);

						$ruta = "vistas/img/productos/".$_POST["nuevoCodigo"]."/".$aleatorio.".jpeg";

						$origen = imagecreatefromjpeg($_FILES["nuevaImagen"]["tmp_name"]);

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagejpeg($destino, $ruta);

					}

					if($_FILES["nuevaImagen"]["type"] == "image/png"){

						$aleatorio = mt_rand(100, 999);

						$ruta = "vistas/img/productos/".$_POST["nuevoCodigo"]."/".$aleatorio.".png";

						$origen = imagecreatefrompng($_FILES["nuevaImagen"]["tmp_name"]);

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagepng($destino, $ruta);


					}

				 }

				$tabla = "productos";

				// Validar proveedor

				$idProveedor = $_POST["nuevoProveedor"];

				if(empty($idProveedor) || $idProveedor == "0" || $idProveedor == 0){

					$idProveedor = null;

				}


				// Valores por defecto si están vacíos (para productos con variantes)

				$stock = !empty($_POST["nuevoStock"]) ? $_POST["nuevoStock"] : 0;

				$precioCompra = !empty($_POST["nuevoPrecioCompra"]) ? $_POST["nuevoPrecioCompra"] : 0;

				$precioVenta = !empty($_POST["nuevoPrecioVenta"]) ? $_POST["nuevoPrecioVenta"] : 0;

				$datos = array(

					"id_categoria" => $_POST["nuevaCategoria"],

					"codigo" => $_POST["nuevoCodigo"],

					"descripcion" => $_POST["nuevaDescripcion"],

					"stock" => $stock,

					"precio_compra" => $precioCompra,

					"precio_venta" => $precioVenta,

					"id_proveedor" => $idProveedor,

					"imagen" => $ruta,

					"tiene_variantes" => $tieneVariantes
				);

				// Si NO tiene variantes, usar el método normal

				if(!$tieneVariantes){

					$respuesta = ModeloProductos::mdlIngresarProducto($tabla, $datos);

					if($respuesta == "ok"){
						// Obtener el ID del producto recién creado
						$productoCreado = ModeloProductos::mdlMostrarProductos($tabla, "codigo", $_POST["nuevoCodigo"], "id");
						$idProducto = $productoCreado["id"]; 

						// 🟢 REGISTRAR MOVIMIENTO DE STOCK - CREACIÓN DE PRODUCTO
						if($stock > 0){
							ControladorMovimientos::ctrRegistrarMovimiento(
								"producto",
								$idProducto,
								null,
								$_POST["nuevaDescripcion"],
								"creacion_producto",
								$stock,
								0,
								$stock,
								"Producto creado con stock inicial",
								""
							);
						}
					}

					/*=============================================
					CALCULAR STOCK AUTOMÁTICO DEL PRODUCTO BASE
					=============================================*/
					if(isset($_POST["totalCombinaciones"]) && $_POST["totalCombinaciones"] > 0){
						
						// Obtener el ID del producto recién creado
						$tablaProductos = "productos";
						$ultimoProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, "codigo", $_POST["nuevoCodigo"], "id");
						$idProducto = $ultimoProducto["id"];
						
						// Calcular la suma del stock de todas las variantes
						$stmt = Conexion::conectar()->prepare("SELECT SUM(stock) as stock_total FROM productos_variantes WHERE id_producto = :id_producto AND estado = 1");
						$stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
						$stmt->execute();
						$resultado = $stmt->fetch();
						$stmt = null;
						
						$stockTotal = $resultado["stock_total"] ? $resultado["stock_total"] : 0;
						
						// Actualizar el stock del producto base
						ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $stockTotal, $idProducto);
					}

					if($respuesta == "ok"){
						
						echo '<script>

						swal({

							type: "success",

							title: "!El producto ha sido guardado correctamente!",

							showConfirmButton: true,

							confirmButtonText: "Cerrar"

						}).then((result)=>{

							if(result.value){

								window.location = "productos";

							}

						})

						</script>';

					}

 

				} else {

					// SI tiene variantes, usar el nuevo método que retorna ID

					$idProducto = ModeloProductos::mdlIngresarProductoConVariantes($tabla, $datos);

					if($idProducto){

						// Procesar variantes

						$totalCombinaciones = isset($_POST["totalCombinaciones"]) ? $_POST["totalCombinaciones"] : 0;

						$variantesCreadas = 0;

						for($i = 0; $i < $totalCombinaciones; $i++){

							// Verificar si existe la combinación

							if(isset($_POST["combinacion_".$i."_ids"])){


								$idsCombinacion = $_POST["combinacion_".$i."_ids"];

								$nombreCombinacion = $_POST["combinacion_".$i."_nombre"];


								// Obtener precio adicional y stock de la variante

								$precioAdicional = isset($_POST["precioAdicional_".$idsCombinacion]) && $_POST["precioAdicional_".$idsCombinacion] !== ""

									? $_POST["precioAdicional_".$idsCombinacion]

									: 0;


								$stockVariante = isset($_POST["stockVariante_".$idsCombinacion]) && $_POST["stockVariante_".$idsCombinacion] !== ""

									? $_POST["stockVariante_".$idsCombinacion]

									: $stock;

 
								// Generar SKU

								$idsOpcionesArray = explode("_", $idsCombinacion);

								$sku = ModeloProductos::mdlGenerarSKU($_POST["nuevoCodigo"], $idsOpcionesArray);

 
								// Datos de la variante

								$datosVariante = array(

									"id_producto" => $idProducto,

									"sku" => $sku,

									"precio_adicional" => $precioAdicional,

									"stock" => $stockVariante,

									"imagen" => $ruta,

									"estado" => 1

								);

  							// Guardar variante

								$idVariante = ModeloProductos::mdlGuardarVariante($datosVariante);

								if($idVariante){

									// 🟢 REGISTRAR MOVIMIENTO DE STOCK - CREACIÓN DE VARIANTE

									if($stockVariante > 0){

										ControladorMovimientos::ctrRegistrarMovimiento(
											"variante",
											$idProducto,
											$idVariante,
											$_POST["nuevaDescripcion"] . " - " . $nombreCombinacion,
											"creacion_variante",
											$stockVariante,
											0,
											$stockVariante,
											"Variante creada con stock inicial: " . $nombreCombinacion,
											""
										);
									}


									// Relacionar variante con sus opciones

									foreach($idsOpcionesArray as $idOpcion){

										$datosRelacion = array(

											"id_producto_variante" => $idVariante,

											"id_opcion_variante" => $idOpcion

										);

										ModeloProductos::mdlGuardarVarianteOpcion($datosRelacion);

									}

									$variantesCreadas++;

								}

							}

						}

						/*=============================================
						CALCULAR STOCK AUTOMÁTICO DEL PRODUCTO BASE
						=============================================*/
						// Calcular la suma del stock de todas las variantes
						$stmt = Conexion::conectar()->prepare("SELECT SUM(stock) as stock_total FROM productos_variantes WHERE id_producto = :id_producto AND estado = 1");
						$stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
						$stmt->execute();
						$resultado = $stmt->fetch();
						$stmt = null;
						
						$stockTotal = $resultado["stock_total"] ? $resultado["stock_total"] : 0;
						
						// Actualizar el stock del producto base
						$tablaProductos = "productos";
						ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $stockTotal, $idProducto);

 
						// Mostrar mensaje de éxito
						echo '<script>

						swal({

							type: "success",

							title: "¡Producto guardado!",

							text: "Se crearon '.$variantesCreadas.' variantes correctamente",

							showConfirmButton: true,

							confirmButtonText: "Cerrar"

						}).then((result)=>{

							if(result.value){

								window.location = "productos";

							}

						})

						</script>';

 

					} else {

						echo '<script>

						swal({

							type: "error",

							title: "Error al guardar el producto",

							showConfirmButton: true,

							confirmButtonText: "Cerrar"

						}).then((result)=>{

							if(result.value){

								window.location = "productos";

							}

						})

						</script>';

					}

				}

 

			} else {

				echo '<script>

					swal({

						type: "error",

						title: "!El producto no puede ir con los campos vacíos o llevar caracteres especiales!",

						showConfirmButton: true,

						confirmButtonText: "Cerrar",

						closeOnConfirm: false

					}).then((result)=>{

						if(result.value){

							window.location = "productos";

						}

					})




				</script>';
			}

		}

	}


	/*==========================================================================================
	EDITAR PRODUCTO
	==========================================================================================*/

	static public function ctrEditarProducto(){

		if(isset($_POST["editarDescripcion"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarDescripcion"]) &&
				preg_match('/^[0-9]+$/', $_POST["editarStock"]) &&
				preg_match('/^[0-9]+$/', $_POST["editarPrecioCompra"]) &&
				preg_match('/^[0-9,.]+$/', $_POST["editarPrecioVenta"])){


			    /*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = $_POST["imagenActual"];

				if(isset($_FILES["editarImagen"]["tmp_name"]) && !empty($_FILES["editarImagen"]["tmp_name"])){

					list($ancho, $alto) = getimagesize($_FILES["editarImagen"]["tmp_name"]);

					$nuevoAncho = 500;
					$nuevoAlto = 500;

					//CREAMOS DIRECTORIO DE LAS FOTOS DEL USUARIO

					$directorio = "vistas/img/productos/".$_POST["editarCodigo"];

					//PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD

					if(!empty($_POST["imagenActual"]) && $_POST["imagenActual"] != "vistas/img/productos/default/anonymous.png"){

						unlink($_POST["imagenActual"]);
					}
					else{

						mkdir($directorio, 0755);
					}

					
						//DE A CUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES PHP, 1ro EN JPEG

						if($_FILES["editarImagen"]["type"] == "image/jpeg"){

							//GUARDAMOS LA IMAGEN EN EL DIRECTORIO

							$aleatorio = mt_rand(100, 999);

							$ruta = "vistas/img/productos/".$_POST["editarCodigo"]."/".$aleatorio.".jpeg";

							$origen = imagecreatefromjpeg($_FILES["editarImagen"]["tmp_name"]);

							$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

							imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

							imagejpeg($destino, $ruta);

						}

						//FUNCIONES PARA PNG

						if($_FILES["editarImagen"]["type"] == "image/png"){

							//GUARDAMOS LA IMAGEN EN EL DIRECTORIO

							$aleatorio = mt_rand(100, 999);

							$ruta = "vistas/img/productos/".$_POST["editarCodigo"]."/".$aleatorio.".png";

							$origen = imagecreatefrompng($_FILES["editarImagen"]["tmp_name"]);

							$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

							imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

							imagepng($destino, $ruta);

						}
				 }


				 $idProveedor = $_POST["editarProveedor"];
        
					// Si viene vacío, "0" o es 0, convertirlo a NULL
					if(empty($idProveedor) || $idProveedor == "0" || $idProveedor == 0){
						$idProveedor = null;
					}


				$tabla = "productos";

				// 🔹 OBTENER STOCK ANTERIOR antes de editar
				$productoAnterior = ModeloProductos::mdlMostrarProductos($tabla, "codigo", $_POST["editarCodigo"], "id");
				$stockAnterior = $productoAnterior["stock"];
				$nuevoStock = $_POST["editarStock"];

				$datos = array("id_categoria" => $_POST["editarCategoria"],
							   "codigo" => $_POST["editarCodigo"],
							   "descripcion" => $_POST["editarDescripcion"],
					           "stock" => $nuevoStock,
					           "precio_compra" => $_POST["editarPrecioCompra"],
				               "precio_venta" => $_POST["editarPrecioVenta"],
							   "id_proveedor" => $_POST["editarProveedor"],
				           	   "imagen" => $ruta); 

				$respuesta = ModeloProductos::mdlEditarProducto($tabla, $datos); 

				 if ($respuesta == "ok") {
					// 🟢 REGISTRAR MOVIMIENTO DE STOCK - EDICIÓN DE PRODUCTO
					if($stockAnterior != $nuevoStock){
						$diferencia = $nuevoStock - $stockAnterior;
						ControladorMovimientos::ctrRegistrarMovimiento(
							"producto",
							$productoAnterior["id"],
							null,
							$_POST["editarDescripcion"],
							"edicion_stock",
							$diferencia,
							$stockAnterior,
							$nuevoStock,
							"Stock editado manualmente",
							"Cambio de stock: " . $stockAnterior . " → " . $nuevoStock
						);

					}

					/*=============================================
					PROCESAR NUEVAS VARIANTES (si se agregaron desde editar)
					=============================================*/
					if(isset($_POST["totalCombinacionesEditar"]) && $_POST["totalCombinacionesEditar"] > 0){ 

						// DEBUG: Log de inicio
						file_put_contents("debug_editar_variantes.txt", "=== EDITAR PRODUCTO CON VARIANTES ===\n", FILE_APPEND);
						file_put_contents("debug_editar_variantes.txt", "Total combinaciones: " . $_POST["totalCombinacionesEditar"] . "\n", FILE_APPEND); 

						$idProducto = $_POST["editarCodigo"]; // Usamos el código como ID del producto 

						// Obtener el ID real del producto
						$productoBase = ModeloProductos::mdlMostrarProductos("productos", "codigo", $idProducto, "id");
						$idProductoReal = $productoBase["id"]; 

						file_put_contents("debug_editar_variantes.txt", "ID Producto: " . $idProductoReal . "\n", FILE_APPEND);
						$totalCombinaciones = $_POST["totalCombinacionesEditar"];
						$tablaVariantes = "productos_variantes"; 

						for($i = 0; $i < $totalCombinaciones; $i++){ 

							file_put_contents("debug_editar_variantes.txt", "\n--- Procesando combinación $i ---\n", FILE_APPEND); 

							// Verificar si esta combinación está seleccionada
							if(isset($_POST["combinacionEditar_".$i."_ids"]) && isset($_POST["combinacionEditar_".$i."_nombre"])){ 

								$idsCombinacion = $_POST["combinacionEditar_".$i."_ids"];
								$nombreCombinacion = $_POST["combinacionEditar_".$i."_nombre"]; 

								file_put_contents("debug_editar_variantes.txt", "IDs Combinación: $idsCombinacion\n", FILE_APPEND);
								file_put_contents("debug_editar_variantes.txt", "Nombre: $nombreCombinacion\n", FILE_APPEND); 

								// Obtener precio adicional y stock de esta combinación
								$precioAdicional = isset($_POST["precioAdicionalEditar_".$idsCombinacion]) && $_POST["precioAdicionalEditar_".$idsCombinacion] !== ""
													? $_POST["precioAdicionalEditar_".$idsCombinacion]
													: 0; 

								$stockVariante = isset($_POST["stockVarianteEditar_".$idsCombinacion]) && $_POST["stockVarianteEditar_".$idsCombinacion] !== ""
													? $_POST["stockVarianteEditar_".$idsCombinacion]
													: $_POST["editarStock"]; // Si no se especifica, usar el stock base del producto 

								file_put_contents("debug_editar_variantes.txt", "Precio Adicional: $precioAdicional\n", FILE_APPEND);
								file_put_contents("debug_editar_variantes.txt", "Stock: $stockVariante\n", FILE_APPEND); 

								// Verificar si la variante ya existe (viene el ID de variante existente)
								if(isset($_POST["idVarianteExistente_".$idsCombinacion]) && !empty($_POST["idVarianteExistente_".$idsCombinacion])){

 									// ACTUALIZAR variante existente
									$idVarianteExistente = $_POST["idVarianteExistente_".$idsCombinacion];

 									// 🔹 OBTENER STOCK ANTERIOR de la variante para registrar movimiento
									$stmtVarianteAntes = Conexion::conectar()->prepare("SELECT stock FROM productos_variantes WHERE id = :id");
									$stmtVarianteAntes->bindParam(":id", $idVarianteExistente, PDO::PARAM_INT);
									$stmtVarianteAntes->execute();
									$varianteAntes = $stmtVarianteAntes->fetch();
									$stockAnteriorVariante = $varianteAntes["stock"];
									$stmtVarianteAntes = null; 

 									file_put_contents("debug_editar_variantes.txt", ">>> UPDATE variante existente ID: $idVarianteExistente\n", FILE_APPEND);
									file_put_contents("debug_editar_variantes.txt", "Precio adicional a actualizar: $precioAdicional\n", FILE_APPEND);
									file_put_contents("debug_editar_variantes.txt", "Stock a actualizar: $stockVariante\n", FILE_APPEND); 

									$datosActualizar = array(
										"id" => $idVarianteExistente,
										"precio_adicional" => $precioAdicional,
										"stock" => $stockVariante
									);

									try {
										$resultado = ModeloProductos::mdlEditarVariante($tablaVariantes, $datosActualizar);
										file_put_contents("debug_editar_variantes.txt", "Resultado UPDATE: $resultado\n", FILE_APPEND); 

										if($resultado === "ok"){
											file_put_contents("debug_editar_variantes.txt", "UPDATE exitoso\n", FILE_APPEND); 

											// 🟢 REGISTRAR MOVIMIENTO DE STOCK - EDICIÓN DE VARIANTE EXISTENTE
											if($stockAnteriorVariante != $stockVariante){
												$diferenciaStock = $stockVariante - $stockAnteriorVariante;
												ControladorMovimientos::ctrRegistrarMovimiento(
													"variante",
													$idProductoReal,
													$idVarianteExistente,
													$_POST["editarDescripcion"] . " - " . $nombreCombinacion,
													"edicion_stock",
													$diferenciaStock,
													$stockAnteriorVariante,
													$stockVariante,
													"Stock de variante actualizado",
													"Variante actualizada desde editar producto"
												);
											}

										} else {
											file_put_contents("debug_editar_variantes.txt", "UPDATE falló: $resultado\n", FILE_APPEND);
										}

									} catch (Exception $e) {
										file_put_contents("debug_editar_variantes.txt", "ERROR en UPDATE: " . $e->getMessage() . "\n", FILE_APPEND);
									}

								} else {
									// CREAR nueva variante
									file_put_contents("debug_editar_variantes.txt", ">>> INSERT nueva variante\n", FILE_APPEND); 

									$skuBase = $_POST["editarCodigo"];
									$skuVariante = $skuBase . "_" . $idsCombinacion;

									file_put_contents("debug_editar_variantes.txt", "SKU: $skuVariante\n", FILE_APPEND); 

									$datosVariante = array(
										"id_producto" => $idProductoReal,
										"sku" => $skuVariante,
										"precio_adicional" => $precioAdicional,
										"stock" => $stockVariante,
										"imagen" => "",
										"estado" => 1
									); 

									// Guardar variante y obtener su ID
									$idVarianteNueva = ModeloProductos::mdlGuardarVariante($datosVariante);
									file_put_contents("debug_editar_variantes.txt", "Resultado INSERT: ID = $idVarianteNueva\n", FILE_APPEND); 

									if($idVarianteNueva){
										// 🟢 REGISTRAR MOVIMIENTO DE STOCK - CREACIÓN DE VARIANTE
										if($stockVariante > 0){
											ControladorMovimientos::ctrRegistrarMovimiento(
												"variante",
												$idProductoReal,
												$idVarianteNueva,
												$_POST["editarDescripcion"] . " - " . $nombreCombinacion,
												"creacion_variante",
												$stockVariante,
												0,
												$stockVariante,
												"Variante creada con stock inicial: " . $nombreCombinacion,
												"Variante agregada desde editar producto"
											);

										}

										// Guardar las opciones de la variante
										$opcionesArray = explode("_", $idsCombinacion);
										file_put_contents("debug_editar_variantes.txt", "Guardando opciones: " . implode(", ", $opcionesArray) . "\n", FILE_APPEND);

 										foreach($opcionesArray as $idOpcion){
											$datosOpcion = array(
												"id_producto_variante" => $idVarianteNueva,
												"id_opcion_variante" => $idOpcion
											);

											ModeloProductos::mdlGuardarVarianteOpcion($datosOpcion);
										}

										file_put_contents("debug_editar_variantes.txt", "Opciones guardadas correctamente\n", FILE_APPEND);

									} else {
										file_put_contents("debug_editar_variantes.txt", "ERROR: No se pudo insertar la variante\n", FILE_APPEND);
									}
								}

							} else {
								file_put_contents("debug_editar_variantes.txt", "Combinación $i NO seleccionada (checkbox desmarcado)\n", FILE_APPEND);
							}

						}
 

						file_put_contents("debug_editar_variantes.txt", "\n=== FIN PROCESAMIENTO ===\n\n", FILE_APPEND);
					}


					/*=============================================
					RECALCULAR STOCK AUTOMÁTICO DEL PRODUCTO BASE
					=============================================*/
					if(isset($_POST["totalCombinacionesEditar"]) && $_POST["totalCombinacionesEditar"] > 0){
						// Calcular la suma del stock de todas las variantes activas
						$stmt = Conexion::conectar()->prepare("SELECT SUM(stock) as stock_total FROM productos_variantes WHERE id_producto = :id_producto AND estado = 1");
						$stmt->bindParam(":id_producto", $idProductoReal, PDO::PARAM_INT);
						$stmt->execute();
						$resultado = $stmt->fetch();
						$stmt = null;
						
						$stockTotal = $resultado["stock_total"] ? $resultado["stock_total"] : 0;
						
						// Actualizar el stock del producto base
						$tablaProductos = "productos";
						ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $stockTotal, $idProductoReal);
					}

			    	echo '<script>
					swal({
						type: "success",
						title: "!El producto ha sido editado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar",
						}).then((result)=>{
							if(result.value){

							   window.location = "productos";
							}
						})
			     	</script>';
		         }

			}

			else{
				echo '<script>
					swal({
						type: "error",
						title: "!El producto no puede ir con los campos vacíos o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar",
						closeOnConfirm: false
						}).then((result)=>{

							if(result.value){

								window.location = "productos";
							}
						})
				</script>';
			}

		}

	}


	/*=============================================
	ELIMINAR PRODUCTO
	=============================================*/
	static public function ctrEliminarProducto(){

		if(isset($_GET["idProducto"])){

			$tabla ="productos";
			$datos = $_GET["idProducto"];

			if($_GET["imagen"] != "" && $_GET["imagen"] != "vistas/img/productos/default/anonymous.png"){

				unlink($_GET["imagen"]);
				rmdir('vistas/img/productos/'.$_GET["codigo"]);
			}
			
			// Verificamos si el producto está asociado a alguna venta
			/*
			$ventas = ModeloVentas::mdlMostrarVentas("ventas", null, null); 
			foreach ($ventas as $venta) {	
				$productosVenta = json_decode($venta["productos"], true);	
				foreach ($productosVenta as $producto) {
					if ($producto["id"] == $datos) {	
						echo '<script>
							swal({
								type: "error",
								title: "¡No se puede eliminar!",
								text: "Este producto está asociado a una o más ventas.",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
							}).then((result) => {
								if (result.value) {
									window.location = "productos";
								}
							});
						</script>';
						return; // Cancelamos eliminación
					}
				}
			}
			*/


			$respuesta = ModeloProductos::mdlEliminarProducto($tabla, $datos);

			if ($respuesta == "ok") {

			    	echo '<script>
					swal({
						type: "success",
						title: "!El producto ha sido borrado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar",
						}).then((result)=>{
							if(result.value){

							   window.location = "productos";
							}
						})
			     	</script>';
		     }

		}


	}


	/*=============================================
	MOSTRAR SUMA VENTAS
	=============================================*/

	static public function ctrMostrarSumaVentas(){

		$tabla = "productos";

		$respuesta = ModeloProductos::mdlMostrarSumaVentas($tabla);

		return $respuesta;

	}


	/*=============================================
	Actualizar Imagen Producto desde DataTable
	=============================================*/
	 public static function ctrActualizarImagenProducto($idProducto, $rutaImagen){
        $tabla = "productos";
        return ModeloProductos::mdlActualizarImagenProducto($tabla, $idProducto, $rutaImagen);
    }


	/*=============================================
	IMPORTAR PRODUCTOS DESDE CSV
	=============================================*/

	static public function ctrImportarProductos(){ 

		if(isset($_FILES["archivoCSV"])){ 

			$archivo = $_FILES["archivoCSV"]["tmp_name"];
			$errores = array();
			$productosImportar = array(); 

			// Abrir archivo CSV
			if (($handle = fopen($archivo, "r")) !== FALSE) {

 
				// Saltar BOM si existe
				$bom = fread($handle, 3);
				if ($bom != "\xEF\xBB\xBF") {
					rewind($handle);
				}

				// DETECTAR DELIMITADOR AUTOMÁTICAMENTE
				$primeraLinea = fgets($handle);
				rewind($handle); 

				// Saltar BOM nuevamente después de rewind
				$bom = fread($handle, 3);
				if ($bom != "\xEF\xBB\xBF") {
					rewind($handle);
				} 

				// Contar delimitadores en la primera línea
				$contadorComa = substr_count($primeraLinea, ',');
				$contadorPuntoYComa = substr_count($primeraLinea, ';'); 

				// Usar el delimitador que aparezca más veces
				$delimitador = ($contadorPuntoYComa > $contadorComa) ? ';' : ','; 

				// Leer encabezados
				$encabezados = fgetcsv($handle, 1000, $delimitador); 

				$numeroFila = 1; // Contador de fila (empieza en 1 para los datos, 0 es el encabezado) 

				// Leer cada línea del CSV
				while (($datos = fgetcsv($handle, 1000, $delimitador)) !== FALSE) {
					$numeroFila++;

					// Saltar filas vacías
					if(empty(array_filter($datos))){
						continue;
					} 

					// Validar que la fila tenga 7 columnas
					if(count($datos) < 7){

						$errores[] = "Fila $numeroFila: Faltan columnas (se requieren 7, encontradas ".count($datos).")";
						continue;
					} 

					$codigo = trim($datos[0]);
					$descripcion = trim($datos[1]);
					$categoria = trim($datos[2]);
					$proveedor = trim($datos[3]);
					$stock = trim($datos[4]);
					$precioCompra = trim($datos[5]);
					$precioVenta = trim($datos[6]);

 					// Validar campos obligatorios (proveedor es OPCIONAL)

					if(empty($codigo) || empty($descripcion) || empty($categoria)){
						$errores[] = "Fila $numeroFila: Campos obligatorios vacíos (código, descripción, categoría)";
						continue;
					} 

					// Validar números
					if(!is_numeric($stock) || !is_numeric($precioCompra) || !is_numeric($precioVenta)){
						$errores[] = "Fila $numeroFila: Stock y precios deben ser números";
						continue;
					} 

					// Normalizar y buscar categoría
					$categoriaNormalizada = self::normalizarTexto($categoria);
					$categoriaEncontrada = self::buscarCategoriaPorNombre($categoriaNormalizada);
 
					if(!$categoriaEncontrada){
						$errores[] = "Fila $numeroFila: La categoría '$categoria' no existe o no coincide";
						continue;
					} 

					// Normalizar y buscar proveedor (OPCIONAL)
					$idProveedor = null;
					if(!empty($proveedor)){
						$proveedorNormalizado = self::normalizarTexto($proveedor);
						$proveedorEncontrado = self::buscarProveedorPorNombre($proveedorNormalizado);
 
						if(!$proveedorEncontrado){
							$errores[] = "Fila $numeroFila: El proveedor '$proveedor' no existe o no coincide";
							continue;
						}

						$idProveedor = $proveedorEncontrado["id"];
					}

					// Verificar si el código ya existe
					$item = "codigo";
					$valor = $codigo;
					$productoExiste = ModeloProductos::mdlMostrarProductos("productos", $item, $valor, null); 

					if($productoExiste){
						$errores[] = "Fila $numeroFila: El código '$codigo' ya existe en el sistema";
						continue;
					}

					// Agregar producto a la lista de importación
					$productosImportar[] = array(
						"id_categoria" => $categoriaEncontrada["id"],
						"id_proveedor" => $idProveedor,
						"codigo" => $codigo,
						"descripcion" => $descripcion,
						"imagen" => "vistas/img/productos/default/anonymous.png",
						"stock" => $stock,
						"precio_compra" => $precioCompra,
						"precio_venta" => $precioVenta,
						"ventas" => 0
					); 
				}

				fclose($handle);

 				// Si hay errores, no importar nada
				if(count($errores) > 0){ 

					$mensajeError = "⚠️ Error en la importación:\\n\\n";
					foreach($errores as $error){
						$mensajeError .= "• " . $error . "\\n";
					}

					$mensajeError .= "\\nPor favor corrige el archivo CSV y vuelve a intentar."; 

					echo '<script>
						swal({
							type: "error",
							title: "Error en la importación",
							text: "'.$mensajeError.'",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then((result)=>{
							if(result.value){
								window.location = "productos";
							}
						});
					</script>';
					return;
				}


				// Si no hay errores, importar todos los productos
				if(count($productosImportar) > 0){ 

					$tabla = "productos";
					$respuesta = ModeloProductos::mdlImportarProductosMasivos($tabla, $productosImportar); 

					if($respuesta == "ok"){

						$totalImportados = count($productosImportar); 

						echo '<script>
							swal({
								type: "success",
								title: "¡Importación exitosa!",
								text: "Se importaron '.$totalImportados.' productos correctamente.",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
							}).then((result)=>{
								if(result.value){
									window.location = "productos";
								}
							});
						</script>'; 

					} else {
						echo '<script>
							swal({
								type: "error",
								title: "Error al importar",
								text: "Hubo un error al guardar los productos en la base de datos.",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
							}).then((result)=>{
								if(result.value){
									window.location = "productos";
								}
							});
						</script>';
					}

				} else {
					echo '<script>
						swal({
							type: "warning",
							title: "Sin productos válidos",
							text: "No hay productos válidos para importar en el archivo CSV.",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then((result)=>{
							if(result.value){
								window.location = "productos";
							}
						});
					</script>';
				}

			} else {
				echo '<script>
					swal({
						type: "error",
						title: "Error al leer archivo",
						text: "No se pudo abrir el archivo CSV.",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then((result)=>{
						if(result.value){
							window.location = "productos";
						}
					});
				</script>';
			}
		}
	}

	/*=============================================
	FUNCIÓN AUXILIAR: NORMALIZAR TEXTO
	=============================================*/
	static private function normalizarTexto($texto){

		// Convertir a minúsculas
		$texto = strtolower($texto); 

		// Quitar espacios al inicio y final
		$texto = trim($texto);

		// Quitar acentos
		$texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);

 		// Quitar espacios múltiples
		$texto = preg_replace('/\s+/', ' ', $texto); 

		return $texto;
	}


	/*=============================================
	FUNCIÓN AUXILIAR: BUSCAR CATEGORÍA POR NOMBRE
	=============================================*/

	static private function buscarCategoriaPorNombre($nombreNormalizado){ 

		$categorias = ControladorCategorias::ctrMostrarCategorias(null, null);
 
		foreach($categorias as $categoria){

			$categoriaNormalizada = self::normalizarTexto($categoria["categoria"]);

			if($categoriaNormalizada == $nombreNormalizado){

				return $categoria;
			}

		} 

		return false;
	}

 
	/*=============================================
	FUNCIÓN AUXILIAR: BUSCAR PROVEEDOR POR NOMBRE
	=============================================*/

	static private function buscarProveedorPorNombre($nombreNormalizado){ 

		$proveedores = ControladorProveedores::ctrMostrarProveedores(null, null); 

		foreach($proveedores as $proveedor){

			$proveedorNormalizado = self::normalizarTexto($proveedor["nombre"]);

			if($proveedorNormalizado == $nombreNormalizado){

				return $proveedor;
			}
		} 

		return false;
	}	



}





