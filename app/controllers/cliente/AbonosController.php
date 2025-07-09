<?php
require_once '../core/Controller.php';

class AbonosController extends Controller
{
    private $usuarioModel;
    private $pagoModel;

    public function __construct()
    {
        $this->usuarioModel = $this->model('UsuarioModel');
        $this->pagoModel = $this->model('PagoModel');
    }
    public function pendientes()
    {
        $sesion_id = $_SESSION['sesion_id'];
        $cliente_id = $this->usuarioModel->obtenerIdCliente($sesion_id);
        $pagos = $this->pagoModel->obtenerPagosPendientesPorCliente($cliente_id);

        $this->view('cliente/abonos/pendientes', ['pagos' => $pagos]);
    }
    public function efectuados()
    {
        $sesion_id = $_SESSION['sesion_id'];
        $cliente_id = $this->usuarioModel->obtenerIdCliente($sesion_id);
        $pagos = $this->pagoModel->obtenerPagosEfectuadosporCliente($cliente_id);
        $this->view('cliente/abonos/efectuados', ['pagos' => $pagos]);
    }
    public function ver($id)
    {
        if (!isset($_SESSION['origen']) && isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['origen'] = $_SERVER['HTTP_REFERER'];
        }
        $sesion_id = $_SESSION['sesion_id'];
        $cliente_id = $this->usuarioModel->obtenerIdCliente($sesion_id);
        $pago = $this->pagoModel->ver($id,$cliente_id);
        $this->view('cliente/abonos/verabono', ['pago' => $pago]);
    }
}
