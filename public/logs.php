<?php
// public/logs.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Logger.php';

$logs = Logger::getLogs(200);
$mensaje = '';

if (isset($_GET['accion']) && $_GET['accion'] === 'limpiar') {
    Logger::clearLogs();
    header('Location: logs.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs del Sistema - ToDo List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="hero">
            <h1>Gestión de Tareas</h1>
            <nav>
                <a href="index.php">Lista</a>
                <a href="crear.php">Nueva Tarea</a>
                <a href="configuracion.php">Config</a>
                <a href="logs.php" class="active">Logs</a>
            </nav>

            <div class="logos-track">
                <img src="assets/solvo.png" alt="Logo 1">
                <img src="assets/becall.png" alt="Logo 2">
            </div>
        </header>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>📄 Registro de Actividad (Últimas <?= count($logs) ?> entradas)</h2>
                <?php if (!empty($logs)): ?>
                <a href="logs.php?accion=limpiar" 
                   class="btn btn-delete" 
                   onclick="return confirm('¿Estás seguro de limpiar todos los logs?')"
                   style="font-size: 0.9em;">
                    🗑️ Limpiar Logs
                </a>
                <?php endif; ?>
            </div>
            
            <?php if (empty($logs)): ?>
                <p class="empty-state">No hay registros de actividad</p>
            <?php else: ?>
                <div class="logs-container">
                    <?php foreach ($logs as $log): ?>
                        <div class="log-entry">
                            <pre><?= htmlspecialchars($log) ?></pre>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card info">
            <h3>ℹ️ Información sobre los Logs</h3>
            <p>
                El sistema registra automáticamente todas las acciones realizadas en la aplicación:
            </p>
            <ul>
                <li>✅ Creación de tareas</li>
                <li>✏️ Actualización de tareas</li>
                <li>🗑️ Eliminación de tareas</li>
                <li>📡 Envío de webhooks</li>
                <li>⚙️ Cambios de configuración</li>
            </ul>
            <p>
                <strong>Archivo de logs:</strong> <code><?= LOG_FILE ?></code>
            </p>
        </div>
    </div>
</body>
</html>