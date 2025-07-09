<?php

require_once 'WhatsAppService.php';
require_once 'Conexion.php'; // Asegúrate que aquí conectas a tu BD correctamente

$numeroDestino = '51987654321'; // Reemplaza con el número real

$db = new Conexion();
$conn = $db->getConnection();

$sql = "
SELECT
    (dp.datos_personales).nombres || ' ' || (dp.datos_personales).apellido_paterno || ' ' || (dp.datos_personales).apellido_materno AS nombre_completo,
    p.fecha AS fecha_pago,
    p.monto
FROM
    pago p
JOIN inversion i ON p.inversion_id = i.id
JOIN cliente dp ON i.cliente_id = dp.id
WHERE
    p.fecha BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '20 days'
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($resultados)) {
    echo "No hay pagos próximos.\n";
    exit;
}

$mensaje = "🔔 *Pagos próximos (20 días)* 🔔\n\n";

foreach ($resultados as $pago) {
    $nombre = $pago['nombre_completo'];
    $fecha = date('d/m/Y', strtotime($pago['fecha_pago']));
    $monto = number_format($pago['monto'], 2);

    $mensaje .= "👤 {$nombre}\n📅 Fecha: {$fecha}\n💵 Monto: S/ {$monto}\n\n";
}

$whatsapp = new WhatsAppService();
$respuesta = $whatsapp->enviarMensajeTexto($numeroDestino, $mensaje);

print_r($respuesta);

$db->closeConnection();
?>