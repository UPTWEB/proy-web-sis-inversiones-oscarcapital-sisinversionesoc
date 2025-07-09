<?php
require_once '../core/Controller.php';

class InversionesController extends Controller
{
    private $inversionModel;

    public function __construct()
    {
        $this->inversionModel = $this->model('InversionModel');
    }
    public function index()
    {
        $inversiones = $this->inversionModel->listar();
        $this->view('admin/inversiones/inversiones', ['inversiones' => $inversiones]);
    }
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->inversionModel->crear();
            header(header: 'Location: /inversiones');
            exit;
        } else {
            $clienteModel = $this->model('ClienteModel');
            $clientes = $clienteModel->listar();
            $this->view('admin/inversiones/crearinversion', ['clientes' => $clientes]);
        }
    }
    public function eliminar()
    {
        if ($_POST['id'] !== null) {
            $this->inversionModel->eliminar($_POST['id']);
        }
        header(header: 'Location: /inversiones');
        exit;
    }
    public function ver($id)
    {
        $inversion = $this->inversionModel->ver($id);
        $this->view('admin/inversiones/verinversion', ['inversion' => $inversion]);
    }
    public function select_cliente($q)
    {
        $this->inversionModel->select_cliente($q);
    }
}
