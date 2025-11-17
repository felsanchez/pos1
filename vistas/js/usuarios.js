/*=============================================
SUBIENDO FOTO DEL USUARIO
=============================================*/

$(".nuevaFoto").change(function(){

	var imagen = this.files[0];


/*=============================================
VALIDAMOS EL FORMATO DE LA IMAGEN QUE SEA JPG O PNG
=============================================*/

	if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

		$(".nuevaFoto").val("");

		swal({
			title: "Error al subir la imagenn",
			text: "¡La imagen debe estar en formato jpg o png!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});
	}

	else if(imagen["size"] > 2000000){

		$(".nuevaFoto").val("");

		swal({
			title: "Error al subir la imagen",
			text: "¡La imagen no debe pesar mas de 2MB!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});

	}

	else{

		var datosImagen = new FileReader;
		datosImagen.readAsDataURL(imagen);

		$(datosImagen).on("load", function(event){

			var rutaImagen = event.target.result;

			$(".previsualizar").attr("src", rutaImagen);
		})
	}


})


/*=============================================
EDITAR USUARIO
=============================================*/

$(".tablas").on("click", ".btnEditarUsuario", function(){

	var idUsuario = $(this).attr("idUsuario");
	
	var datos = new FormData();
	datos.append("idUsuario", idUsuario);

	$.ajax({

		url:"ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			$("#editarNombre").val(respuesta["nombre"]);
			$("#editarUsuario").val(respuesta["usuario"]);
			$("#editarPerfil").html(respuesta["perfil"]);

			$("#editarPerfil").val(respuesta["perfil"]);
			$("#fotoActual").val(respuesta["foto"]);
			$("#passwordActual").val(respuesta["password"]);

			if(respuesta["foto"] != ""){

				$(".previsualizar").attr("src", respuesta["foto"]);
			}

		}

	});

})



/*=============================================
ACTIVAR USUARIO CON EFECTO
=============================================*/
$(".tablas").on("click", ".btnActivar", function(){

	var idUsuario = $(this).attr("idUsuario");
	var estadoUsuario = $(this).attr("estadoUsuario");
	var boton = $(this);
	var fila = boton.closest('tr');

	// Agregar efecto de fade
	fila.css('opacity', '0.5');
	
	// Deshabilitar botón temporalmente
	boton.prop('disabled', true);
	var textoOriginal = boton.html();
	boton.html('<i class="fa fa-spinner fa-spin"></i> Procesando...');

	var datos = new FormData();
	datos.append("activarId", idUsuario);
	datos.append("activarUsuario", estadoUsuario);

	$.ajax({
		url:"ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		success: function(respuesta){

			// Pequeño delay para ver el efecto
			setTimeout(function(){

				// Cambiar el estado del botón con animación
				if(estadoUsuario == 0){
					boton.removeClass('btn-success').addClass('btn-danger');
					boton.html('Desactivado');
					boton.attr('estadoUsuario', 1);
				} else {
					boton.removeClass('btn-danger').addClass('btn-success');
					boton.html('Activado');
					boton.attr('estadoUsuario', 0);
				}

				// Efecto de "parpadeo" para indicar cambio
				fila.css('background-color', '#d4edda');
				fila.animate({opacity: 1}, 300);
				
				setTimeout(function(){
					fila.css('background-color', '');
				}, 1000);

				boton.prop('disabled', false);

			}, 400); // Delay para ver el efecto

		},
		error: function(){
			boton.html(textoOriginal);
			boton.prop('disabled', false);
			fila.css('opacity', '1');
			
			swal({
				type: "error",
				title: "Error en la conexión",
				showConfirmButton: true,
				confirmButtonText: "Cerrar"
			});
		}
	})

})


/*=============================================
REVISAR SI EL USUARIO YA ESTA REGISTRADO
=============================================*/

$("#nuevoUsuario").change(function(){

	$(".alert").remove();

	var usuario = $(this).val();

	var datos = new FormData();
	datos.append("validarUsuario", usuario);

	$.ajax({
		url:"ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			if(respuesta){

				$("#nuevoUsuario").parent().after('<div class="alert alert-warning">Este usuario ya existe en la base de datos!</div>');

				$("#nuevoUsuario").val("");
			}

		}
	})
})

/*=============================================
ELIMINAR USUARIO
=============================================*/

$(".tablas").on("click", ".btnEliminarUsuario", function(){

	var idUsuario = $(this).attr("idUsuario");
	var fotoUsuario = $(this).attr("fotoUsuario");
	var usuario = $(this).attr("usuario");

	swal({
		title: '¿Esta seguro de borrar el usuario?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar usuario!'
	}).then((result)=>{

		if(result.value){

			window.location = "index.php?ruta=usuarios&idUsuario="+idUsuario+"&usuario="+usuario+"&fotoUsuario="+fotoUsuario;
		}


	})


})