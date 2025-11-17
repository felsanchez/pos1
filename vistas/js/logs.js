/*=============================================
SISTEMA DE LOGS
=============================================*/

$(document).ready(function() {
    // Cargar logs al iniciar
    cargarLogs();
    cargarEstadisticas();

    // Botón filtrar
    $("#btn-filtrar").click(function() {
        cargarLogs();
        cargarEstadisticas();
    });

    // Botón refrescar
    $("#btn-refrescar").click(function() {
        cargarLogs();
        cargarEstadisticas();
    });

    // Botón limpiar logs antiguos
    $("#btn-limpiar").click(function() {
        swal({
            title: "¿Está seguro?",
            text: "Se eliminarán todos los logs con más de 30 días",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sí, limpiar"
        }).then(function(result) {
            if (result.value) {
                limpiarLogsAntiguos();
            }
        });
    });

    // Auto-refresh cada 30 segundos
    setInterval(function() {
        cargarEstadisticas();
    }, 30000);
});

/*=============================================
CARGAR LOGS
=============================================*/
function cargarLogs() {
    var fecha = $("#filtro-fecha").val();
    var nivel = $("#filtro-nivel").val();
    var limite = $("#filtro-limite").val();

    $("#tbody-logs").html('<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando logs...</td></tr>');

    var datos = new FormData();
    datos.append("accion", "obtener_logs");
    datos.append("fecha", fecha);
    datos.append("nivel", nivel);
    datos.append("limite", limite);

    $.ajax({
        url: "ajax/logs.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            if (respuesta.length === 0) {
                $("#tbody-logs").html('<tr><td colspan="6" class="text-center">No hay logs para mostrar</td></tr>');
                return;
            }

            var html = '';
            respuesta.forEach(function(log) {
                var nivelClass = getNivelClass(log.level);
                var nivelBadge = getNivelBadge(log.level);

                html += '<tr>';
                html += '<td><small>' + log.timestamp + '</small></td>';
                html += '<td>' + nivelBadge + '</td>';
                html += '<td>' + escapeHtml(log.message) + '</td>';
                html += '<td><small>' + escapeHtml(log.user) + '</small></td>';
                html += '<td><small>' + escapeHtml(log.ip) + '</small></td>';
                html += '<td><button class="btn btn-info btn-xs btn-ver-detalle" data-log=\'' + JSON.stringify(log) + '\'><i class="fa fa-eye"></i></button></td>';
                html += '</tr>';
            });

            $("#tbody-logs").html(html);

            // Event listener para ver detalles
            $(".btn-ver-detalle").click(function() {
                var log = JSON.parse($(this).attr("data-log"));
                mostrarDetalleLog(log);
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar logs:", textStatus, errorThrown);
            $("#tbody-logs").html('<tr><td colspan="6" class="text-center text-danger">Error al cargar logs</td></tr>');
        }
    });
}

/*=============================================
CARGAR ESTADÍSTICAS
=============================================*/
function cargarEstadisticas() {
    var fecha = $("#filtro-fecha").val();

    var datos = new FormData();
    datos.append("accion", "obtener_estadisticas");
    datos.append("fecha", fecha);

    $.ajax({
        url: "ajax/logs.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            $("#stat-total").text(respuesta.total || 0);
            $("#stat-errors").text(respuesta.errors || 0);
            $("#stat-warnings").text(respuesta.warnings || 0);
            $("#stat-info").text(respuesta.info || 0);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar estadísticas:", textStatus, errorThrown);
        }
    });
}

/*=============================================
LIMPIAR LOGS ANTIGUOS
=============================================*/
function limpiarLogsAntiguos() {
    var datos = new FormData();
    datos.append("accion", "limpiar_logs");
    datos.append("dias", 30);

    $.ajax({
        url: "ajax/logs.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            if (respuesta.success) {
                swal({
                    type: "success",
                    title: "Logs limpiados",
                    text: "Se eliminaron " + respuesta.deleted + " archivo(s) de log",
                    showConfirmButton: true
                }).then(function() {
                    cargarLogs();
                    cargarEstadisticas();
                });
            } else {
                swal({
                    type: "error",
                    title: "Error",
                    text: "No se pudieron limpiar los logs"
                });
            }
        },
        error: function() {
            swal({
                type: "error",
                title: "Error",
                text: "Error al comunicarse con el servidor"
            });
        }
    });
}

/*=============================================
MOSTRAR DETALLE DEL LOG
=============================================*/
function mostrarDetalleLog(log) {
    var html = '<div class="row">';

    // Información básica
    html += '<div class="col-md-6">';
    html += '<dl class="dl-horizontal">';
    html += '<dt>Fecha/Hora:</dt><dd>' + log.timestamp + '</dd>';
    html += '<dt>Nivel:</dt><dd>' + getNivelBadge(log.level) + '</dd>';
    html += '<dt>Usuario:</dt><dd>' + escapeHtml(log.user) + '</dd>';
    html += '<dt>IP:</dt><dd>' + escapeHtml(log.ip) + '</dd>';
    html += '<dt>URL:</dt><dd><small>' + escapeHtml(log.url) + '</small></dd>';
    html += '<dt>Método:</dt><dd>' + escapeHtml(log.method) + '</dd>';
    html += '</dl>';
    html += '</div>';

    html += '<div class="col-md-6">';
    html += '<h4>Mensaje:</h4>';
    html += '<p>' + escapeHtml(log.message) + '</p>';
    html += '</div>';

    html += '</div>';

    // Contexto
    if (log.context && Object.keys(log.context).length > 0) {
        html += '<hr>';
        html += '<h4>Contexto:</h4>';
        html += '<pre>' + JSON.stringify(log.context, null, 2) + '</pre>';
    }

    // Excepción
    if (log.exception) {
        html += '<hr>';
        html += '<h4 class="text-danger">Excepción:</h4>';
        html += '<dl class="dl-horizontal">';
        html += '<dt>Mensaje:</dt><dd>' + escapeHtml(log.exception.message) + '</dd>';
        html += '<dt>Código:</dt><dd>' + log.exception.code + '</dd>';
        html += '<dt>Archivo:</dt><dd><small>' + escapeHtml(log.exception.file) + '</small></dd>';
        html += '<dt>Línea:</dt><dd>' + log.exception.line + '</dd>';
        html += '</dl>';
        html += '<h5>Stack Trace:</h5>';
        html += '<pre style="max-height: 300px; overflow-y: auto;">' + escapeHtml(log.exception.trace) + '</pre>';
    }

    $("#modal-body-log").html(html);
    $("#modal-detalle-log").modal("show");
}

/*=============================================
OBTENER CLASE CSS SEGÚN NIVEL
=============================================*/
function getNivelClass(nivel) {
    switch(nivel) {
        case 'ERROR': return 'danger';
        case 'WARNING': return 'warning';
        case 'INFO': return 'info';
        case 'DEBUG': return 'default';
        default: return 'default';
    }
}

/*=============================================
OBTENER BADGE SEGÚN NIVEL
=============================================*/
function getNivelBadge(nivel) {
    var clase = getNivelClass(nivel);
    return '<span class="label label-' + clase + '">' + nivel + '</span>';
}

/*=============================================
ESCAPAR HTML
=============================================*/
function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}