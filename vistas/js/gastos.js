console.log("✅ Archivo gastos.js cargado correctamente");

$(document).ready(function(){
    console.log("✅ jQuery está funcionando en gastos.js");

    // Inicializar DataTable para tabla de gastos (solo si existe en la página)
    if($('#tablaGastos').length > 0) {
        var tablaGastos = $('#tablaGastos').DataTable({
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
    }
});

/*=============================================
EDITAR GASTO
=============================================*/

$(".tablas1").on("click", ".btnEditarGasto", function(){

	var idGasto = $(this).attr("idGasto");
    console.log("ID Gasto: " + idGasto);

	// Rellenar el input hidden
    $('#modalEditarGasto input[name="idGasto"]').val(idGasto);

	var datos = new FormData();
	datos.append("idGasto", idGasto);

	$.ajax({

		url:"ajax/gastos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

            console.log("Respuesta AJAX:", respuesta);

			$("#editarConceptoGasto").val(respuesta["concepto"]);
            $("#editarMontoGasto").val(respuesta["monto"]);
            $("#editarFechaGasto").val(respuesta["fecha"]);
            $("#editarCategoriaGasto").val(respuesta["id_categoria_gasto"]);
            $("#editarProveedorGasto").val(respuesta["id_proveedor"]);
            $("#editarMetodoPagoGasto").val(respuesta["metodo_pago"]);
            $("#editarNumeroComprobante").val(respuesta["numero_comprobante"]);
            $("#editarEstadoGasto").val(respuesta["estado"]);
            $("#editarNotasGasto").val(respuesta["notas"]);
            $("#imagenActual").val(respuesta["imagen_comprobante"]);

            // Mostrar preview de imagen si existe
            if(respuesta["imagen_comprobante"] != "" && respuesta["imagen_comprobante"] != null){
                $("#previsualizarImagen").html('<img src="'+respuesta["imagen_comprobante"]+'" class="img-thumbnail img-ampliar-gasto" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;">');
            } else {
                $("#previsualizarImagen").html('');
            }

		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.error("Error en AJAX:", textStatus, errorThrown);
		}

	})

});

/*=============================================
ELIMINAR GASTO
=============================================*/

$(".tablas1").on("click", ".btnEliminarGasto", function(){

	var idGasto = $(this).attr("idGasto");
	var codigoGasto = $(this).attr("codigoGasto");

	swal({

		title: '¿Está seguro de eliminar el gasto '+codigoGasto+'?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, eliminar gasto!'
	}).then((result)=>{

		if(result.value){

			window.location = "index.php?ruta=gastos&idGasto="+idGasto;
		}
	})
})

/*=============================================
EDITAR CATEGORÍA DE GASTO
=============================================*/

$("#modalGestionarCategorias").on("click", ".btnEditarCategoriaGasto", function(){

	var idCategoria = $(this).attr("idCategoria");
    console.log("ID Categoría: " + idCategoria);

	// Rellenar el input hidden
    $('#modalEditarCategoria input[name="idCategoriaGasto"]').val(idCategoria);

	var datos = new FormData();
	datos.append("idCategoria", idCategoria);

	$.ajax({

		url:"ajax/categorias_gastos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

            console.log("Respuesta AJAX Categoría:", respuesta);

			$("#editarNombreCategoriaGasto").val(respuesta["nombre"]);
            $("#editarColorCategoriaGasto").val(respuesta["color"]);
            $("#editarDescripcionCategoriaGasto").val(respuesta["descripcion"]);

		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.error("Error en AJAX:", textStatus, errorThrown);
		}

	})

});

/*=============================================
ELIMINAR CATEGORÍA DE GASTO
=============================================*/

$("#modalGestionarCategorias").on("click", ".btnEliminarCategoriaGasto", function(){

	var idCategoria = $(this).attr("idCategoria");
	var nombreCategoria = $(this).attr("nombreCategoria");

	swal({

		title: '¿Está seguro de eliminar la categoría "'+nombreCategoria+'"?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, eliminar categoría!'
	}).then((result)=>{

		if(result.value){

			window.location = "index.php?ruta=gastos&idCategoriaGasto="+idCategoria;
		}
	})
})

/*=============================================
VER COMPROBANTE DESDE TABLA
=============================================*/

$(".tablas1").on("click", ".img-comprobante-clickeable", function(){

	var imagen = $(this).attr("src");

    $("#imagenComprobante").attr("src", imagen);
    $("#modalVerComprobante").modal("show");

});

/*=============================================
FILTRAR GASTOS
=============================================*/

$("#btnFiltrarGastos").on("click", function(){

	var fechaInicio = $("#filtroFechaInicio").val();
	var fechaFin = $("#filtroFechaFin").val();
	var categoria = $("#filtroCategoria").val();
	var proveedor = $("#filtroProveedor").val();

	console.log("Filtros:", fechaInicio, fechaFin, categoria, proveedor);

	var datos = new FormData();
	datos.append("accion", "filtrarGastos");
	datos.append("fechaInicio", fechaInicio);
	datos.append("fechaFin", fechaFin);
	datos.append("categoria", categoria);
	datos.append("proveedor", proveedor);

	$.ajax({

		url:"ajax/gastos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

            console.log("Gastos filtrados:", respuesta);

            // Limpiar tabla
            $(".tablas1 tbody").empty();

            if(respuesta.length == 0){
                $(".tablas1 tbody").html('<tr><td colspan="4" class="text-center">No se encontraron gastos con los filtros seleccionados</td></tr>');
            } else {

                // Llenar tabla con resultados
                respuesta.forEach(function(gasto, index){

                    // Formatear fecha
                    var fecha = new Date(gasto.fecha);
                    var fechaFormateada = ("0" + fecha.getDate()).slice(-2) + "/" +
                                          ("0" + (fecha.getMonth() + 1)).slice(-2) + "/" +
                                          fecha.getFullYear();

                    // Categoría badge
                    var categoriaBadge = '';
                    if(gasto.categoria_nombre){
                        categoriaBadge = '<span class="badge" style="background-color: '+gasto.categoria_color+'">'+gasto.categoria_nombre+'</span>';
                    } else {
                        categoriaBadge = '-';
                    }

                    // Crear fila
                    var fila = '<tr>';

                    // Columna 1: Número
                    fila += '<td>'+(index+1)+'</td>';

                    // Columna 2: Concepto
                    fila += '<td>'+gasto.concepto+'</td>';

                    // Columna 3: Categoría
                    fila += '<td>'+categoriaBadge+'</td>';

                    // Columna 4: Acciones
                    fila += '<td>';
                    fila += '<div class="btn-group">';
                    fila += '<button class="btn btn-warning btnEditarGasto" idGasto="'+gasto.id+'" data-toggle="modal" data-target="#modalEditarGasto"><i class="fa fa-pencil"></i></button>';
                    fila += '<button class="btn btn-danger btnEliminarGasto" idGasto="'+gasto.id+'" codigoGasto="'+gasto.codigo+'"><i class="fa fa-times"></i></button>';
                    fila += '</div>';
                    fila += '</td>';

                    fila += '</tr>';

                    $(".tablas1 tbody").append(fila);
                });

            }

		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.error("Error en AJAX:", textStatus, errorThrown);
		}

	})

});