<?php
// API REST para gestionar tareas
// Endpoints: GET, POST, PUT, DELETE /api/tareas

// Headers para la API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Si es una petición OPTIONS (preflight), respondemos OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cargar todas las dependencias
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Logger.php';
require_once __DIR__ . '/../includes/Webhook.php';
require_once __DIR__ . '/../includes/Tarea.php';

$tarea = new Tarea();
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Extraer el ID de la URL si existe (ej: /api/tareas/5)
preg_match('/\/api\/tareas\/(\d+)/', $path, $matches);
$id = $matches[1] ?? null;

// Respuesta base - SIEMPRE incluye el saludo (requisito de la prueba)
$response = ['saludo' => 'hola'];

try {
    switch ($method) {
        case 'GET':
            // GET /api/tareas/{id} - Obtener una tarea específica
            if ($id) {
                $resultado = $tarea->obtenerPorId($id);
                if ($resultado) {
                    $response['data'] = $resultado;
                    http_response_code(200);
                } else {
                    $response['error'] = 'Tarea no encontrada';
                    http_response_code(404);
                }
            } 
            // GET /api/tareas - Listar todas las tareas
            else {
                $filtros = [];
                // Permitir filtrar por estado (?estado=pendiente)
                if (isset($_GET['estado'])) {
                    $filtros['estado'] = $_GET['estado'];
                }
                $response['data'] = $tarea->listar($filtros);
                http_response_code(200);
            }
            break;
            
        case 'POST':
            // POST /api/tareas - Crear nueva tarea
            $datos = json_decode(file_get_contents('php://input'), true);
            
            // Validar campos obligatorios
            if (!isset($datos['titulo']) || !isset($datos['fecha_vencimiento'])) {
                $response['error'] = 'Faltan campos obligatorios: titulo, fecha_vencimiento';
                http_response_code(400);
                break;
            }
            
            $resultado = $tarea->crear($datos);
            
            if ($resultado['success']) {
                $response['data'] = $resultado['tarea'];
                $response['mensaje'] = 'Tarea creada exitosamente';
                http_response_code(201);  // Created
            } else {
                $response['error'] = $resultado['error'];
                http_response_code(500);
            }
            break;
            
        case 'PUT':
            // PUT /api/tareas/{id} - Actualizar tarea existente
            if (!$id) {
                $response['error'] = 'ID de tarea no especificado';
                http_response_code(400);
                break;
            }
            
            $datos = json_decode(file_get_contents('php://input'), true);
            
            // Validar campos obligatorios
            if (!isset($datos['titulo']) || !isset($datos['fecha_vencimiento']) || !isset($datos['estado'])) {
                $response['error'] = 'Faltan campos obligatorios: titulo, fecha_vencimiento, estado';
                http_response_code(400);
                break;
            }
            
            $resultado = $tarea->actualizar($id, $datos);
            
            if ($resultado['success']) {
                $response['data'] = $resultado['tarea'];
                $response['mensaje'] = 'Tarea actualizada exitosamente';
                http_response_code(200);
            } else {
                $response['error'] = $resultado['error'];
                http_response_code(500);
            }
            break;
            
        case 'DELETE':
            // DELETE /api/tareas/{id} - Eliminar tarea
            if (!$id) {
                $response['error'] = 'ID de tarea no especificado';
                http_response_code(400);
                break;
            }
            
            $resultado = $tarea->eliminar($id);
            
            if ($resultado['success']) {
                $response['mensaje'] = 'Tarea eliminada exitosamente';
                http_response_code(200);
            } else {
                $response['error'] = $resultado['error'];
                http_response_code(500);
            }
            break;
            
        default:
            $response['error'] = 'Método no permitido';
            http_response_code(405);
            break;
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
}

// Devolver la respuesta en JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);