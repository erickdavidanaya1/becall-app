<?php
// Configuración general de la aplicación

// Ruta base del proyecto
define('BASE_PATH', dirname(__DIR__));

// Carpeta donde se guardan los logs
define('LOG_PATH', BASE_PATH . '/logs');

// Archivo de log principal
define('LOG_FILE', LOG_PATH . '/app.log');

// Si no existe la carpeta logs, la creamos
if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

// 👉 PASO 3 COMPLETO: asegurar que exista el archivo app.log
if (!file_exists(LOG_FILE)) {
    file_put_contents(LOG_FILE, '');
}

// URL base de la aplicación
define('BASE_URL', 'http://localhost/becall-app');

// Mostrar errores (útil para la prueba técnica)
error_reporting(E_ALL);
ini_set('display_errors', 1);
