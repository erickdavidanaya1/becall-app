<?php
// Configuración de la conexión a la base de datos

define('DB_HOST', 'localhost');      // El servidor MySQL
define('DB_NAME', 'becall_app');     // Nombre de nuestra base de datos
define('DB_USER', 'root');           // Usuario de MySQL (por defecto en XAMPP es root)
define('DB_PASS', '');               // Contraseña (vacía por defecto en XAMPP)
define('DB_CHARSET', 'utf8mb4');     

// Configuramos la zona horaria para Bogotá
date_default_timezone_set('America/Bogota');