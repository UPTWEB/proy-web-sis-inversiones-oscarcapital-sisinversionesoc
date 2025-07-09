<?php
require_once '../app/views/includes/header.security.php';

header('Content-Type: application/json');

require_once '../core/Controller.php';

class AuthApiController extends Controller
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = $this->model('UsuarioModel');
    }

    public function Authlogin()
    {

        // only post request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            return;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['username']) || !isset($data['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Ingrese todos los datos para iniciar sesion.']);
            return;
        }

        $username = $data['username'];
        $password = $data['password'];
        $ipclient = $data['ipclient'];

        $usuario = $this->usuarioModel->iniciarSesion($username);


        if($usuario){
            
            // password verification
            if (crypt($password, $usuario['password']) !== $usuario['password']) {

                $this->usuarioModel->intentoLogin($usuario['id'], $ipclient, $_SERVER['HTTP_USER_AGENT'], false);
                echo json_encode(['status' => 'error', 'message' => '❌ Usuario o contraseña incorrectos.']);
                return;
            }
            
            // user status 
            if (!$usuario['estado']) {
                echo json_encode(['status' => 'error', 'message' => 'Su cuenta se encuentra deshabilitada.']);
                return;

            }
            
            // failed attempts
            $intentosFallidos = $this->usuarioModel->intentosFallidos($usuario['id']);

            if ($intentosFallidos >= 3) {
                echo json_encode(['status' => 'error', 'message' => 'Su cuenta ha sido bloqueada por múltiples intentos fallidos.']);
                return;
            }

            // Supongamos que ya validaste el usuario
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['usuario_ip'] = $ipclient;

            // save session
            $id_sesion = $this->usuarioModel->insertarSesion($usuario['id'], $ipclient, $_SERVER['HTTP_USER_AGENT']);
    

            if($id_sesion){

                $_SESSION['sesion_id'] = $id_sesion; 

                $this->usuarioModel->intentoLogin($usuario['id'], $ipclient, $_SERVER['HTTP_USER_AGENT'],true);
                
                switch ($usuario['rol']) {
                    case 'admin':
                        $redirect = '/inicio';
                        break;
                    case 'cliente"':
                        $redirect = '/inicio';
                        break;
                    default:
                        $redirect = '/inicio';
                        break;
                }

                $_SESSION['username'] = $usuario['username'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['email'] = $usuario['email']; 
                // $_SESSION['password'] = $usuario['password']; 
                $_SESSION['foto'] = $usuario['foto']; 

                echo json_encode(['status' => 'success', 'message' => '¡Bienvenido ' . $usuario['username'] . '!', 'redirect' => $redirect]);
            }
            else{
                echo json_encode(['status' => 'error', 'message' => 'Intentelo de nuevo más tarde.']);
            }
        } else {
    
            echo json_encode(['status' => 'error', 'message' => 'El usuario o correo que ingresaste no esta asociado a ninguna cuenta.']);

        }

    }

    public function Authlogout()
    {
        // only post request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $ip = $_SESSION['usuario_ip'];
        $tiempo = $data['tiempo'] ?? 0;

        $id_sesion = $_SESSION['sesion_id'] ?? null;
        $this->usuarioModel->cerrarSesion($id_sesion,$tiempo);
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Sesión cerrada correctamente.']);
    }


    public function AuthlogoutDisconnect()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $ip = $_SESSION['usuario_ip'] ?? '';
        $id_sesion = $_SESSION['sesion_id'] ?? null;
        $tiempo = $data['tiempo'] ?? 0;
    
        if ($id_sesion) {
            $this->usuarioModel->cerrarSesionDesconectado($id_sesion,$tiempo);
        }
    }

    public function PingSesion()
    {

        $id_sesion = $_SESSION['sesion_id'] ?? null;
        if (!$id_sesion) return;

        $data = json_decode(file_get_contents("php://input"), true);
        $tiempo = $data['tiempo'] ?? 0;

        $this->usuarioModel->actualizarUltimoAcceso($id_sesion,$tiempo);
        echo json_encode(['status' => 'success', 'message' => 'Sesión actualizada correctamente.']);
    
    }

    public function TimeSesion()
    {
        $id_sesion = $_SESSION['sesion_id'] ?? null;
        if (!$id_sesion) {
            echo json_encode(['status' => 'error', 'time' => '00:00:00']);
            return;
        }
    
        $tiempo = $this->usuarioModel->obtenerTiempoSesion($id_sesion);
    
        echo json_encode(['status' => 'success', 'time' => $tiempo]);
    }


}
