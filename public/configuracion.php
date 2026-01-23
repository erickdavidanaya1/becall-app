<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Logger.php';

$db = Database::getInstance();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $webhook_url = trim($_POST['webhook_url'] ?? '');
    
    if (!empty($webhook_url)) {
        $db->setConfig('webhook_url', $webhook_url);
        Logger::log("Configuración actualizada", "Webhook URL: $webhook_url");
        $mensaje = '<div class="alert alert-success">✓ Configuración guardada exitosamente</div>';
    } else {
        $mensaje = '<div class="alert alert-error">❌ La URL del webhook no puede estar vacía</div>';
    }
}

$webhook_url = $db->getConfig('webhook_url');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - ToDo List</title>
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
                <a href="configuracion.php" class="active">Config</a>
                <a href="logs.php">Logs</a>
            </nav>

            <div class="logos-track">
                <img src="assets/solvo.png" alt="Logo 1">
                <img src="assets/becall.png" alt="Logo 2">
            </div>
        </header>

        <?= $mensaje ?>

        <div class="card">
            <h2>⚙️ Configuración del Sistema</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="webhook_url">URL del Webhook</label>
                    <input type="url" 
                           id="webhook_url" 
                           name="webhook_url" 
                           required
                           value="<?= htmlspecialchars($webhook_url) ?>"
                           placeholder="https://webhook.site/tu-id-unico">
                    <small>
                        Esta URL recibirá notificaciones cada vez que se cree, actualice o elimine una tarea.
                        <br>
                        Puedes obtener una URL de prueba en <a href="https://webhook.site" target="_blank">webhook.site</a>
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Guardar Configuración</button>
                </div>
            </form>
        </div>

        <div class="card info">
            <h3>📡 Información del Webhook</h3>
            <p>
                Cada vez que se realice una acción (crear, editar o eliminar), se enviará un POST con formato JSON:
            </p>
            <pre>{
  "accion": "create|update|delete",
  "fecha_hora": "2026-01-19 14:30:00",
  "tarea": {
    "id": 1,
    "titulo": "Título de la tarea",
    "descripcion": "Descripción...",
    "estado": "pendiente",
    "fecha_creacion": "2026-01-19 14:00:00",
    "fecha_vencimiento": "2026-01-25"
  }
}</pre>
        </div>

        <div class="card">
            <h3>🗄️ Información de Base de Datos</h3>
            <p>
                <strong>Base de datos:</strong> <?= DB_NAME ?><br>
                <strong>Host:</strong> <?= DB_HOST ?><br>
                <strong>Charset:</strong> <?= DB_CHARSET ?>
            </p>
        </div>
    </div>
</body>
</html>