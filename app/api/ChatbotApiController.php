<?php


require_once '../core/Controller.php';

class ChatbotApiController extends Controller
{
    private $chatbotModel;

    public function __construct()
    {
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no autenticado']);
            exit;
        }
        
        try {
            $this->chatbotModel = $this->model('ChatbotModel');
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al inicializar el modelo: ' . $e->getMessage()]);
            exit;
        }
    }

    public function processMessage()
    {
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');

        if (empty($message)) {
            echo json_encode(['error' => 'Mensaje vacío']);
            return;
        }

        try {
            $response = $this->chatbotModel->processQuery($message);
            
            $this->logChatbotQuery($message, $_SESSION['usuario_id'], $_SESSION['usuario_rol']);
            
            echo json_encode([
                'response' => $response,
                'timestamp' => date('Y-m-d H:i:s'),
                'user_role' => $_SESSION['usuario_rol'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("Error en ChatbotApiController: " . $e->getMessage());
            echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
        }
    }
    
    private function logChatbotQuery($message, $userId, $userRole)
    {
        try {
            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id' => $userId,
                'user_role' => $userRole,
                'message' => $message,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            error_log("Chatbot Query: " . json_encode($logData));
        } catch (Exception $e) {
        }
    }
}
?>