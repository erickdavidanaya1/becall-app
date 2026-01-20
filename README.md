# ToDo List - Aplicación de Gestión de Tareas

Aplicación web desarrollada en PHP para gestionar tareas con operaciones CRUD, API REST y notificaciones mediante Webhooks.

##  Características

-  **CRUD completo** de tareas (Crear, Leer, Actualizar, Eliminar)
-  **API REST** con endpoints JSON
-  **Webhooks** para notificar acciones
-  **Sistema de Logs** en archivo local
-  **Base de datos** dividida en dos tablas según requisitos
-  **Interfaz web** moderna y responsive

##  Requisitos Técnicos

- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Apache con mod_rewrite
- Extensión PHP cURL (para webhooks)
- Extensión PHP PDO MySQL

##  Instalación

### 1. Clonar o descargar el proyecto

```bash
# Si es un repositorio Git
git clone <url-del-repositorio> todo-app
cd todo-app

# O descomprimir el archivo ZIP en la carpeta del servidor web
```

### 2. Configurar el servidor web

Coloca el proyecto en tu directorio web:
- **XAMPP**: `C:\xampp\htdocs\todo-app`
- **WAMP**: `C:\wamp64\www\todo-app`
- **LAMP**: `/var/www/html/todo-app`

### 3. Crear la base de datos

Accede a phpMyAdmin o usa la terminal de MySQL:

```bash
mysql -u root -p < database.sql
```

O copia y ejecuta el contenido de `database.sql` en phpMyAdmin.

### 4. Configurar la conexión a la base de datos

Edita el archivo `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'becall_app');
define('DB_USER', 'root');        // Tu usuario de MySQL
define('DB_PASS', '');            // Tu contraseña de MySQL
```

### 5. Configurar permisos (Linux/Mac)

```bash
chmod 755 logs/
chmod 644 logs/app.log
```

### 6. Acceder a la aplicación

Abre tu navegador y visita:
```
http://localhost/todo-app
```

##  Uso de la Aplicación

### Interfaz Web

1. **Lista de Tareas**: `http://localhost/todo-app/`
   - Ver todas las tareas
   - Estadísticas por estado
   - Editar o eliminar tareas

2. **Crear Tarea**: Click en "+Nueva Tarea"
   - Completar formulario
   - Guardar

