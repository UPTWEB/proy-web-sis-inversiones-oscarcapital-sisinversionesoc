<?php
require_once '../core/Controller.php';

class InversionesController extends Controller
{
    private $inversionModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->inversionModel = $this->model('InversionModel');
        $this->usuarioModel = $this->model('UsuarioModel');
    }
    public function index()
    {
        $sesion_id = $_SESSION['sesion_id'];
        $cliente_id = $this->usuarioModel->obtenerIdCliente($sesion_id);
        $inversiones = $this->inversionModel->listarPorCliente($cliente_id);
        $this->view('cliente/inversiones/inversiones', ['inversiones' => $inversiones]);
    }
    public function ver($id)
    {
        $sesion_id = $_SESSION['sesion_id'];
        $cliente_id = $this->usuarioModel->obtenerIdCliente($sesion_id);
        $inversion = $this->inversionModel->verPorCliente($id, $cliente_id);
        $this->view('admin/inversiones/verinversion', ['inversion' => $inversion]);
    }
}
