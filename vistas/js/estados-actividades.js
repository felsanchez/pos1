/*=============================================
DATATABLE ESTADOS
=============================================*/
$(".tablaEstadosActividades").DataTable({

	"language": {
		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
		},

		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}
	}
});


/*=============================================
EDITAR ESTADO ACTIVIDAD
=============================================*/ 

$(".tablaEstadosActividades").on("click", ".btnEditarEstadoActividad", function(){ 

	var idEstado = $(this).attr("idEstado");
	var datos = new FormData();
	datos.append("idEstado", idEstado); 

	$.ajax({
		url: "ajax/estados-actividades.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){ 

			$("#idEstado").val(respuesta["id"]);
			$("#editarEstadoNombre").val(respuesta["nombre"]);
			$("#editarEstadoColor").val(respuesta["color"]);
			$("#editarEstadoOrden").val(respuesta["orden"]);
		}
	});
});
 

/*=============================================
ELIMINAR ESTADO ACTIVIDAD
=============================================*/ 

$(".tablaEstadosActividades").on("click", ".btnEliminarEstadoActividad", function(){

 	var idEstado = $(this).attr("idEstado");
	var nombreEstado = $(this).attr("nombreEstado");

	swal({
		title: '¿Está seguro de eliminar el estado?',
		text: "¡Puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, eliminar estado'
	}).then(function(result){

		if(result.value){

			window.location = "index.php?ruta=estados-actividades&idEstado="+idEstado;
		}
	});

});