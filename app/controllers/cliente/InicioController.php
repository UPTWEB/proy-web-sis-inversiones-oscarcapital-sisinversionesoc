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
        $inversionModel = $this->model('InversionModel');
        $usuarioModel = $this->model('UsuarioModel');

        // Obtener id del cliente
        $sesion_id = $_SESSION['sesion_id'];
        $id = $usuarioModel->obtenerIdCliente($sesion_id);

        $ganancias = $this->pagoModel->obtenerGananciaPorCliente($id);
        $cantidadInversiones = $inversionModel->contarInversionesActivasPorCliente($id);
        $montoInvertido = $inversionModel->sumarInversionesPorCliente($id);
        $montoGanado = $this->pagoModel->sumarPagosCanceladosPorCliente($id);
        
        $pagosMensuales = $this->pagoModel->obtenerPagosMensualesporCliente($id);

        // Generar los últimos 12 meses
        $mesesPagos = [];

        for ($i = 11; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("-$i months"));
            $mesesPagos[$mes] = 0;
        }


        // Reemplazar los montos reales
        foreach ($pagosMensuales as $fila) {
            $mesesPagos[$fila['mes']] = (float)$fila['total'];
        }

        // Separar en arrays de etiquetas y datos
        $labels = array_keys($mesesPagos);
        $data = array_values($mesesPagos);


        $this->view('cliente/index', [
            'id' => $id,
            'ganancias' => $ganancias,

            'cantidadInversiones' => $cantidadInversiones,
            'montoInvertido' => $montoInvertido,
            'montoGanado' => $montoGanado,
            'labelsIngresos' => $labels,
            'dataIngresos' => $data
        ]);
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
