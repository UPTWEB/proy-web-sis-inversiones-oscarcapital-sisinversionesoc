<?php

class WhatsAppService
{
    private $token;
    private $instancia;
    
    public function __construct()
    {
        $this->token = $_ENV['WHATSAPP_TOKEN'] ?? 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzODYwMyIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.c6rXTQYdo-4rBfgtO8QaskrTu3PpbDX3Lglbge4gZ3c';
        $this->instancia = $_ENV['WHATSAPP_INSTANCIA'] ?? 'NTE5NjM2NTM3Mzc=';
    }

    public function enviarMensajeTexto($numero, $mensaje)
    {
        try {
            if (empty($numero) || empty($mensaje)) {
                throw new Exception('Número y mensaje son requeridos');
            }

            if (empty($this->token) || empty($this->instancia)) {
                throw new Exception('Token e instancia deben estar configurados');
            }

            $datos = [
                'number' => $numero,
                'text' => $mensaje
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://apiwsp.factiliza.com/v1/message/sendtext/{$this->instancia}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($datos),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->token,
                    'Content-Type: application/json'
                ],
                CURLOPT_TIMEOUT => 30
            ]);

            $respuesta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception('Error cURL: ' . curl_error($ch));
            }

            curl_close($ch);

            $resultado = json_decode($respuesta, true);

            if ($httpCode !== 200) {
                throw new Exception('Error en la API de WhatsApp: ' . $respuesta);
            }

            return $resultado;

        } catch (Exception $e) {
            error_log('Error enviando WhatsApp texto: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
