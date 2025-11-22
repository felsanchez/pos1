/*=============================================
EDITAR Actividades
=============================================*/

//console log
//console.log("Datos completos:", datos);

/*=============================================
EDITAR Actividades
=============================================*/
$(".tablas").on("click", ".btnEditarActividad", function(){
	var idActividad = $(this).attr("idActividad");
    console.log("ID Actividad: " + idActividad); 

	// Rellenar el input hidden
    $('#modalEditarActividad input[name="idActividad"]').val(idActividad);

	var datos = new FormData();
	datos.append("idActividad", idActividad);

	$.ajax({

		url:"ajax/actividades.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

            //console.log("Respuesta AJAX:", respuesta);

			//$("#idActividad").val(respuesta["id"]);
			$("#editarActividad").val(respuesta["descripcion"]);
            $("#editarTipo").val(respuesta["tipo"]);
            $("#editarUsuario").val(respuesta["id_user"]);
            //$("#editarFecha").val(respuesta["fecha"]);

			if (respuesta.fecha) {
				$("#editarFecha").val(respuesta.fecha.substring(0, 10));
			} else {
				console.error("Fecha no válida:", respuesta.fecha);
			}

            $("#editarEstado").val(respuesta["estado"]);
            $("#editarCliente").val(respuesta["id_cliente"]);
            $("#editarObservacion").val(respuesta["observacion"]);

            // ✅ mostrar el modal
			$('#modalEditarActividad').modal('show');
            

		},

        //error: function(xhr, status, error){
            //console.error("Error en AJAX:", xhr.responseText);
        //}

	}) 

});



/*=============================================
ELIMINAR Actividad
=============================================*/
$(".tablas").on("click", ".btnEliminarActividad", function(){
	
	var idActividad = $(this).attr("idActividad");

	//var_dump($idActividad);

	swal({

		title: '¿Esta seguro de borrar la actividad?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar actividad!'
	}).then((result)=>{

		if(result.value){

			window.location = "index.php?ruta=actividades&idActividad="+idActividad;
		}
	})
})


/*=============================================
Guardar Tipo - DESACTIVADO (campo ahora es solo lectura)
=============================================*/
/*
$(".tablas").on("change", ".cambiarTipo", function() {
    var idActividad = $(this).data("id");
    var nuevoTipo = $(this).val();

	console.log("Voy a enviar AJAX con id:", idActividad, "y nuevoTipo:", nuevoTipo);

    $.ajax({
        url: "ajax/actividades.ajax.php",
        method: "POST",
        data: { idActividad: idActividad, nuevoTipo: nuevoTipo },
        success: function(respuesta) {

			//console.log("Respuesta RAW:", respuesta);

			var datos = JSON.parse(respuesta);
			//console.log("Respuesta al cambiar tipo:", datos);
			if (datos.status === "error") {
				alert("Hubo un error al actualizar");
			} else {
				alert("Tipo actualizado correctamente a: " + datos.tipo);
				// Aquí puedes actualizar el valor mostrado en la tabla, si quieres
			}
		}

    });
});
*/


/*=============================================
Guardar Estado - DESACTIVADO (campo ahora es solo lectura)
=============================================*/
/*
$(".tablas").on("change", ".cambiarEstado", function() {
    var idActividad = $(this).data("id");
    var nuevoEstado = $(this).val();

	console.log("Voy a enviar AJAX con id:", idActividad, "y nuevoEstado:", nuevoEstado);

    $.ajax({
        url: "ajax/actividades.ajax.php",
        method: "POST",
        data: { idActividad: idActividad, nuevoEstado: nuevoEstado },
        success: function(respuesta) {

			//console.log("Respuesta RAW:", respuesta);

			var datos = JSON.parse(respuesta);
			//console.log("Respuesta al cambiar tipo:", datos);
			if (datos.status === "error") {
				alert("Hubo un error al actualizar");
			} else {
				alert("Estado actualizado correctamente a: " + datos.estado);
				// Aquí puedes actualizar el valor mostrado en la tabla, si quieres
			}
		}

    });
});
*/


