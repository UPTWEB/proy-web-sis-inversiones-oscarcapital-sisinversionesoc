<?php
require_once '../app/views/includes/header.security.php';

header('Content-Type: application/json');

require_once '../core/Controller.php';

class UserApiController extends Controller
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = $this->model('UsuarioModel');
    }

    public function UpdateUser()
    {
        // only post request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            return;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'multipart/form-data') !== false) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';
            $email = $_POST['email'] ?? '';
        } else {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            $newPassword = $data['newPassword'] ?? '';
            $email = $data['email'] ?? '';
        }

        if (!$username || !$password || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Ingrese los datos correctamente.']);
            return;
        }

        $usuario = $this->usuarioModel->buscarIdUsuario($_SESSION['usuario_id']);

        if($usuario){
            
            // password verification
            if (crypt($password, $usuario['password']) !== $usuario['password']) {
                echo json_encode(['status' => 'error', 'message' => 'La contraseña actual ingresada es incorrecta.']);
                return;
            }
            
            // user status 
            if (!$usuario['estado']) {
                echo json_encode(['status' => 'error', 'message' => 'Su cuenta se encuentra deshabilitada.']);
                return;
            }

            $rutaFoto = null;
            if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
                $resultadoFoto = $this->procesarFoto($_FILES['profilePhoto']);
                if ($resultadoFoto['success']) {
                    $rutaFoto = $resultadoFoto['ruta'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $resultadoFoto['message']]);
                    return;
                }
            }

            try{
                $resultado = $this->usuarioModel->modificarUsuario($_SESSION["usuario_id"], $username, $email, $newPassword, $rutaFoto);
                
                if ($resultado['success']) {
                    $_SESSION['username'] = $username; 
                    $_SESSION['email'] = $email;  
                    if ($rutaFoto) {
                        $_SESSION['foto'] = $rutaFoto;
                    }
                    
                    echo json_encode(['status' => 'success', 'message' => '¡Usuario modificado con éxito!', 'affected_rows' => $resultado['affected_rows'] ?? 0]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $resultado['message']]);
                }
            }
            catch(Exception $e){
                echo json_encode(['status' => 'error', 'message' => 'Intentelo de nuevo más tarde.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'El usuario no esta asociado a ninguna cuenta.']);
        }
    }

    private function procesarFoto($file)
    {
        try {
            $targetDir = realpath(__DIR__ . '/../../public') . "/uploads/profile";
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'message' => 'Formato no permitido. Solo JPG y PNG.'];
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Error al subir el archivo.'];
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                return ['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 5MB.'];
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nombreArchivo = $_SESSION['usuario_id'] . "." . $extension;
            $rutaRelativa = "uploads/profile/" . $nombreArchivo;
            $rutaDestino = "$targetDir/$nombreArchivo";

            $fotosAnteriores = glob($targetDir . "/" . $_SESSION['usuario_id'] . ".*");
            foreach ($fotosAnteriores as $fotoAnterior) {
                if (file_exists($fotoAnterior)) {
                    unlink($fotoAnterior);
                }
            }

            if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                return ['success' => true, 'ruta' => $rutaRelativa];
            } else {
                return ['success' => false, 'message' => 'Error al guardar el archivo.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error interno al procesar la foto.'];
        }
    }
}
