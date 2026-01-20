<?php
// Clase para enviar notificaciones por webhook

class Webhook {
    private $url;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->url = $db->getConfig('webhook_url');
    }
    
    // Enviar datos al webhook
    public function enviar($tipo_accion, $datos_tarea) {
        // Si no está configurada la URL
        if (empty($this->url) || $this->url === 'https://webhook.site/YOUR-UNIQUE-ID') {
            Logger::log("Webhook no configurado", "Acción: $tipo_accion");
            return false;
        }
        
        // Preparar los datos que vamos a enviar
        $payload = [
            'action' => $tipo_accion,
            'datetime' => date('Y-m-d H:i:s'),
            'task' => $datos_tarea
        ];
        
        // Usar cURL para hacer el POST
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: BecallApp/1.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);  // Timeout de 5 segundos
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Si hubo error, lo registramos
        if ($error) {
            Logger::log("Error al enviar webhook", "Error: $error");
            return false;
        }
        
        // Todo bien, registramos el envío
        Logger::log("Webhook enviado", "Acción: $tipo_accion, HTTP Code: $httpCode");
        return true;
    }
}