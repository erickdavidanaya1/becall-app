-- Creamos la tabla principal
-- Esta lleva el ID, las fechas y el estado de la tarea
CREATE TABLE Tarea_data (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ID autoincremental, la llave primaria
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- Se pone sola cuando creamos la tarea
    fecha_vencimiento DATE NOT NULL,  -- Hasta cuando hay que terminar la tarea
    estado ENUM('pendiente', 'en progreso', 'completada') NOT NULL DEFAULT 'pendiente',  -- Solo puede ser uno de estos 3 valores
    aux1 VARCHAR(50) DEFAULT ''  -- Campo extra
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Creamos la tabla que guarda el título y la descripción
CREATE TABLE Tarea_dataexten (
    id INT PRIMARY KEY,  -- Es el mismo ID que en Tarea_data
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,  
    aux1 VARCHAR(50) DEFAULT '',  -- campo auxiliar1
    FOREIGN KEY (id) REFERENCES Tarea_data(id) ON DELETE CASCADE  -- Si borramos una tarea, se borra también de aquí
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Creamos la tabla para guardar configuraciones de la app, por ahora solo guardamos la URL del webhook
CREATE TABLE config (
    clave VARCHAR(50) PRIMARY KEY,  -- nombre de la configuración
    valor TEXT NOT NULL,  -- valor de la configuración
    descripcion VARCHAR(255),  -- Para saber qué hace cada config
    aux1 VARCHAR(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Metemos la config del webhook con una URL
-- se puede cambiar desde la interfaz web
INSERT INTO config VALUES ('webhook_url', 'https://webhook.site/YOUR-UNIQUE-ID', 'URL del webhook', '');

-- insertamos 3 tareas de prueba para que no este vacia la app
INSERT INTO Tarea_data (fecha_vencimiento, estado) VALUES 
('2026-01-25', 'pendiente'),  -- vence pronto
('2026-01-30', 'en progreso'),  -- ya empezo
('2026-02-01', 'completada');  -- esta lista

-- titulo y descripción a esas 3 tareas
INSERT INTO Tarea_dataexten VALUES 
(1, 'Completar prueba técnica PHP', 'Desarrollar app Becall', ''),
(2, 'Revisar documentación', 'Leer requisitos', ''),
(3, 'Preparar presentación', 'Crear ejemplos', '');