<?php 

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

class AjaxUsuarios{

	/*=============================================
	EDITAR USUARIO
	=============================================*/

	public $idUsuario;

	public function ajaxEditarUsuario(){

		$item = "id";
		$valor = $this->idUsuario;

		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

		echo json_encode($respuesta);
	}

	/*=============================================
	ACTIVAR USUARIO
	=============================================*/

	public $activarUsuario;
	public $activarId;

	public function ajaxActivarUsuario(){

		$tabla = "usuarios";

		$item1 = "estado";
		$valor1 = $this->activarUsuario;

		$item2 = "id";
		$valor2 = $this->activarId;

		$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2);

	}

	/*=============================================
	VALIDAR NO REPETIR USUARIO
	=============================================*/

	public $validarUsuario;
	public function ajaxValidarUsuario(){

		$item = "usuario";
		$valor = $this->validarUsuario;

		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

		echo json_encode($respuesta);
	}


}

/*=============================================
EDITAR USUARIO
=============================================*/
if(isset($_POST["idUsuario"])){

	$editar = new AjaxUsuarios();
	$editar -> idUsuario = $_POST["idUsuario"];
	$editar -> ajaxEditarUsuario();

}

/*=============================================
ACTIVAR USUARIO
=============================================*/

if(isset($_POST["activarUsuario"])){

	$activarUsuario = new AjaxUsuarios();
	$activarUsuario -> activarUsuario = $_POST["activarUsuario"];
	$activarUsuario -> activarId = $_POST["activarId"];
	$activarUsuario -> ajaxActivarUsuario();
}

/*=============================================
VALIDAR NO REPETIR USUARIO
=============================================*/

if(isset($_POST["validarUsuario"])){

	$valUsuario = new AjaxUsuarios();
	$valUsuario -> validarUsuario = $_POST["validarUsuario"];
	$valUsuario -> ajaxValidarUsuario();
}


/*=============================================
ACTUALIZAR IMAGEN DE USUARIO DESDE LA TABLA
=============================================*/

if(isset($_FILES["nuevaImagenUsuario"])){

    require_once "../modelos/usuarios.modelo.php";

    $idUsuario = $_POST["idUsuarioImagen"];
    $usuario = $_POST["usuarioNombre"];
    
    list($ancho, $alto) = getimagesize($_FILES["nuevaImagenUsuario"]["tmp_name"]);

    $nuevoAncho = 500;
    $nuevoAlto = 500;

    // Crear directorio si no existe
    $directorio = "../vistas/img/usuarios/".$usuario;

    if(!file_exists($directorio)){
        mkdir($directorio, 0755, true);
    }

    // Procesar según el tipo de imagen
    $ruta = "";
    
    if($_FILES["nuevaImagenUsuario"]["type"] == "image/jpeg"){

        $aleatorio = mt_rand(100, 999);
        $ruta = "vistas/img/usuarios/".$usuario."/".$aleatorio.".jpeg";

        $origen = imagecreatefromjpeg($_FILES["nuevaImagenUsuario"]["tmp_name"]);
        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
        imagejpeg($destino, "../".$ruta);

    }

    if($_FILES["nuevaImagenUsuario"]["type"] == "image/png"){

        $aleatorio = mt_rand(100, 999);
        $ruta = "vistas/img/usuarios/".$usuario."/".$aleatorio.".png";

        $origen = imagecreatefrompng($_FILES["nuevaImagenUsuario"]["tmp_name"]);
        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
        imagepng($destino, "../".$ruta);

    }

    // Actualizar en la base de datos
    if(!empty($ruta)){
        $tabla = "usuarios";
        $datos = array("foto" => $ruta);
        
        $respuesta = ModeloUsuarios::mdlActualizarImagenUsuario($tabla, $datos, $idUsuario);

        echo json_encode($respuesta);
    } else {
        echo json_encode("error");
    }
    
    exit;
}