<?php
require_once '../core/Controller.php';

class InicioController extends Controller
{
    private $pagoModel;

    public function __construct()
    {
        $this->pagoModel = $this->model('PagoModel');
    }
    public function index()
    {
        $clienteModel = $this->model('ClienteModel');
        $inversionModel = $this->model('InversionModel');

        $clientes = $clienteModel->contarClientes();
        $contratos = $inversionModel->contarContratos();
        $pagosEfectuados = $this->pagoModel->contarPagosEfectuados();
        $pagosPendientes = $this->pagoModel->contarPagosPendientes();
        
        $datosIngresos = $inversionModel->obtenerIngresosMensuales();

        // Generar los últimos 12 meses
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("-$i months"));
            $meses[$mes] = 0;
        }

        // Reemplazar los montos reales
        foreach ($datosIngresos as $fila) {
            $meses[$fila['mes']] = (float)$fila['total'];
        }

        // Separar en arrays de etiquetas y datos
        $labels = array_keys($meses);
        $data = array_values($meses);


        $this->view('admin/index', [
            'clientes' => $clientes,
            'contratos' => $contratos,
            'pagosEfectuados' => $pagosEfectuados,
            'pagosPendientes' => $pagosPendientes,
            'labelsIngresos' => $labels,
            'dataIngresos' => $data
        ]);
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
            // Usa el id de inversión para seleccionar un color
            $color = $colores[$pago['inversion_id'] % count($colores)];


            $eventos[] = [
                'title' => 'S/' . $pago['monto'],
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
