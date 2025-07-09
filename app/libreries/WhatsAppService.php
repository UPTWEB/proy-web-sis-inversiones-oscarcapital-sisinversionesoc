<?php

class WhatsAppService
{
    private $token;
    private $instancia;
    
    public function __construct()
    {
        // Configuración desde variables de entorno o valores por defecto
        $this->token = $_ENV['WHATSAPP_TOKEN'] ?? 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzODYwMyIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.c6rXTQYdo-4rBfgtO8QaskrTu3PpbDX3Lglbge4gZ3c';
        $this->instancia = $_ENV['WHATSAPP_INSTANCIA'] ?? 'NTE5NjM2NTM3Mzc=';
    }
    
    /**
     * Envía un mensaje de WhatsApp con archivo adjunto
     * @param string $numero - Número de teléfono
     * @param string $mensaje - Mensaje de texto
     * @param string $rutaArchivo - Ruta del archivo a enviar
     * @return array - Respuesta de la API
     */
    public function enviarComprobanteWhatsApp($numero, $mensaje, $rutaArchivo)
    {
        try {
            // Validaciones básicas
            if (empty($numero) || empty($mensaje)) {
                throw new Exception('Número y mensaje son requeridos');
            }
            
            if (empty($this->token) || empty($this->instancia)) {
                throw new Exception('Token e instancia deben estar configurados');
            }
            
            // Leer el archivo y convertir a base64
            if (!file_exists($rutaArchivo)) {
                throw new Exception('El archivo no existe: ' . $rutaArchivo);
            }
            
            $contenidoArchivo = file_get_contents($rutaArchivo);
            $base64 = base64_encode($contenidoArchivo);
            
            // Preparar datos para la API
            $datos = [
                'number' => $numero,
                'mediatype' => 'image',
                'media' => $base64,
                'caption' => $mensaje
            ];
            
            // Configurar cURL
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://apiwsp.factiliza.com/v1/message/sendmedia/{$this->instancia}",
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
            error_log('Error enviando WhatsApp: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    public function generarMensajePago($nombreCliente, $numeroPago, $monto, $moneda)
    {
        $mesesEspanol = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $mesNumero = date('n');
        $año = date('Y');
        $mesActual = $mesesEspanol[$mesNumero] . ' ' . $año;
        
        $partesCuota = explode('/', $numeroPago);
        $cuotaActual = isset($partesCuota[0]) ? (int)$partesCuota[0] : 1;
        $totalCuotas = isset($partesCuota[1]) ? (int)$partesCuota[1] : 1;
        
        $esUltimaCuota = ($cuotaActual === $totalCuotas);
        
        if ($esUltimaCuota) {
            $mensaje = "Buenos días {$nombreCliente} ☀ te adjunto el comprobante de las ganancias correspondientes al mes de {$mesActual}.\n\n";
            $mensaje .= "Detalles del pago:\n";
            $mensaje .= "• 🧾 Cuota: #{$numeroPago}\n";
            $mensaje .= "• 💰 Monto: {$monto} {$moneda}\n\n";
            $mensaje .= "Hola {$nombreCliente} ☀ Adjunto el comprobante de su inversión, además queremos agradecerte por haber trabajado con nosotros y por confiar en nuestros servicios 🙌🏽. Ha sido un gusto atenderte. Si decides realizar otra inversión en el futuro, estaremos encantados de asistirte nuevamente. No dudes en contactarnos para cualquier consulta.\n\n";
            $mensaje .= "Saludos cordiales,\n";
            $mensaje .= "Equipo de Inversiones de Oscar Capital.";
        } else {
            $mensaje = "Buenos días {$nombreCliente} ☀ te adjunto el comprobante de las ganancias correspondientes al mes de {$mesActual}.\n\n";
            $mensaje .= "Detalles del pago:\n";
            $mensaje .= "• 🧾 Cuota: #{$numeroPago}\n";
            $mensaje .= "• 💰 Monto: {$monto} {$moneda}\n\n";
            $mensaje .= "Gracias por confiar en nuestros servicios. Continuamos trabajando para ofrecerte los mejores resultados en tu inversión. ¡Que tengas un excelente día! 😊\n\n";
            $mensaje .= "Equipo de Inversiones de Oscar Capital.";
        }
        
        return $mensaje;
    }
}