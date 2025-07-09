<?php
require_once '../core/Controller.php';

class ExportController extends Controller
{
    private $clienteModel;
    private $inversionModel;
    private $pagoModel;

    public function __construct()
    {
        $this->clienteModel = $this->model('ClienteModel');
        $this->inversionModel = $this->model('InversionModel');
        $this->pagoModel = $this->model('PagoModel');
    }

    public function clientes($formato = 'excel')
    {
        $clientes = $this->clienteModel->listar();
        
        // Procesar datos para agregar campos faltantes
        foreach ($clientes as &$cliente) {
            $cliente['celular'] = $cliente['celular1'] ?? '';
            // estado ya viene de la consulta
        }
        
        if ($formato === 'csv') {
            $this->exportarCSV($clientes, 'clientes', [
                'dni' => 'DNI',
                'nombres' => 'Nombres',
                'apellido_paterno' => 'Apellido Paterno',
                'apellido_materno' => 'Apellido Materno',
                'direccion' => 'Dirección',
                'celular' => 'Celular',
                'estado' => 'Estado'
            ]);
        } else {
            $this->exportarExcel($clientes, 'clientes', [
                'dni' => 'DNI',
                'nombres' => 'Nombres',
                'apellido_paterno' => 'Apellido Paterno',
                'apellido_materno' => 'Apellido Materno',
                'direccion' => 'Dirección',
                'celular' => 'Celular',
                'estado' => 'Estado'
            ]);
        }
    }

    public function inversiones($formato = 'excel')
    {
        $inversiones = $this->inversionModel->listar();
        
        // Procesar datos para agregar campos faltantes
        foreach ($inversiones as &$inversion) {
            $inversion['cliente_nombre'] = trim($inversion['nombres'] . ' ' . $inversion['apellido_paterno'] . ' ' . $inversion['apellido_materno']);
            $inversion['fecha_fin'] = $inversion['fecha_calculada'] ?? '';
            $inversion['planInversion'] = $inversion['plan_inversion'] ?? '';
            // monto y estado ya vienen de la consulta
        }
        
        if ($formato === 'csv') {
            $this->exportarCSV($inversiones, 'inversiones', [
                'id' => 'Nro',
                'cliente_nombre' => 'Cliente',
                'fecha_inicio' => 'Inscripción',
                'fecha_fin' => 'Finalización',
                'planInversion' => 'Plan',
                'meses' => 'Meses',
                'monto' => 'Monto',
                'estado' => 'Estado'
            ]);
        } else {
            $this->exportarExcel($inversiones, 'inversiones', [
                'id' => 'Nro',
                'cliente_nombre' => 'Cliente',
                'fecha_inicio' => 'Inscripción',
                'fecha_fin' => 'Finalización',
                'planInversion' => 'Plan',
                'meses' => 'Meses',
                'monto' => 'Monto',
                'estado' => 'Estado'
            ]);
        }
    }

    public function pagosEfectuados($formato = 'excel')
    {
        $pagos = $this->pagoModel->pagosEfectuados();
        
        // Procesar datos para agregar campos faltantes
        foreach ($pagos as &$pago) {
            $pago['cliente_nombre'] = trim($pago['nombres'] . ' ' . $pago['apellido_paterno'] . ' ' . $pago['apellido_materno']);
            $pago['fecha_pago'] = $pago['fecha'] ?? '';
            // estado ya viene de la consulta
        }
        
        if ($formato === 'csv') {
            $this->exportarCSV($pagos, 'pagos_efectuados', [
                'id' => 'Nro',
                'cliente_nombre' => 'Cliente',
                'monto' => 'Monto',
                'numero_pago' => 'N° Pago',
                'fecha_pago' => 'Fecha',
                'estado' => 'Estado'
            ]);
        } else {
            $this->exportarExcel($pagos, 'pagos_efectuados', [
                'id' => 'Nro',
                'cliente_nombre' => 'Cliente',
                'monto' => 'Monto',
                'numero_pago' => 'N° Pago',
                'fecha_pago' => 'Fecha',
                'estado' => 'Estado'
            ]);
        }
    }

    public function pagosPendientes($formato = 'excel')
    {
        $verTodos = isset($_GET['todos']) && $_GET['todos'] == '1';
        
        if ($verTodos) {
            $pagos = $this->pagoModel->pagosPendientes();
        } else {
            $pagos = $this->pagoModel->proximosPagosPendientes();
        }
        
        $nombreArchivo = $verTodos ? 'pagos_pendientes_todos' : 'pagos_pendientes_proximos';
        
        // Procesar datos para agregar campos faltantes
        foreach ($pagos as &$pago) {
            $pago['cliente_nombre'] = trim($pago['nombres'] . ' ' . $pago['apellido_paterno'] . ' ' . $pago['apellido_materno']);
            $pago['fecha_vencimiento'] = $pago['fecha'] ?? '';
            // estado ya viene de la consulta
        }
        
        if ($formato === 'csv') {
            $this->exportarCSV($pagos, $nombreArchivo, [
                'id' => 'Nro',
                'cliente_nombre' => 'Cliente',
                'monto' => 'Monto',
                'numero_pago' => 'N° Pago',
                'fecha_vencimiento' => 'Fecha Vencimiento',
                'estado' => 'Estado'
            ]);
        } else {
            $this->exportarExcel($pagos, $nombreArchivo, [
                'id' => 'Nro',
                'cliente_nombre' => 'Cliente',
                'monto' => 'Monto',
                'numero_pago' => 'N° Pago',
                'fecha_vencimiento' => 'Fecha Vencimiento',
                'estado' => 'Estado'
            ]);
        }
    }

    private function exportarCSV($datos, $nombreArchivo, $columnas)
    {
        $filename = $nombreArchivo . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Escribir encabezados
        fputcsv($output, array_values($columnas), ';');
        
        // Escribir datos
        foreach ($datos as $fila) {
            $filaExportar = [];
            foreach (array_keys($columnas) as $campo) {
                $valor = isset($fila[$campo]) ? $fila[$campo] : '';
                $filaExportar[] = $valor;
            }
            fputcsv($output, $filaExportar, ';');
        }
        
        fclose($output);
        exit;
    }

    private function exportarExcel($datos, $nombreArchivo, $columnas)
    {
        // Cargar autoload primero
        require_once '../vendor/autoload.php';
        
        // Verificar si PhpSpreadsheet está disponible
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Fallback a CSV si no está disponible
            $this->exportarCSV($datos, $nombreArchivo, $columnas);
            return;
        }
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar encabezados
        $col = 1;
        foreach ($columnas as $titulo) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($columnLetter . '1', $titulo);
            $col++;
        }
        
        // Estilo para encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ];
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columnas));
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        
        // Escribir datos
        $fila = 2;
        foreach ($datos as $registro) {
            $col = 1;
            foreach (array_keys($columnas) as $campo) {
                $valor = isset($registro[$campo]) ? $registro[$campo] : '';
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($columnLetter . $fila, $valor);
                $col++;
            }
            $fila++;
        }
        
        // Ajustar ancho de columnas
        for ($i = 1; $i <= count($columnas); $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
        
        // Configurar descarga
        $filename = $nombreArchivo . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}