<?php
header('Content-Type: application/json');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$dominio_request= $protocol . '://' . $host;

$dominios_internos = [
    'http://localhost',
    'http://localhost:8080',
    'http://192.168.0.176:8080',
    'http://127.0.0.1',
    'https://sitioweb.com',
    'https://www.sitioweb.com'
];

$request_uri = $_SERVER['REQUEST_URI'];
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$origin  = $_SERVER['HTTP_ORIGIN'] ?? '';
$headers = getallheaders();

$origen_valido = false;

foreach ($dominios_internos as $dominio) {
    if (str_starts_with($referer, $dominio) || str_starts_with($origin, $dominio)) {
        $origen_valido = true;
        break;
    }
}

// dominio interno solicituud
if (!$origen_valido) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado: origen no permitido '.$dominio_request]);
    exit;
}


// header de autorización
// if (!isset($data['sendBeacon']) && !isset($headers['X-Internal-Request']) || $headers['X-Internal-Request'] !== '1') {
//     http_response_code(403);
//     echo json_encode(['status' => 'error', 'message' => 'Acceso denegado: encabezado no permitido '.$dominio_request]);
//     exit;
// }



// --- Lógica de excepción para sendBeacon (Opción 2) --- //
$isLogoutSendBeacon = str_contains($request_uri, 'AuthlogoutDisconnect');

// Decodifica input si es JSON
$data = json_decode(file_get_contents("php://input"), true) ?? [];

// Si no es la ruta especial, exigir el header personalizado
if (!$isLogoutSendBeacon) {
    
    if (!isset($headers['X-Internal-Request']) || $headers['X-Internal-Request'] !== '1') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Acceso denegado: encabezado no permitido '.$dominio_request]);
        exit;
    }
}