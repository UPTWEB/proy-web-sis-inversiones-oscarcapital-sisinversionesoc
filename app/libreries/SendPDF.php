<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sergiocolque.tlv@gmail.com';
    $mail->Password = 'cuab hemc obiw ccjj';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('sergiocolque.tlv@gmail.com', 'Tu Nombre');
    $mail->addAddress('destinatario@ejemplo.com');

    // Adjuntar el PDF protegido
    $mail->addAttachment($pdfFilePath);

    // Cuerpo HTML
    $mail->isHTML(true);
    $mail->Subject = 'PDF Protegido con contraseña';
    $mail->Body = '
        <html>
        <body style="font-family: Arial, sans-serif;">
            <h2 style="color: #2980B9;">Hola!</h2>
            <p>Adjunto encontrarás el PDF protegido.</p>
            <p>Su nombre de usuario es: <strong></strong></p>
            <p>La contraseña para abrirlo es: <strong></strong></p>
        </body>
        </html>
    ';
    $mail->AltBody = 'Adjunto el PDF correspondiente, con sus datos de usuario para ingresar al sitio web https://sistemainversionesoc.azurewebsites.net, la clave es su DNI';

    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar correo: {$mail->ErrorInfo}";
}
?>