<?php
$config = require 'credentials.php';

if (!isset($_GET['code'])) {
    echo "<script>window.close();</script>";
    exit;
}

$code = $_GET['code'];

// code por access_token
$token_response = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code' => $code,
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code',
        ]),
    ]
]));


$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'] ?? null;

if (!$access_token) {
    echo "<script>window.close();</script>";
    exit;
}
 
$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $access_token;

$user_info = file_get_contents($user_info_url);
$user = json_decode($user_info, true);

if (!isset($user['email'])) {
    echo "
    <script>
        window.opener.postMessage(".json_encode([
                                                    'status' => 'error',
                                                    'message' => '❌ No se pudo obtener la información del usuario.'
                                                ]) . ", '*');
        window.close();
    </script>";
    exit;
}

// verify if email is in the database
require_once __DIR__ . '/../../models/UsuarioModel.php';
require_once __DIR__ . '/../../../core/Model.php';

$db = new Model();
$conn = $db->getConnection();
$usuarioModel = new UsuarioModel($conn);

$usuario = $usuarioModel->buscarPorCorreo($user['email']);

if ($usuario) {
    

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['usuario_ip'] = $ipclient;

    // save session
    $id_sesion = $usuarioModel->insertarSesion($usuario['id'], $ipclient, $_SERVER['HTTP_USER_AGENT']);
        

    if($id_sesion){

        $_SESSION['sesion_id'] = $id_sesion; 

        $usuarioModel->intentoLogin($usuario['id'], $ipclient, $_SERVER['HTTP_USER_AGENT'],true);
        
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
        echo "
        <script>
            window.opener.postMessage(".json_encode([
                                                        'status' => 'success',
                                                        'message' => '¡Bienvenido '. $usuario['username'] .'!',
                                                        'redirect' => $redirect 
                                                    ]) . ", '*');
            window.close();
        </script>";
        
        exit;
    
    }
    else{
        
        echo "
        <script>
            window.opener.postMessage(".json_encode([
                                                        'status' => 'success',
                                                        'message' => 'Intentelo de nuevo más tarde.',
                                                    ]) . ", '*');
            window.close();
        </script>";
        
        exit;
    
    }

    
} else {
    echo "
    <script>
        window.opener.postMessage(".json_encode([
                                                    'status' => 'error',
                                                    'message' => '❌ Usted no esta autorizado para iniciar sesion.'
                                                ]) . ", '*');
        window.close();
    </script>";
}