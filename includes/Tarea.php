<?php
// Clase para manejar todas las operaciones de tareas (CRUD)

class Tarea {
    private $db;
    private $webhook;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->webhook = new Webhook();
    }

    // CREAR una nueva tarea
    public function crear($datos) {
        $conn = null;

        try {
            $conn = $this->db->getConnection();
            $conn->beginTransaction(); // Iniciamos una transacción

            // Insertamos en Tarea_data
            $sql = "INSERT INTO Tarea_data (fecha_vencimiento, estado) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $datos['fecha_vencimiento'],
                $datos['estado'] ?? 'pendiente'
            ]);

            // ID recién creado
            $id = $conn->lastInsertId();

            // Insertamos en Tarea_dataexten con el mismo ID
            $sql = "INSERT INTO Tarea_dataexten (id, titulo, descripcion) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $id,
                $datos['titulo'],
                $datos['descripcion'] ?? ''
            ]);

            $conn->commit(); // Confirmamos la transacción

            // Obtenemos la tarea completa
            $tarea = $this->obtenerPorId($id);

            // Log de creación
            Logger::log("Tarea creada", "ID: $id, Título: {$datos['titulo']}");

            // Webhook (no debe romper el flujo si falla)
            $ok = $this->webhook->enviar('create', $tarea);
            if (!$ok) {
                Logger::log("Webhook fallo", "Acción: create, ID: {$tarea['id']}");
            }

            return ['success' => true, 'id' => (int)$id, 'tarea' => $tarea];

        } catch (Exception $e) {
            if ($conn && $conn->inTransaction()) {
                $conn->rollBack();
            }

            Logger::log("Error al crear tarea", $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // LISTAR todas las tareas (con filtros opcionales)
    public function listar($filtros = []) {
        $sql = "SELECT 
                    td.id,
                    td.fecha_creacion,
                    td.fecha_vencimiento,
                    td.estado,
                    tde.titulo,
                    tde.descripcion
                FROM Tarea_data td
                LEFT JOIN Tarea_dataexten tde ON td.id = tde.id";

        $where = [];
        $params = [];

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $where[] = "td.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY td.fecha_creacion DESC";

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    // OBTENER una tarea por su ID
    public function obtenerPorId($id) {
        $sql = "SELECT 
                    td.id,
                    td.fecha_creacion,
                    td.fecha_vencimiento,
                    td.estado,
                    tde.titulo,
                    tde.descripcion
                FROM Tarea_data td
                LEFT JOIN Tarea_dataexten tde ON td.id = tde.id
                WHERE td.id = ?";

        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    // ACTUALIZAR una tarea existente
    public function actualizar($id, $datos) {
        $conn = null;

        try {
            $conn = $this->db->getConnection();
            $conn->beginTransaction();

            // Actualizamos Tarea_data
            $sql = "UPDATE Tarea_data 
                    SET fecha_vencimiento = ?, estado = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $datos['fecha_vencimiento'],
                $datos['estado'],
                $id
            ]);

            // Actualizamos Tarea_dataexten
            $sql = "UPDATE Tarea_dataexten 
                    SET titulo = ?, descripcion = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $datos['titulo'],
                $datos['descripcion'] ?? '',
                $id
            ]);

            $conn->commit();

            // Obtenemos la tarea actualizada
            $tarea = $this->obtenerPorId($id);

            Logger::log("Tarea actualizada", "ID: $id, Título: {$datos['titulo']}");

            // Webhook (no debe romper el flujo si falla)
            $ok = $this->webhook->enviar('update', $tarea);
            if (!$ok) {
                Logger::log("Webhook fallo", "Acción: update, ID: {$tarea['id']}");
            }

            return ['success' => true, 'tarea' => $tarea];

        } catch (Exception $e) {
            if ($conn && $conn->inTransaction()) {
                $conn->rollBack();
            }

            Logger::log("Error al actualizar tarea", $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ELIMINAR una tarea
    public function eliminar($id) {
        try {
            // Obtenemos la tarea antes de borrarla (para el webhook)
            $tarea = $this->obtenerPorId($id);

            if (!$tarea) {
                return ['success' => false, 'error' => 'Tarea no encontrada'];
            }

            // Borramos de Tarea_data (CASCADE debería borrar también Tarea_dataexten)
            $sql = "DELETE FROM Tarea_data WHERE id = ?";
            $this->db->query($sql, [$id]);

            Logger::log("Tarea eliminada", "ID: $id, Título: {$tarea['titulo']}");

            // Webhook (no debe romper el flujo si falla)
            $ok = $this->webhook->enviar('delete', $tarea);
            if (!$ok) {
                Logger::log("Webhook fallo", "Acción: delete, ID: {$tarea['id']}");
            }

            return ['success' => true];

        } catch (Exception $e) {
            Logger::log("Error al eliminar tarea", $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
