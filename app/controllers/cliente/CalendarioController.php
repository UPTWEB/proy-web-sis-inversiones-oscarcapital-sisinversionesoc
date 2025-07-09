<?php
require_once '../core/Controller.php';

class CalendarioController extends Controller
{
    private $pagoModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->pagoModel = $this->model('PagoModel');
        $this->usuarioModel = $this->model('UsuarioModel');
    }
    public function index()
    {
        $sesion_id = $_SESSION['sesion_id'];
        $id = $this->usuarioModel->obtenerIdCliente($sesion_id);
        $this->view('cliente/calendario', ['id' => $id]);
    }
    public function eventosCalendario($id)
    {
        $pagos = $this->pagoModel->obtenerPagosPendientesPorCliente($id);
        $eventos = [];
        // Colores predefinidos (puedes ampliarlos o generarlos dinámicamente)
        $colores = [
            '#1abc9c',
            '#3498db',
            '#9b59b6',
            '#f1c40f',
            '#e67e22',
            '#e74c3c',
            '#2ecc71',
            '#34495e',
            '#16a085',
            '#2980b9'
        ];
        foreach ($pagos as $pago) {
            $color = $colores[$pago['inversion_id'] % count($colores)];

            $eventos[] = [
                'title' => $pago['nombres'] . " " . $pago['apellido_paterno'] . " " . $pago['apellido_materno'],
                'start' => $pago['fecha'],
                'allDay' => true,
                'url' => '/abonos/' . $pago['id'],
                'color' => $color  // <- Aquí asignas el color
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($eventos);
    }
}
