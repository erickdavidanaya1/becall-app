<?php
// Clase para manejar la conexión a la base de datos

class Database {
    private $conn;
    private static $instance = null;
    
    // Constructor privado - solo se puede crear una instancia
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Que lance errores si algo falla
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Que devuelva arrays asociativos
                PDO::ATTR_EMULATE_PREPARES => false,  // Para mayor seguridad
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }
    
    // Patrón Singleton - solo una conexión a la BD
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Obtener la conexión PDO
    public function getConnection() {
        return $this->conn;
    }
    
    // Ejecutar una consulta con parámetros (más seguro contra SQL injection)
    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // Obtener un valor de configuración
    public function getConfig($clave) {
        $stmt = $this->query("SELECT valor FROM config WHERE clave = ?", [$clave]);
        $result = $stmt->fetch();
        return $result ? $result['valor'] : null;
    }
    
    // Guardar un valor de configuración
    public function setConfig($clave, $valor) {
        $sql = "INSERT INTO config (clave, valor) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE valor = ?";
        return $this->query($sql, [$clave, $valor, $valor]);
    }
}