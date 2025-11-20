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

$(document).on("click", ".btnEditarGasto", function(){

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

$(document).on("click", ".btnEliminarGasto", function(){

	var idGasto = $(this).attr("idGasto");
	var codigoGasto = $(this).attr("codigoGasto");
	var conceptoGasto = $(this).attr("conceptoGasto");

	swal({

		title: '¿Está seguro de eliminar el gasto: "'+conceptoGasto+'"?',
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

            // Limpiar tabla y cards
            $(".tablas1 tbody").empty();
            $(".cards-gastos").empty();

            if(respuesta.length == 0){
                $(".tablas1 tbody").html('<tr><td colspan="8" class="text-center">No se encontraron gastos con los filtros seleccionados</td></tr>');
                $(".cards-gastos").html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> No se encontraron gastos con los filtros seleccionados</div>');
            } else {

                // Llenar tabla con resultados
                respuesta.forEach(function(gasto, index){

                    // Formatear fecha
                    var fecha = gasto.fecha ? new Date(gasto.fecha + 'T00:00:00') : null;
                    var fechaFormateada = fecha ? ("0" + fecha.getDate()).slice(-2) + "/" +
                                          ("0" + (fecha.getMonth() + 1)).slice(-2) + "/" +
                                          fecha.getFullYear() : '-';

                    // Verificar si es hoy
                    var hoy = new Date();
                    var esHoy = fecha && fecha.toDateString() === hoy.toDateString();
                    var rowStyle = esHoy ? 'style="border-left: 6px solid #28a745 !important; background-color: #f0f9f4; box-shadow: inset 6px 0 0 #28a745;"' : '';

                    // Categoría badge
                    var categoriaBadge = '';
                    if(gasto.categoria_nombre){
                        categoriaBadge = '<span class="badge" style="background-color: '+gasto.categoria_color+'">'+gasto.categoria_nombre+'</span>';
                    } else {
                        categoriaBadge = '-';
                    }

                    // Formatear monto
                    var monto = gasto.monto ? '$' + parseFloat(gasto.monto).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-';

                    // Proveedor
                    var proveedor = gasto.proveedor_nombre ? gasto.proveedor_nombre : '-';

                    // Imagen
                    var imagen = '';
                    if(gasto.imagen_comprobante && gasto.imagen_comprobante != ''){
                        imagen = '<img src="'+gasto.imagen_comprobante+'" class="img-thumbnail img-comprobante-clickeable" width="40px" style="cursor: pointer;" data-imagen="'+gasto.imagen_comprobante+'" data-idgasto="'+gasto.id+'" data-concepto="'+gasto.concepto+'">';
                    } else {
                        imagen = '<img src="vistas/img/gastos/default/sin-imagen.png" class="img-thumbnail img-comprobante-clickeable" width="40px" style="cursor: pointer;" data-imagen="" data-idgasto="'+gasto.id+'" data-concepto="'+gasto.concepto+'">';
                    }

                    // Crear fila
                    var fila = '<tr '+rowStyle+'>';

                    // Columna 1: Número
                    fila += '<td>'+(index+1)+'</td>';

                    // Columna 2: Concepto
                    fila += '<td>'+gasto.concepto+'</td>';

                    // Columna 3: Fecha
                    fila += '<td>'+fechaFormateada+'</td>';

                    // Columna 4: Monto
                    fila += '<td><strong>'+monto+'</strong></td>';

                    // Columna 5: Categoría
                    fila += '<td>'+categoriaBadge+'</td>';

                    // Columna 6: Proveedor
                    fila += '<td>'+proveedor+'</td>';

                    // Columna 7: Imagen
                    fila += '<td>'+imagen+'</td>';

                    // Columna 8: Acciones
                    fila += '<td>';
                    fila += '<div class="btn-group">';
                    fila += '<button class="btn btn-warning btnEditarGasto" idGasto="'+gasto.id+'" data-toggle="modal" data-target="#modalEditarGasto"><i class="fa fa-pencil"></i></button>';
                    fila += '<button class="btn btn-danger btnEliminarGasto" idGasto="'+gasto.id+'" codigoGasto="'+gasto.codigo+'" conceptoGasto="'+gasto.concepto+'"><i class="fa fa-times"></i></button>';
                    fila += '</div>';
                    fila += '</td>';

                    fila += '</tr>';

                    $(".tablas1 tbody").append(fila);

                    // CREAR CARD PARA MÓVIL
                    var claseHoy = esHoy ? ' gasto-hoy' : '';
                    var categoriaBadgeCard = gasto.categoria_nombre ?
                        '<span class="badge" style="background-color: '+gasto.categoria_color+'">'+gasto.categoria_nombre+'</span>' :
                        '<span class="text-muted">Sin categoría</span>';
                    var proveedorCard = gasto.proveedor_nombre ? gasto.proveedor_nombre : 'Sin proveedor';

                    var card = '<div class="card-gasto'+claseHoy+'">';

                    // Header con botones
                    card += '<div class="card-gasto-header">';
                    card += '<div class="btn-group">';
                    card += '<button class="btn btn-warning btn-sm btnEditarGasto" idGasto="'+gasto.id+'" data-toggle="modal" data-target="#modalEditarGasto">';
                    card += '<i class="fa fa-pencil"></i>';
                    card += '</button>';
                    card += '<button class="btn btn-danger btn-sm btnEliminarGasto" idGasto="'+gasto.id+'" codigoGasto="'+gasto.codigo+'" conceptoGasto="'+gasto.concepto+'">';
                    card += '<i class="fa fa-times"></i>';
                    card += '</button>';
                    card += '</div>';
                    card += '</div>';

                    // Concepto
                    card += '<div class="card-gasto-concepto">💰 '+gasto.concepto+'</div>';

                    // Contenido
                    card += '<div class="card-gasto-contenido">';

                    // Imagen
                    if(gasto.imagen_comprobante && gasto.imagen_comprobante != ''){
                        card += '<div class="card-gasto-imagen">';
                        card += '<img src="'+gasto.imagen_comprobante+'" class="img-comprobante-clickeable" style="cursor: pointer;" data-imagen="'+gasto.imagen_comprobante+'" data-idgasto="'+gasto.id+'" data-concepto="'+gasto.concepto+'">';
                        card += '</div>';
                    } else {
                        card += '<div class="card-gasto-imagen sin-imagen img-comprobante-clickeable" style="cursor: pointer;" data-imagen="" data-idgasto="'+gasto.id+'" data-concepto="'+gasto.concepto+'">';
                        card += '<i class="fa fa-image fa-2x"></i><br>Sin imagen';
                        card += '</div>';
                    }

                    // Detalles
                    card += '<div class="card-gasto-detalles">';
                    card += '<div class="card-gasto-fecha"><i class="fa fa-calendar"></i> '+fechaFormateada+'</div>';
                    card += '<div class="card-gasto-monto"><i class="fa fa-money"></i> '+monto+'</div>';
                    card += '<div class="card-gasto-categoria">'+categoriaBadgeCard+'</div>';
                    card += '<div class="card-gasto-proveedor"><i class="fa fa-user"></i> '+proveedorCard+'</div>';
                    card += '</div>';

                    card += '</div>';
                    card += '</div>';

                    $(".cards-gastos").append(card);
                });

            }

		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.error("Error en AJAX:", textStatus, errorThrown);
		}

	})

});