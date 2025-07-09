<?php
require_once '../core/Controller.php';

class CalendarioController extends Controller
{
    private $pagoModel;

    public function __construct()
    {
        $this->pagoModel = $this->model('PagoModel');
    }
    public function index()
    {
        $this->view('admin/calendario');
    }
    public function eventosCalendario()
    {
        $pagos = $this->pagoModel->obtenerPagosCalendario();
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
                'url' => '/pagos/' . $pago['id'] . '/registrar/',
                'color' => $color  // <- Aquí asignas el color
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($eventos);
    }
}