3. **Configuración**: Click en "Config"
   - Configurar URL del webhook
   - Obtener una URL de prueba en [webhook.site](https://webhook.site)

4. **Logs**: Click en "Logs"
   - Ver todas las acciones registradas
   - Limpiar logs

### API REST

#### Endpoints disponibles:

**1. Listar todas las tareas**
```bash
GET http://localhost/todo-app/api/tareas

# Con filtro por estado
GET http://localhost/todo-app/api/tareas?estado=pendiente
```

**2. Obtener una tarea específica**
```bash
GET http://localhost/todo-app/api/tareas/1
```

**3. Crear una nueva tarea**
```bash
POST http://localhost/todo-app/api/tareas
Content-Type: application/json

{
  "titulo": "Nueva tarea de prueba",
  "descripcion": "Descripción detallada",
  "fecha_vencimiento": "2026-01-30",
  "estado": "pendiente"
}
```

**4. Actualizar una tarea**
```bash
PUT http://localhost/todo-app/api/tareas/1
Content-Type: application/json

{
  "titulo": "Tarea actualizada",
  "descripcion": "Nueva descripción",
  "fecha_vencimiento": "2026-02-01",
  "estado": "en progreso"
}
```

**5. Eliminar una tarea**
```bash
DELETE http://localhost/todo-app/api/tareas/1
```

#### Respuestas de la API

Todas las respuestas incluyen el campo `"saludo": "holi"` según requisitos.

**Ejemplo de respuesta exitosa (GET):**
```json
{
  "saludo": "holi",
  "data": [
    {
      "id": 1,
      "titulo": "Completar prueba técnica",
      "descripcion": "Desarrollar aplicación ToDo",
      "estado": "en progreso",
      "fecha_creacion": "2026-01-19 10:00:00",
      "fecha_vencimiento": "2026-01-25"
    }
  ]
}
```

**Ejemplo de respuesta de error:**
```json
{
  "saludo": "holi",
  "error": "Tarea no encontrada"
}
```

### Webhooks

Cada vez que se realiza una operación (crear, editar, eliminar), se envía automáticamente un POST al webhook configurado:

```json
{
  "accion": "create",
  "fecha_hora": "2026-01-19 14:30:00",
  "tarea": {
    "id": 1,
    "titulo": "Nueva tarea",
    "descripcion": "Descripción...",
    "estado": "pendiente",
    "fecha_creacion": "2026-01-19 14:30:00",
    "fecha_vencimiento": "2026-01-25"
  }
}
```

**Configurar webhook:**
1. Visita [webhook.site](https://webhook.site)
2. Copia tu URL única
3. Ve a "Config" en la aplicación
4. Pega la URL y guarda

##  Estructura del Proyecto

```
todo-app/
├── config/
│   ├── database.php      # Configuración de BD
│   └── config.php        # Configuración general
├── includes/
│   ├── Database.php      # Clase de conexión
│   ├── Tarea.php         # Modelo de Tarea (CRUD)
│   ├── Logger.php        # Sistema de logs
│   └── Webhook.php       # Envío de webhooks
├── api/
│   └── tareas.php        # Endpoints REST
├── public/
│   ├── index.php         # Lista de tareas
│   ├── crear.php         # Crear tarea
│   ├── editar.php        # Editar tarea
│   ├── eliminar.php      # Eliminar tarea
│   ├── configuracion.php # Configurar webhook
│   ├── logs.php          # Ver logs
│   └── style.css         # Estilos
├── logs/
│   └── app.log           # Archivo de logs
├── database.sql          # Script SQL
├── .htaccess             # Configuración Apache
└── README.md             # Este archivo
```

## Estructura de Base de Datos

### Tabla: Tarea_data
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `fecha_creacion` (DATETIME)
- `fecha_vencimiento` (DATE)
- `estado` (ENUM: 'pendiente', 'en progreso', 'completada')
- `aux1` (VARCHAR(50))

### Tabla: Tarea_dataexten
- `id` (INT, PRIMARY KEY, FOREIGN KEY)
- `titulo` (VARCHAR(255))
- `descripcion` (TEXT)
- `aux1` (VARCHAR(50))

### Tabla: config
- `clave` (VARCHAR(50), PRIMARY KEY)
- `valor` (TEXT)
- `descripcion` (VARCHAR(255))
- `aux1` (VARCHAR(50))

## Probar la API con cURL

```bash
# Listar tareas
curl http://localhost/todo-app/api/tareas

# Crear tarea
curl -X POST http://localhost/todo-app/api/tareas \
  -H "Content-Type: application/json" \
  -d '{"titulo":"Tarea de prueba","fecha_vencimiento":"2026-01-30","estado":"pendiente"}'

# Actualizar tarea
curl -X PUT http://localhost/todo-app/api/tareas/1 \
  -H "Content-Type: application/json" \
  -d '{"titulo":"Tarea actualizada","fecha_vencimiento":"2026-02-01","estado":"completada"}'

# Eliminar tarea
curl -X DELETE http://localhost/todo-app/api/tareas/1
```

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo
- Comprueba las credenciales en `config/database.php`
- Asegúrate de que la base de datos `becall_app` existe

### Los webhooks no se envían
- Verifica que la extensión cURL esté habilitada en PHP
- Comprueba que la URL del webhook sea válida
- Revisa los logs en "📄 Logs"

### Error 404 en la API
- Verifica que `mod_rewrite` esté habilitado en Apache
- Comprueba que el archivo `.htaccess` exista
- Revisa la configuración de Apache para AllowOverride

### Los logs no se guardan
- Verifica permisos de escritura en la carpeta `logs/`
- En Linux/Mac: `chmod 755 logs/`

## Notas Adicionales

- La aplicación registra todas las acciones en `logs/app.log`
- Los webhooks tienen un timeout de 5 segundos
- La URL del webhook se almacena en la base de datos y puede modificarse desde la interfaz
- El proyecto usa PHP puro sin frameworks para máxima compatibilidad
- Se implementó PDO para seguridad contra inyección SQL

## Desarrollo

**Versión**: 1.0.0  
**Autor**: [Tu Nombre]  
**Fecha**: Enero 2026  
**PHP**: 7.4+  
**Base de Datos**: MySQL