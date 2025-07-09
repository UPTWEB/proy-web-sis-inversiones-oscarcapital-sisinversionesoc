<?php
require_once '../core/Controller.php';

class ReportesController extends Controller
{
    public function index()
    {
        $inversionModel = $this->model('InversionModel');
        $pagoModel = $this->model('PagoModel');

        $pagosMensuales = $pagoModel->obtenerPagosRecaudadosMensuales();
        $datosIngresos = $inversionModel->obtenerIngresosMensuales();
        $estadoPagosMesActual = $pagoModel->obtenerCantidadPagosMesActual();

        // Generar últimos 12 meses
        $mesesIngresos = [];
        $mesesPagos = [];

        for ($i = 11; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("-$i months"));
            $mesesIngresos[$mes] = 0;
            $mesesPagos[$mes] = 0;
        }

        foreach ($datosIngresos as $fila) {
            $mesesIngresos[$fila['mes']] = (float)$fila['total'];
        }

        foreach ($pagosMensuales as $fila) {
            $mesesPagos[$fila['mes']] = (float)$fila['total'];
        }

        // Procesar estadoPagosMesActual
        $dataEstadoPagos = [
            'Recaudado' => 0,
            'Pendiente' => 0
        ];

        foreach ($estadoPagosMesActual as $fila) {
            if ($fila['estado'] === true || $fila['estado'] === 't') {
                $dataEstadoPagos['Recaudado'] = (int)$fila['cantidad'];
            } else {
                $dataEstadoPagos['Pendiente'] = (int)$fila['cantidad'];
            }
        }

        // Top 5 clientes
        $topClientes = $inversionModel->obtenerTop5ClientesPorInversion();
        $labelsTop = [];
        $dataTop = [];

        foreach ($topClientes as $fila) {
            $labelsTop[] = $fila['cliente'];
            $dataTop[] = (float) $fila['monto_pen'];
        }

        $this->view('admin/reportes', [
            'labels' => array_keys($mesesPagos),
            'dataIngresos' => array_values($mesesIngresos),
            'dataPagos' => array_values($mesesPagos),
            'dataEstadoPagos' => $dataEstadoPagos,
            'labelsTopClientes' => $labelsTop,
            'dataTopClientes' => $dataTop
        ]);
    }
}
