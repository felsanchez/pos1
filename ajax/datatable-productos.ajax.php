<?php
// Comentado temporalmente para evitar que warnings rompan el JSON
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";

require_once "../controladores/proveedores.controlador.php";
require_once "../modelos/proveedores.modelo.php";


class TablaProductos{

 	/*=============================================
 	 MOSTRAR LA TABLA DE PRODUCTOS
  	=============================================*/ 

	public function mostrarTabla(){

		$item = null;
    	$valor = null;
    	$orden = "id"; 

  		$productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden); 

  		if(count($productos) == 0){ 

  			echo json_encode(array("data" => array()));
		  	return;
  		}

   		// Crear array de datos en lugar de concatenar strings
  		$data = array();

		  for($i = 0; $i < count($productos); $i++){ 

		  	/*=============================================
 	 		TRAEMOS LA IMAGEN
  			=============================================*/ 

		  	//$imagen = "<img src='".$productos[$i]["imagen"]."' width='40px'>";
			$imagen = $productos[$i]["imagen"] ? $productos[$i]["imagen"] : ""; 

		  	/*=============================================
 	 		TRAEMOS LA CATEGORÍA
  			=============================================*/ 

		  	$item = "id";
		  	$valor = $productos[$i]["id_categoria"]; 

		  	$categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);
			$nombreCategoria = ($categorias && isset($categorias["categoria"])) ? $categorias["categoria"] : "Sin categoría"; 

			/*=============================================
				TRAEMOS EL PROVEEDOR
				=============================================*/ 

				$nombreProveedor = "Sin proveedor"; // Valor por defecto
 

				// Solo buscar proveedor si existe y es diferente de 0
				if(!empty($productos[$i]["id_proveedor"]) && $productos[$i]["id_proveedor"] != 0 && $productos[$i]["id_proveedor"] != null){

 					$item = "id";
					$valor = $productos[$i]["id_proveedor"];
					$proveedores = ControladorProveedores::ctrMostrarProveedores($item, $valor); 

					// Verificar que se obtuvo resultado

					if($proveedores && isset($proveedores["nombre"])){
						$nombreProveedor = $proveedores["nombre"];
					}

				}

		  	/*=============================================
 	 		STOCK
  			=============================================*/ 

  			if($productos[$i]["stock"] <= 10){ 

  				$stock = "<button class='btn btn-danger'>".$productos[$i]["stock"]."</button>"; 

  			}else if($productos[$i]["stock"] >= 11 && $productos[$i]["stock"] <= 15){ 

  				$stock = "<button class='btn btn-warning'>".$productos[$i]["stock"]."</button>"; 

  			}else{ 

  				$stock = "<button class='btn btn-success'>".$productos[$i]["stock"]."</button>"; 
  			} 

		  	/*=============================================
 	 		COLUMNA DE ACCIONES CON BOTÓN DE EXPANSIÓN
  			=============================================*/
  			// Construir botones de acciones

  			$botonesAcciones = '<div class="btn-group">'; 

  			// Si el producto tiene variantes, agregar botón de expansión

  			if($productos[$i]["tiene_variantes"] == 1){
  				$botonesAcciones .= '<button class="btn btn-info btn-xs btnExpandirVariantes" data-id-producto="'.$productos[$i]["id"].'" title="Ver variantes"><i class="fa fa-plus"></i></button>';
  			}

  			// Botón editar
  			$botonesAcciones .= '<button class="btn btn-warning btnEditarProducto" idProducto="'.$productos[$i]["id"].'"><i class="fa fa-pencil"></i></button>'; 

  			// Botón eliminar
  			$botonesAcciones .= '<button class="btn btn-danger btnEliminarProducto" idProducto="'.$productos[$i]["id"].'" codigo="'.$productos[$i]["codigo"].'" imagen="'.$productos[$i]["imagen"].'"><i class="fa fa-times"></i></button>';

  			$botonesAcciones .= '</div>'; 

  			// Descripción con botones para móvil
  			$descripcionBotones = $productos[$i]["descripcion"].' <div class="btn-group"><button class="btn btn-danger btn-xs solo-movil btnEliminarProducto" style="float: right;" idProducto="'.$productos[$i]["id"].'" codigo="'.$productos[$i]["codigo"].'" imagen="'.$productos[$i]["imagen"].'"><i class="fa fa-times"></i></button><button class="btn btn-warning btn-xs solo-movil btnEditarProducto" style="float: right;" idProducto="'.$productos[$i]["id"].'"><i class="fa fa-pencil"></i></button></div>';

		  	// Agregar fila al array de datos
		  	$data[] = array(
		  		($i+1),
		  		$imagen,
		  		$productos[$i]["codigo"],
		  		$descripcionBotones,
		  		$nombreCategoria,
		  		$stock,
		  		"$ ".number_format($productos[$i]["precio_compra"]),
		  		"$ ".number_format($productos[$i]["precio_venta"]),
		  		$nombreProveedor,
		  		$productos[$i]["fecha"],
		  		$botonesAcciones
		  	);
 		  }

		  // Usar json_encode para generar JSON válido
		  echo json_encode(array("data" => $data));
 	}
 
} 

/*=============================================
ACTIVAR TABLA DE PRODUCTOS
=============================================*/

$activar = new TablaProductos();
$activar -> mostrarTabla();

