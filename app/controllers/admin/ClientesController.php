<?php
require_once '../core/Controller.php';

class ClientesController extends Controller
{
    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = $this->model('ClienteModel');
    }
    public function index()
    {
        $clientes = $this->clienteModel->listar();
        $this->view('admin/clientes/clientes', ['clientes' => $clientes]);
    }
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->clienteModel->crear(); // Inserta en la base de datos
            header(header: 'Location: /clientes');
            exit;
        } else {
            $this->view('admin/clientes/crearcliente'); // Muestra el formulario
        }
    }
    public function editar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente = $_POST;
            $this->clienteModel->editar($cliente); // Inserta en la base de datos
            header(header: 'Location: /clientes');
            exit;
        } else {
            $cliente = $this->clienteModel->ver($id);
            $this->view('admin/clientes/crearcliente', ['cliente' => $cliente]); // Muestra el formulario
        }
    }
    public function ver($accion)
    {
        $cliente = $this->clienteModel->ver($accion);
        $this->view('admin/clientes/vercliente', ['cliente' => $cliente]);
    }
    public function verAjax($dni)
    {
        $cliente = $this->clienteModel->ver($dni);
        header('Content-Type: application/json');
        echo json_encode($cliente);
    }
    public function eliminar()
    {
        if ($_POST['id'] !== null) {
            $this->clienteModel->eliminar($_POST['id']);
        }
        header(header: 'Location: /clientes');
    }
    public function consultar_dni()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'];
            $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzODEwNCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.n6qzs6YekPVa80d8k2BamUBXZ8tAwerf53Cw7VKtKkM'; // Tu token API

            try {
                $resultado = $this->clienteModel->consultarDNI($dni, $token);
                if (isset($resultado['success']) && $resultado['success'] && isset($resultado['data'])) {
                    echo json_encode([
                        'success' => true,
                        'data' => $resultado['data']
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => $resultado['message'] ?? 'Error desconocido'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
}
