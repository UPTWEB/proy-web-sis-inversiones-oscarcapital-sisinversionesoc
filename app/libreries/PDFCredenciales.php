<?php
require_once(__DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PDFCredenciales
{
    private $dni;
    private $nombres;
    private $apellidos;
    private $email;
    
    public function __construct($dni, $nombres, $apellidos, $email = 'sc2022073503@virtual.upt.pe')
    {
        $this->dni = $dni;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->email = $email;
    }
    
    public function crearYEnviarPDF()
    {
        try {
            $pdfPath = $this->crearPDF();
            
            $resultado = $this->enviarPorCorreo($pdfPath);
            
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al crear y enviar PDF: " . $e->getMessage());
            return false;
        }
    }
    
    private function crearPDF()
    {
        try {
            $pdf = new TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistema de Inversiones de Oscar Capital');
            $pdf->SetTitle('Credenciales de Acceso');
            $pdf->SetSubject('Datos de acceso al sistema');
            
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 12);
            
            $html = '
            <h2 style="color:rgb(185, 106, 41); text-align: center;">Bienvenido al Sistema de Inversiones</h2>
            <h3>Estimado/a ' . $this->nombres . ' ' . $this->apellidos . '</h3>
            <p>Sus credenciales de acceso al sistema son:</p>
            <p><strong>Sitio web:</strong> https://sistemainversionesoc.azurewebsites.net</p>
            <p><strong>Usuario:</strong> ' . $this->dni . '</p>
            <p><strong>Contraseña:</strong> ' . $this->dni . '</p>
            <p>Por favor, guarde estas credenciales en un lugar seguro.</p>
            <p>Atentamente,<br>Sistema de Inversiones Oscar Capital</p>
            ';
            
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Establecer clave de usuario (DNI)
            $pdf->SetProtection(array(), $this->dni, null, 0, null);
            
            // Crear directorio temp si no existe
            $tempDir = __DIR__ . '/../../temp';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            // Guardar el PDF
            $pdfFilePath = $tempDir . '/credenciales_' . $this->dni . '_' . time() . '.pdf';
            $pdf->Output($pdfFilePath, 'F');
            
            return $pdfFilePath;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function enviarPorCorreo($pdfPath)
    {
        try {
            $autoloadPaths = [
                __DIR__ . '/../../vendor/autoload.php',
                __DIR__ . '/../../../vendor/autoload.php',
                dirname(__DIR__, 2) . '/vendor/autoload.php'
            ];
            
            $autoloadFound = false;
            foreach ($autoloadPaths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    $autoloadFound = true;
                    break;
                }
            }
            
            if (!$autoloadFound) {
                throw new Exception("No se pudo encontrar vendor/autoload.php");
            }
            
            $mail = new PHPMailer(true);
            
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sergiocolque.tlv@gmail.com';
            $mail->Password = 'cuab hemc obiw ccjj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Configuración del correo
            $mail->setFrom('sergiocolque.tlv@gmail.com', 'Sistema de Inversiones Oscar Capital');
            $mail->addAddress($this->email);
            
            // Verificar que el archivo PDF existe
            if (!file_exists($pdfPath)) {
                throw new Exception("El archivo PDF no existe: " . $pdfPath);
            }
            
            // Adjuntar el PDF
            $mail->addAttachment($pdfPath, 'credenciales_acceso.pdf');
            
            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Credenciales de Acceso - Sistema de Inversiones';
            $mail->Body = '
            <html>
            <body style="font-family: Arial, sans-serif;">
                <h2 style="color:rgb(196, 115, 10);">¡Bienvenido al Sistema de Inversiones!</h2>
                <p>Estimado/a <strong>' . $this->nombres . ' ' . $this->apellidos . '</strong>,</p>
                <p>Su registro ha sido completado exitosamente. Adjunto encontrará sus credenciales de acceso.</p>
                <p><strong>Datos de acceso:</strong></p>
                <ul>
                    <li><strong>Sitio web:</strong> https://sistemainversionesoc.azurewebsites.net</li>
                    <li><strong>Usuario:</strong> ' . $this->dni . '</li>
                    <li><strong>Contraseña:</strong> ' . $this->dni . '</li>
                </ul>
                <p><strong>Nota:</strong> La contraseña para abrir el PDF adjunto es su DNI: <strong>' . $this->dni . '</strong></p>
                <p>Atentamente,<br>Equipo de Oscar Capital</p>
            </body>
            </html>
            ';
            
            $mail->AltBody = 'Bienvenido al Sistema de Inversiones. Sus credenciales: Usuario: ' . $this->dni . ', Contraseña: ' . $this->dni . '. Sitio: https://sistemainversionesoc.azurewebsites.net';
            
            $mail->send();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $e->getMessage());
            return false;
        }
    }
}
?>