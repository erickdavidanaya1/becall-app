<?php
// public/crear.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Logger.php';
require_once __DIR__ . '/../includes/Webhook.php';
require_once __DIR__ . '/../includes/Tarea.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? '',
        'estado' => $_POST['estado'] ?? 'pendiente'
    ];
    
    if (empty($datos['titulo'])) {
        $error = 'El título es obligatorio';
    } elseif (empty($datos['fecha_vencimiento'])) {
        $error = 'La fecha de vencimiento es obligatoria';
    } else {
        $tareaObj = new Tarea();
        $resultado = $tareaObj->crear($datos);
        
        if ($resultado['success']) {
            header('Location: index.php?accion=creada');
            exit;
        } else {
            $error = $resultado['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Tarea - ToDo List</title>
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
                <a href="crear.php" class="active">Nueva Tarea</a>
                <a href="configuracion.php">Config</a>
                <a href="logs.php">Logs</a>
            </nav>

            <div class="logos-track">
                <img src="assets/solvo.png" alt="Logo 1">
                <img src="assets/becall.png" alt="Logo 2">
            </div>
        </header>

        <div class="card">
            <h2>➕ Crear Nueva Tarea</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" 
                           id="titulo" 
                           name="titulo" 
                           required 
                           maxlength="255"
                           value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                           placeholder="Ej: Completar informe mensual">
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" 
                              name="descripcion" 
                              rows="4"
                              placeholder="Detalles adicionales sobre la tarea..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_vencimiento">Fecha de Vencimiento *</label>
                        <input type="date" 
                               id="fecha_vencimiento" 
                               name="fecha_vencimiento" 
                               required
                               min="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($_POST['fecha_vencimiento'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="pendiente" <?= ($_POST['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>
                                Pendiente
                            </option>
                            <option value="en progreso" <?= ($_POST['estado'] ?? '') === 'en progreso' ? 'selected' : '' ?>>
                                En Progreso
                            </option>
                            <option value="completada" <?= ($_POST['estado'] ?? '') === 'completada' ? 'selected' : '' ?>>
                                Completada
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Guardar Tarea</button>
                    <a href="index.php" class="btn btn-secondary">❌ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>