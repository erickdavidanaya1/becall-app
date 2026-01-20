<?php
// public/eliminar.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Logger.php';
require_once __DIR__ . '/../includes/Webhook.php';
require_once __DIR__ . '/../includes/Tarea.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $tareaObj = new Tarea();
    $tareaObj->eliminar($id);
}

header('Location: index.php?accion=eliminada');
exit;