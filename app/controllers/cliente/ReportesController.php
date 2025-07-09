<?php
require_once '../core/Controller.php';

class ReportesController extends Controller
{
    public function index()
    {
        $usuarioModel = $this->model('UsuarioModel');
        $inversionModel = $this->model('InversionModel');
        $pagoModel = $this->model('PagoModel');

        // Obtener id del cliente
        $sesion_id = $_SESSION['sesion_id'];
        $id = $usuarioModel->obtenerIdCliente($sesion_id);


        $datosIngresos = $inversionModel->obtenerInversionesMensualesPorCliente($id);
        $pagosMensuales = $pagoModel->obtenerPagosMensualesporCliente($id);


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

        $this->view('cliente/reportes', [
            'labels' => array_keys($mesesPagos),
            'dataIngresos' => array_values($mesesIngresos),
            'dataPagos' => array_values($mesesPagos),
        ]);
    }
}
