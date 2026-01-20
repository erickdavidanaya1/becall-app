<?php
// Página principal - Lista de tareas

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Logger.php';
require_once __DIR__ . '/../includes/Webhook.php';
require_once __DIR__ . '/../includes/Tarea.php';

$tareaObj = new Tarea();
$tareas = $tareaObj->listar();

// Mensajes de confirmación
$mensaje = '';
if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case 'creada':
            $mensaje = '<div class="alert alert-success">✓ Tarea creada exitosamente</div>';
            break;
        case 'actualizada':
            $mensaje = '<div class="alert alert-success">✓ Tarea actualizada exitosamente</div>';
            break;
        case 'eliminada':
            $mensaje = '<div class="alert alert-success">✓ Tarea eliminada exitosamente</div>';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
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
                <a href="index.php" class="active">Lista</a>
                <a href="crear.php">Nueva Tarea</a>
                <a href="configuracion.php">Config</a>
                <a href="logs.php">Logs</a>
            </nav>

            <div class="logos-track">
                <img src="assets/solvo.png" alt="Logo 1">
                <img src="assets/becall.png" alt="Logo 2">
            </div>
        </header>

        <?= $mensaje ?>

        <div class="card">
            <h2>Mis Tareas (<?= count($tareas) ?>)</h2>
            
            <?php if (empty($tareas)): ?>
                <p class="empty-state">No hay tareas. <a href="crear.php">Crea tu primera tarea</a></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Fecha Vencimiento</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['id']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($t['titulo']) ?></strong>
                                <?php if (!empty($t['descripcion'])): ?>
                                    <br><small><?= htmlspecialchars(substr($t['descripcion'], 0, 60)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= str_replace(' ', '-', $t['estado']) ?>">
                                    <?= ucfirst($t['estado']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($t['fecha_vencimiento'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></td>
                            <td class="actions">
                                <a href="editar.php?id=<?= $t['id'] ?>" class="btn btn-edit">✏️ Editar</a>
                                <a href="eliminar.php?id=<?= $t['id'] ?>" 
                                   class="btn btn-delete" 
                                   onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                    🗑️ Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card info">
            <h3>📊 Estadísticas</h3>
            <?php
            $stats = [
                'pendiente' => 0,
                'en progreso' => 0,
                'completada' => 0
            ];
            foreach ($tareas as $t) {
                $stats[$t['estado']]++;
            }
            ?>
            <p>
                <span class="badge badge-pendiente">Pendientes: <?= $stats['pendiente'] ?></span>
                <span class="badge badge-en-progreso">En Progreso: <?= $stats['en progreso'] ?></span>
                <span class="badge badge-completada">Completadas: <?= $stats['completada'] ?></span>
            </p>
        </div>
    </div>
</body>
</html>