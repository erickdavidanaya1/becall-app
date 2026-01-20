<?php
// Sistema de logs - guarda todas las acciones en un archivo

class Logger {
    private static $logFile = LOG_FILE;
    
    // Registrar una acción en el log
    public static function log($accion, $info_adicional = '') {
        $fecha_hora = date('Y-m-d H:i:s');
        $mensaje = "[$fecha_hora] Acción: $accion";
        
        // Si hay info adicional, la agregamos
        if (!empty($info_adicional)) {
            // Si es un array, lo convertimos a JSON
            if (is_array($info_adicional)) {
                $info_adicional = json_encode($info_adicional, JSON_UNESCAPED_UNICODE);
            }
            $mensaje .= " | Info: $info_adicional";
        }
        
        $mensaje .= PHP_EOL;  // Salto de línea
        
        // Escribir en el archivo
        file_put_contents(self::$logFile, $mensaje, FILE_APPEND);
    }
    
    // Leer los últimos logs
    public static function getLogs($limit = 100) {
        if (!file_exists(self::$logFile)) {
            return [];
        }
        
        // Leer todas las líneas del archivo
        $lines = file(self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Devolver las últimas N líneas, más recientes primero
        return array_slice(array_reverse($lines), 0, $limit);
    }
    
    // Limpiar el archivo de logs
    public static function clearLogs() {
        if (file_exists(self::$logFile)) {
            file_put_contents(self::$logFile, '');
        }
    }
}