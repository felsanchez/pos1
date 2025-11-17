<?php

require_once "../controladores/logs.controlador.php";
require_once "../modelos/logger.php";

class AjaxLogs {

    /**
     * Obtener logs
     */
    public function ajaxObtenerLogs() {
        $fecha = isset($_POST["fecha"]) ? $_POST["fecha"] : null;
        $nivel = isset($_POST["nivel"]) ? $_POST["nivel"] : null;
        $limite = isset($_POST["limite"]) ? intval($_POST["limite"]) : 100;

        $logs = ControladorLogs::ctrObtenerLogs($fecha, $nivel, $limite);

        echo json_encode($logs);
    }

    /**
     * Obtener estadísticas
     */
    public function ajaxObtenerEstadisticas() {
        $fecha = isset($_POST["fecha"]) ? $_POST["fecha"] : null;

        $stats = ControladorLogs::ctrObtenerEstadisticas($fecha);

        echo json_encode($stats);
    }

    /**
     * Obtener lista de archivos de log
     */
    public function ajaxObtenerArchivos() {
        $archivos = ControladorLogs::ctrMostrarLogs();

        echo json_encode($archivos);
    }

    /**
     * Limpiar logs antiguos
     */
    public function ajaxLimpiarLogs() {
        $dias = isset($_POST["dias"]) ? intval($_POST["dias"]) : 30;

        $deleted = ControladorLogs::ctrLimpiarLogsAntiguos($dias);

        echo json_encode([
            'success' => true,
            'deleted' => $deleted
        ]);
    }
}

// Procesar peticiones
if (isset($_POST["accion"])) {
    $ajax = new AjaxLogs();

    switch ($_POST["accion"]) {
        case 'obtener_logs':
            $ajax->ajaxObtenerLogs();
            break;

        case 'obtener_estadisticas':
            $ajax->ajaxObtenerEstadisticas();
            break;

        case 'obtener_archivos':
            $ajax->ajaxObtenerArchivos();
            break;

        case 'limpiar_logs':
            $ajax->ajaxLimpiarLogs();
            break;
    }
}