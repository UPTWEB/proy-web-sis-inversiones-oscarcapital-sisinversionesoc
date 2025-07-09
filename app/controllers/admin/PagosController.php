<?php
require_once '../core/Controller.php';
require_once '../app/libreries/WhatsAppService.php';

class PagosController extends Controller
{
    private $pagoModel;
    private $whatsappService;

    public function __construct()
    {
        $this->pagoModel = $this->model('PagoModel');
        $this->whatsappService = new WhatsAppService();
    }
    public function pendientes()
    {
        $verTodos = isset($_GET['todos']) && $_GET['todos'] == '1';
        
        if ($verTodos) {
            $pagos = $this->pagoModel->pagosPendientes();
        } else {
            $pagos = $this->pagoModel->proximosPagosPendientes();
        }
        
        $this->view('admin/pagos/pendientes', [
            'pagos' => $pagos,
            'verTodos' => $verTodos
        ]);
    }
    public function efectuados()
    {
        $pagos = $this->pagoModel->pagosEfectuados();
        $this->view('admin/pagos/efectuados', ['pagos' => $pagos]);
    }
    public function ver($id)
    {
        if (!isset($_SESSION['origen']) && isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['origen'] = $_SERVER['HTTP_REFERER'];
        }
        $pago = $this->pagoModel->ver($id);
        $this->view('admin/pagos/verpago', ['pago' => $pago]);
    }
    public function registrar($id)
    {
        if (!isset($_SESSION['origen']) && isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['origen'] = $_SERVER['HTTP_REFERER'];
        }

        if (!$this->pagoModel->validarOrdenPago($id)) {
            $_SESSION['error'] = 'No se puede registrar este pago. Debe cancelar primero los pagos anteriores pendientes.';
            header("Location: " . ($_SESSION['origen'] ?? '/pagos/pendientes'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['comprobante'];
            $fechaHoy = date('Y-m-d');
            $targetDir = realpath(__DIR__ . '/../../../public') . "/uploads/comprobantes/$fechaHoy";

            // Crear carpeta si no existe
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Validación simple del archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                die('Formato no permitido. Solo JPG y PNG.');
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                die('Error al subir el archivo.');
            }

            // Renombrar archivo de forma segura
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nombreArchivo = "pago_{$id}_" . time() . "." . $extension;
            $rutaRelativa = "uploads/comprobantes/$fechaHoy/$nombreArchivo";
            $rutaDestino = "$targetDir/$nombreArchivo";

            if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                // Registrar el pago en la base de datos
                $this->pagoModel->registrar($id, $rutaRelativa);
                
                // Obtener datos del pago para WhatsApp
                $pago = $this->pagoModel->ver($id);
                
                // Enviar WhatsApp si hay número de celular
                if (!empty($pago['celular'])) {
                    $nombreCompleto = trim($pago['nombres'] . ' ' . $pago['apellido_paterno'] . ' ' . $pago['apellido_materno']);
                    $mensaje = $this->whatsappService->generarMensajePago(
                        $nombreCompleto,
                        $pago['numero_pago'],
                        $pago['monto'],
                        $pago['moneda']
                    );
                    
                    // Enviar el comprobante por WhatsApp
                    $resultado = $this->whatsappService->enviarComprobanteWhatsApp(
                        $pago['celular'],
                        $mensaje,
                        $rutaDestino
                    );
                    
                    // Log del resultado (opcional)
                    if (isset($resultado['error'])) {
                        error_log("Error enviando WhatsApp para pago {$id}: " . $resultado['error']);
                        $_SESSION['warning'] = 'Pago registrado correctamente, pero no se pudo enviar el WhatsApp.';
                    } else {
                        $_SESSION['success'] = 'Pago registrado y comprobante enviado por WhatsApp correctamente.';
                    }
                } else {
                    $_SESSION['warning'] = 'Pago registrado correctamente, pero el cliente no tiene número de celular registrado.';
                }
                
                header("Location: /pagos/efectuados");
                exit;
            } else {
                die('Error al mover el archivo al destino.');
            }
        } else {
            $pago = $this->pagoModel->ver($id);
            $this->view('admin/pagos/registrarpago', ['pago' => $pago]);
        }
    }
}