/*=============================================
Editar Estado desde Modal de Gestión
=============================================*/
$("#modalGestionarEstados").on("click", ".btnEditarEstadoActividad", function(e){

	// Prevenir que el modal se abra inmediatamente
	e.preventDefault();

	var idEstado = $(this).attr("idEstado");
    console.log("ID Estado: " + idEstado);

	var datos = new FormData();
	datos.append("idEstado", idEstado);

	$.ajax({

		url:"ajax/estados-actividades.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success:function(respuesta){

			console.log("Datos del estado:", respuesta);

			// Verificar si hay error en la respuesta
			if(respuesta.error){
				console.error("Error del servidor:", respuesta.error);
				swal({
					type: "error",
					title: "Error",
					text: respuesta.error
				});
				return;
			}

			// Rellenar los campos del modal
			console.log("Rellenando campos con:", respuesta);
			$("#idEstado").val(respuesta["id"]);
			$("#editarEstadoNombre").val(respuesta["nombre"]);
			$("#editarEstadoColor").val(respuesta["color"]);
			$("#editarEstadoOrden").val(respuesta["orden"]);

			// Verificar valores
			console.log("Valor de editarEstadoNombre:", $("#editarEstadoNombre").val());
			console.log("Campo disabled?:", $("#editarEstadoNombre").prop("disabled"));
			console.log("Campo readonly?:", $("#editarEstadoNombre").prop("readonly"));

			// Asegurar que los campos no estén deshabilitados
			$("#editarEstadoNombre").prop("disabled", false).prop("readonly", false);
			$("#editarEstadoColor").prop("disabled", false).prop("readonly", false);

			console.log("Abriendo modal...");
			// Abrir el modal DESPUÉS de cargar los datos
			$("#modalEditarEstadoActividad").modal("show");

			// Forzar focus en el campo nombre cuando el modal esté completamente visible
			$("#modalEditarEstadoActividad").one("shown.bs.modal", function(){
				console.log("Modal abierto, aplicando focus");
				setTimeout(function(){
					$("#editarEstadoNombre").focus().select();
					console.log("Focus aplicado");
				}, 100);
			});

		},
		error: function(xhr, status, error){
			console.error("Error AJAX:", error);
			console.error("Status:", status);
			console.error("Respuesta completa:", xhr.responseText);

			var mensajeError = "No se pudieron cargar los datos del estado";

			// Intentar parsear la respuesta como JSON
			try {
				var respuestaJSON = JSON.parse(xhr.responseText);
				if(respuestaJSON.error){
					mensajeError = respuestaJSON.error;
				}
			} catch(e) {
				// Si no es JSON válido, mostrar la respuesta tal cual
				if(xhr.responseText){
					mensajeError = xhr.responseText.substring(0, 200);
				}
			}

			swal({
				type: "error",
				title: "Error",
				text: mensajeError
			});
		}

	})

})

// Debug: Detectar eventos en el campo de edición
$(document).ready(function(){
	$(document).on("keydown keypress keyup input", "#editarEstadoNombre", function(e){
		console.log("Evento detectado en editarEstadoNombre:", e.type, "Key:", e.key);
		return true; // Permitir que el evento continúe
	});

	$(document).on("click", "#editarEstadoNombre", function(e){
		console.log("Click en editarEstadoNombre detectado");
		console.log("Elemento:", this);
		console.log("Es editable?:", !$(this).prop("disabled") && !$(this).prop("readonly"));
	});
});


/*=============================================
Eliminar Estado
=============================================*/
$(".btnEliminarEstadoActividad").click(function(){

	var idEstado = $(this).attr("idEstado");
	var nombreEstado = $(this).attr("nombreEstado");

	swal({
		title: '¿Está seguro de borrar el estado "'+nombreEstado+'"?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, borrar estado!'
	}).then(function(result){

		if(result.value){

			window.location = "index.php?ruta=actividades&idEstado="+idEstado+"&nombreEstado="+nombreEstado;

		}

	})

})
