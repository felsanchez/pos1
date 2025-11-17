<?php

class ControladorLogs {

    /**
     * Muestra la vista principal de logs
     */
    public static function ctrMostrarLogs() {
        // Solo accesible para usuarios autenticados
        if (!isset($_SESSION["validarSesion"]) || $_SESSION["validarSesion"] != "ok") {
            return [];
        }

        return Logger::getLogFiles();
    }

    /**
     * Obtiene logs filtrados
     */
    public static function ctrObtenerLogs($fecha = null, $nivel = null, $limite = 100) {
        // Solo accesible para usuarios autenticados
        if (!isset($_SESSION["validarSesion"]) || $_SESSION["validarSesion"] != "ok") {
            return [];
        }

        try {
            return Logger::readLogs($fecha, $nivel, $limite);
        } catch (Exception $e) {
            Logger::error('Error al obtener logs', ['exception' => $e]);
            return [];
        }
    }

    /**
     * Obtiene estadísticas de logs
     */
    public static function ctrObtenerEstadisticas($fecha = null) {
        // Solo accesible para usuarios autenticados
        if (!isset($_SESSION["validarSesion"]) || $_SESSION["validarSesion"] != "ok") {
            return [];
        }

        try {
            return Logger::getStats($fecha);
        } catch (Exception $e) {
            Logger::error('Error al obtener estadísticas de logs', ['exception' => $e]);
            return [];
        }
    }

    /**
     * Limpia logs antiguos
     */
    public static function ctrLimpiarLogsAntiguos($dias = 30) {
        // Solo accesible para usuarios autenticados y administradores
        if (!isset($_SESSION["validarSesion"]) || $_SESSION["validarSesion"] != "ok") {
            return 0;
        }

        if (!isset($_SESSION["perfil"]) || $_SESSION["perfil"] != "Administrador") {
            return 0;
        }

        try {
            $deleted = Logger::cleanOldLogs($dias);
            Logger::info("Se limpiaron {$deleted} archivos de log antiguos", [
                'dias' => $dias,
                'archivos_eliminados' => $deleted
            ]);
            return $deleted;
        } catch (Exception $e) {
            Logger::error('Error al limpiar logs antiguos', ['exception' => $e]);
            return 0;
        }
    }
}