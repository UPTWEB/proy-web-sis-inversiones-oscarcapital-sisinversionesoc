<?php
require_once '../core/Controller.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../libreries/MiPDF.php';

class ContratosController extends Controller
{
    private $inversionModel;
    private $PagoModel;
    public function __construct()
    {
        $this->inversionModel = $this->model('InversionModel');
        $this->PagoModel = $this->model('PagoModel');
    }

    public function index()
    {
        $inversiones = $this->inversionModel->listar();
        $this->view('admin/contratos/contratos', ['inversiones' => $inversiones]);
    }

    public function beneficiario($id)
    {
        $inversion = $this->inversionModel->ver($id);
        $this->view('admin/contratos/verbeneficiario', ['inversion' => $inversion]);
    }

    public function calendario($id)
    {
        $this->view('admin/contratos/calendario', ['id' => $id]);
    }

    public function eventosCalendario($id)
    {
        $pagos = $this->PagoModel->proximosPagosPendientesPorInversion($id);
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

    public function ver($id)
    {
        $inversion = $this->inversionModel->ver($id);
        $pago = $this->PagoModel->verPorInversion($id);
        if (!$inversion) {
            header('Location: /contratos');
            exit;
        }

        // Suprime los warnings para evitar la salida de texto antes del PDF
        error_reporting(0);


        $pdf = new MiPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('NextMind');
        $pdf->SetAuthor('NextMind');
        $pdf->SetTitle('Contrato de Inversión');;
        $pdf->SetMargins(20, 31, 20);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setImageScale(scale: PDF_IMAGE_SCALE_RATIO);
        $pdf->setFontSubsetting(true);

        $pdf->AddPage();

        $html = $this->generarContenidoContrato($inversion, $pago);

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('Contrato_' . $inversion['id'] . '.pdf', 'I');
    }

    private function generarContenidoContrato($inversion, $pago)
    {
        $nombrecompleto = $inversion['nombres'] . ' ' . $inversion['apellido_paterno'] . ' ' . $inversion['apellido_materno'];
        $dni = $inversion['dni'];
        $estadocivil = $inversion['estado_civil'];
        $direccion = $inversion['direccion'];

        $partes = explode(',', $direccion);
        $domicilio = trim($partes[0]);

        // Separar distrito, provincia y departamento
        $ubicacion = isset($partes[1]) ? explode('-', $partes[1]) : [];

        $distrito = isset($ubicacion[0]) ? trim($ubicacion[0]) : '';
        $provincia = isset($ubicacion[1]) ? trim($ubicacion[1]) : '';
        $departamento = isset($ubicacion[2]) ? trim($ubicacion[2]) : '';

        $moneda = $inversion['moneda'];
        $monto = $inversion['monto'];
        $montotexto = $this->numeroATexto($monto);

        switch ($moneda) {
            case 'PEN':
                $monedaTexto = 'nuevos soles';
                $simbolo = 'S/';
                $origenMoneda = 'nacional';
                break;
            case 'USD':
                $monedaTexto = 'dólares estadounidenses';
                $simbolo = '$';
                $origenMoneda = 'extranjera';

                break;
            case 'CLP':
                $monedaTexto = 'pesos chilenos';
                $simbolo = 'CLP$';
                $origenMoneda = 'extranjera';

                break;
            default:
                $monedaTexto = 'moneda desconocida';
                $simbolo = '';
                break;
        }

        $montoSimbolo = $simbolo . $monto;
        $montotexto = $montotexto . ' ' . $monedaTexto;

        $meses = $inversion['meses'];

        $mesesTexto = $this->numeroATextoMeses($meses);

        $plan = (int)$inversion['plan_inversion'];

        $porcentaje = $inversion['porcentaje'];

        $ganancia = $pago['monto'];
        $gananciaTexto = $this->numeroATexto($ganancia) . ' ' . $monedaTexto;
        $gananciaSimbolo = $simbolo . $ganancia;

        $primerpago = $pago['primer_pago'];

        $primerpagoTexto = $this->fechaATexto($primerpago);

        $diaPago = substr($primerpago, 8, 2);

        $fechaInicio = $inversion['fecha_inicio'];

        $fechaFormateada = str_replace('-', '/', $fechaInicio);


        switch ($plan) {
            case 1:
                $frecuencia = 'MENSUAL';
                break;
            case 6:
                $frecuencia = 'SEMESTRAL';
                break;
            case 12:
                $frecuencia = 'ANUAL';
                break;
            default:
                $frecuencia = 'CADA ' . $plan . ' MESES'; // Ej: cada 3 meses
                break;
        }

        $imgPath = __DIR__ . '/../../../public/images/resources/firmaAbogado.png';
        $imgData = base64_encode(file_get_contents($imgPath));
        $src = 'data:image/png;base64,' . $imgData;
        // Aquí va TODO el contenido del contrato en HTML.
        $hoy = date('d \d\e F \d\e Y', time()); // ej: 09 de mayo de 2025
        $html = <<<EOD
                <style>
                    h2 { 
                        font-size: 14pt; 
                        text-align: center; 
                        margin-bottom: 20px;
                        font-weight: bold;
                    }
                    p { 
                        font-family: serif;
                        font-size: 11pt; 
                        line-height: 1.2; 
                        text-align: justify;
                        margin-bottom: 5px;
                    }
                    .clausula { 
                        font-family: serif;
                        margin-bottom: 5px; 
                    }
                    .center { text-align: center; }
                    .bold { font-weight: bold; }
                    .underline { text-decoration: underline; }
                    .signature-section {
                        margin-top: 40px;
                        page-break-inside: avoid;
                    }
                    .signature-table {
                        width: 100%;
                    }
                    .signature-table td {
                        vertical-align: top;
                        width: 50%;
                    }
                    .lawyer-section {
                        font-size: 10pt; 
                        text-align: left;
                    }
                </style>
                    <h2>CONTRATO DE MUTUO DE DINERO A TÍTULO ONEROSO<br/>CONTRATO N° xxxxx-2025-CMA</h2>

                    <p class="clausula">Conste por el presente documento el contrato de mutuo que celebran de una parte <strong>CORPORACION SAVIO S.A.C.</strong>, 
                    empresa de personería jurídica inscrita en la Partida Registral N° 11177702 de la Zona Registral XIII con <strong>RUC N°</strong> 20613209591 
                    con domicilio en Pasaje Bolognesi #181, Distrito, Provincia y Departamento de Tacna, debidamente representado por su Gerente Don <strong>OSCAR JOSUE SANCHEZ VILCA</strong> 
                    identificado con <strong>DNI N° 46130736</strong> a quien en lo sucesivo se denominara <strong>EL MUTUATARIO,</strong> y de otra parte, la parte identificada como $nombrecompleto, 
                    identificado con <strong>D.N.I. Nº $dni</strong>, de estado civil $estadocivil y con domicilio $domicilio, Distrito $distrito, Provincia $provincia y Departamento $departamento 
                    quien en lo sucesivo se denominará <strong>EL MUTUANTE</strong>; en los términos contenidos en las cláusulas siguientes:</p>

                    <p class="clausula"><strong>ANTECEDENTES:</strong></p>
                    <p class="clausula"><strong>PRIMERA.-</strong> <strong>EL MUTUATARIO</strong> es una persona jurídica, dedicada a las actividades de
                    consultoría de gestión comercial, y actividades auxiliares derivadas de acciones comerciales de
                    inversión múltiple, que desarrolla su actividad en forma independiente.</p>
                    
                    <p class="clausula"><strong>SEGUNDA.-</strong> <strong>EL MUTUATARIO</strong>,con el fin de desarrollar sus actividades comerciales y de
                    gestión de inversión, requiere contar con un capital de trabajo ascendente a la suma de $montoSimbolo ($montotexto) que <strong>EL MUTUANTE</strong> está dispuesto a
                    entregarle en calidad de préstamo.
                    </p>

                    <p class="clausula"><strong>TERCERA.-</strong> <strong>EL MUTUANTE</strong> es una persona natural, con capacidad absoluta y en pleno
                    ejercicio de sus obligaciones a título personal, manifiesta contar con capacidad financiera de
                    origen lícito, y la libre disposición del mismo para fines del presente contrato. El MUTUANTE
                    declara en este acto que los fondos aportados provienen de la siguiente actividad lícita:
                    ........................................................................................
                    EL MUTUANTE declara que producto
                    de sus actividades económicas lícitas tiene en promedio un ingreso mensual de
                    ......................................., y un ingreso promedio anual de ........................................
                    </p>

                    <p class="clausula"><strong>OBJETO DEL CONTRATO:</strong></p>
                    <p class="clausula"><strong>CUARTA.-</strong> Por el presente contrato, EL MUTUANTE se obliga a entregar en mutuo, en favor
                    de EL MUTUATARIO, la suma de dinero ascendente $montoSimbolo ($montotexto). 
                    EL MUTUATARIO, a su turno, se obliga a devolver a EL MUTUANTE la
                    referida suma de dinero en la forma y oportunidad pactadas en las cláusulas siguientes
                    </p>

                    <p class="clausula"><strong>QUINTA.-</strong> En caso de fallecimiento de <strong>EL MUTUANTE</strong>, el rendimiento de la inversión
                    realizada será pagado a favor del beneficiario que éste haya consignado en el Anexo I al presente
                    contrato. Dichos servicios serán prestados de acuerdo con lo estipulado en el presente contrato.
                    </p>

                    <p class="clausula"><strong>OBLIGACIONES DE LAS PARTES:</strong></p>

                    <p class="clausula"><u><strong>SEXTA.-</strong></u> <strong>EL MUTUANTE</strong> se obliga a entregar la suma de dinero objeto de la prestación a su
                    cargo en el momento de la firma de este documento, asumiendo además la obligación de suscribir
                    la <i>“Declaración jurada de origen lícito de fondos y prevención de lavado de activos”</i> que se
                    adjunta al presente contrato. EL MUTUANTE deja constancia que el MUTUATARIO ha cumplo
                    con el <i>deber mínimo de diligencia</i> de indagar que el origen de los fondos son lícitos. Se deja
                    constancia que el MUTUANTE asumirá la obligación del pago de sus respectivos impuestos que 
                    correspondan por la renta generada, salvo que su inversión haya sido destinada a un agente de
                    retención, situación en la cual ya se habría descontado el pago de los impuestos respectivos.
                    </p>

                    <p class="clausula"><strong>SEPTIMA.-</strong> 
                    <strong>EL MUTUATARIO</strong> declara haber recibido conforme la referida suma mutuada,
                    en dinero en XXXX, en moneda $origenMoneda y en la cantidad a que se refiere la cláusula segunda.
                    El MUTUATARIO deja constancia que cumplirá oportunamente con el pago de los impuestos
                    que correspondan por el interés generado a su favor.
                    </p>
                    
                    <p class="clausula"><u><strong>OCTAVA.-</strong></u>
                    <strong>EL MUTUATARIO</strong> se obliga a devolver el íntegro del dinero objeto del mutuo,
                    en los $mesesTexto meses posteriores a la suscripción del presente contrato, y abonar la ganancia que surge
                    de la presente, A TRAVÉS DEL PAGO DE UNA ARMADA $frecuencia, contando 30 días
                    calendario a partir del día siguiente de la suscripción del presente contrato, teniendo en cuenta las
                    contingencias emergentes que establece Artículo 1315º del Código Civil Peruano. Asimismo, por
                    la suma y en la oportunidad que se indican a continuación:
                    <br>
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;01. $montoSimbolo el día 09 de junio 2025 más el interés convenido. 
                    </p>


                    <p class="clausula"><u><strong>NOVENA.-</strong></u>
                    <strong>EL MUTUATARIO </strong>
                    se obliga a cumplir fielmente con el cronograma de pagos
                    descrito en la cláusula anterior. En caso de resolver de forma unilateral el presente pacto por parte
                    del EL MUTUANTE, EL MUTUATARIO solo estará obligado a devolver el 100% del
                    préstamo, más el XX% de ganancias prorrateado a la fecha de la resolución de la presente.
                    </p>


                    <p class="clausula"><u><strong>DECIMA.-</strong></u>
                    Las partes acuerdan que EL MUTUATARIO devolverá la suma de dinero objeto del
                    mutuo, en la misma moneda y cantidad recibida, debiendo efectuar el pago de cada armada vía
                    depósito, transferencia bancaria, o en efectivo según corresponda.
                    </p>

                    <p class="clausula"><u><strong>UNDECIMA.-</strong></u>
                    El pago deberá realizarse de forma directa a favor de <strong>EL MUTUANTE</strong>, quien
                    suscribe el presente contrato. En caso <strong>EL MUTUANTE</strong> requiere una modalidad de pago
                    diferente pondrá en conocimiento del <strong>EL MUTUATARIO</strong>, 10 días hábiles previos a la fecha de
                    pago. El pago es personal e intransferible, a favor de quien suscribe el contrato, en caso de delegar
                    este derecho, se realizará bajo poder por escritura pública inscrita en el registro de “mandatos y
                    poderes” de los Registros Públicos (SUNARP)
                    </p>

                    <p class="clausula"><u><strong>PAGO DE GANANCIAS:</strong></p>

                    <p class="clausula"><u><strong>DUODECIMA.-</strong></u>
                    Ambas partes convienen en que el presente contrato de mutuo se celebra a
                    título oneroso, en consecuencia, <strong>EL MUTUATARIO</strong> está obligado al pago de ganancias
                    compensatorios en favor de <strong>EL MUTUANTE</strong>, de acuerdo con la tasa y forma de pago a que se
                    refiere la cláusula siguiente.
                    </p>

                    <p class="clausula"><u><strong>DÉCIMO TERCERA.-</strong></u>
                    Queda convenido que la tasa de ganancia compensatoria asciende al
                    $porcentaje% del total de la suma mutuada, el mismo que equivale a $gananciaSimbolo ($gananciaTexto) 
                    el abono se realizará el $primerpagoTexto; ( si es mensual se abona los dias $diaPago de cada Mes) 
                    en caso de que dicha fecha coincida con un día domingo, el pago se efectuará el día
                    lunes siguiente, y si coincidiera con un día feriado, se realizará el siguiente día hábil bancario
                    </p>

                    <p class="clausula"><u><strong>GASTOS Y TRIBUTOS DEL CONTRATO:</strong></p>

                    <p class="clausula"><u><strong>DÉCIMO CUARTA.-</strong></u>
                    Las partes acuerdan que todos los gastos y tributos que origine la
                    celebración y ejecución de este contrato serán asumidos por <strong>EL MUTUANTE</strong>.
                    </p>

                    <p class="clausula"><u><strong>COMPETENCIA TERRITORIAL:</strong></p>

                    <p class="clausula"><u><strong>DÉCIMO QUINTA.-</strong></u>
                    Para efectos de cualquier controversia que se genere con motivo de la
                    celebración y ejecución de este contrato, las partes se someten a la competencia territorial de la
                    Corte Superior de Justicia de Tacna. Asimismo, previo a ello, se convocará a una audiencia de
                    conciliación en el Centro de Conciliación Extrajudicial <strong>“AGORA”</strong> R. D. N° 525-2013-
                    JUS/DGDP-DCMA, sito en Calle Presbitero Andia – Agrup. Jorge Basadre Grohmann Block “C”
                    Dpto. 204 2do Piso – Cercado de Tacna
                    </p>

                    <p class="clausula"><u><strong>DOMICILIO:</strong></p>

                    <p class="clausula"><u><strong>DÉCIMO SEXTA.-</strong></u>
                    Para la validez de todas las comunicaciones y notificaciones a las partes,
                    con motivo de la ejecución de este contrato, ambas señalan como sus respectivos domicilios los
                    indicados en la introducción de este documento. El cambio de domicilio de cualquiera de las
                    partes surtirá efecto desde la fecha de comunicación de dicho cambio a la otra parte, por cualquier
                    medio escrito con recepción de fecha cierta.
                    </p>

                    <p class="clausula"><u><strong>APLICACIÓN SUPLETORIA DE LA LEY:</strong></p>

                    <p class="clausula"><u><strong>DÉCIMO SEPTIMA.-</strong></u>
                    En lo no previsto por las partes en el presente contrato, ambas se someten
                    a lo establecido por las normas del Código Civil y demás del sistema jurídico que resulten
                    aplicables.
                    <br>
                    En señal de conformidad las partes suscriben este documento en la ciudad de Tacna, a los XXXX.
                    <br><br><br><br><br><br>

                    </p>

                    <!-- Sigue todas las cláusulas QUINTA, SEXTA, etc., copiando el texto exacto del PDF -->
                    
                    <div class="signature-section">
                        <table class="signature-table">
                            <tr>
                                <td style="text-align: left;">
                                    <hr style="text-align: left; border: none; border-bottom: 1px solid #000; width: 90%; margin: 2px;">
                                    <div style="text-align: center;"><strong>EL MUTUANTE</strong></div>
                                    <br><br>
                                    NOMBRE: ________________________<br><br>
                                    DNI: ___________________________<br><br>
                                </td>
                                <td style="text-align: center;">
                                    <hr style="text-align: left; border: none; border-bottom: 1px solid #000; width: 90%; margin: 2px;">
                                    <div style="text-align: center;"><strong>EL MUTUATARIO</strong></div>
                                    <br><br>
                                    <strong>CORPORACIÓN SAVIO S.A.C.</strong><br>
                                    OSCAR JOSUE SANCHEZ VILCA<br>
                                    <strong>GERENTE GENERAL</strong><br><br>
                                </td>
                            </tr>
                        </table>
                        <br><br>
                        <div class="lawyer-section">
                            <img src="$src">
                            <hr style="text-align: left; border: none; border-bottom: 1px solid #000; width: 40%;">
                            <p><strong>Abog. Miguel Angel Farfan Vargas</strong><br>
                            <strong>ICAT N° 02224</strong></p>
                            
                            <p><strong>NORMAS APLICABLES:</strong><br>
                            Art. 1648 (Definición de mutuo)<br>
                            Art. 1663 (Pago de ganancias).</p>
                        </div>
                    </div>
                                  
                    <h2 style="font-size: 11pt;">ANEXO I</h2>
                    <h2 style="font-size: 11pt;">DECLARACION DE BENEFICIARIO EN CASO DE FALLECIMIENTO O ACCIDENTE</h2>

                    <p class="clausula">
                    En aplicación de los dispuestos en el contrato de mutuo acuerdo, <strong>EL CLIENTE</strong> declara que su
                    beneficiario en caso de fallecimiento o accidente será:
                    </p>
                    <h4>BENEFICIARIO 1: EN PRIMERA INSTANCIA</h4>
                    
                    <table border="1" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px; line-height: 3;">NOMBRES:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">APELLIDOS:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">DNI:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">CELULAR:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">DIRECCIÓN:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                    </table>

                    <h4>BENEFICIARIO 2: EN SEGUNDA INSTANCIA</h4>
                    
                    <table border="1" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px; line-height: 3;">NOMBRES:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">APELLIDOS:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">DNI:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">CELULAR:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; line-height: 3;">DIRECCIÓN:</td>
                            <td style="padding: 10px; line-height: 3;">&nbsp;</td>
                        </tr>
                    </table>

                    <h4>FECHA:$fechaFormateada</h4>
                    <br><br><br><br><br><br>

                    <div style="text-align: center; margin-top: 20px;">
                        <hr style="border: none; border-bottom: 1px solid #000; width: 30%;">
                        <center>EL MUTUANTE</center>
                    </div>
                    <br><br><br><br><br>
                    <h4>NOMBRES Y APELLIDOS: </h4>
                    <h4>DNI: </h4>

                    <br>
                    <h2 style="font-size: 11pt;">DECLARACION DE BENEFICIARIO EN CASO DE FALLECIMIENTO O ACCIDENTE</h2>
                    
                    <p class="clausula">
                    Por el presente documento, declaro bajo juramento, lo siguiente:
                    </p>
 
                    <table style="table-layout: auto; border-collapse: collapse; width: 100%;font-family: serif; font-size: 12px; text-align: center;">
                        <tr>
                            <td colspan="11" style="border: 1px solid #000; padding: 4px; font-weight: bold; line-height: 3; text-align: left;">Nombres y Apellidos:</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; line-height: 1; width: 18%;">Ser de nacionalidad:</td>
                            <td colspan="2" style="border: 1px solid #000; line-height: 1;width: 14%;">Peruana</td>
                            <td style="border: 1px solid #000; width: 8%;" ></td>
                            <td colspan="7" style="border: 1px solid #000; line-height: 1;width: 60%; text-align: left;">Otra (especificar):</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000;width: 18%;">De estado civil:</td>
                            <td style="border: 1px solid #000;width: 8%;">Soltero</td>
                            <td style="border: 1px solid #000;width: 6%;"></td>
                            <td style="border: 1px solid #000;width: 8%">Casado</td>
                            <td style="border: 1px solid #000;width: 6%;"></td>
                            <td style="border: 1px solid #000;width: 10%;">Viudo</td>
                            <td style="border: 1px solid #000;width: 4%;"></td>
                            <td style="border: 1px solid #000;width: 16%;">Divorciado</td>
                            <td style="border: 1px solid #000;width: 4%;"></td>
                            <td style="border: 1px solid #000;width: 16%;">Conviviente</td>
                            <td style="border: 1px solid #000;width: 4%;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; width: 18%;">Identificarme con:</td>
                            <td style="border: 1px solid #000; width: 8%;"></td>
                            <td style="border: 1px solid #000; width: 6%;">DNI</td>
                            <td colspan="2" style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                            <td colspan="3" style="border: 1px solid #000;">Pasaporte o C. Extranjería</td>
                            <td colspan="2" style="border: 1px solid #000;">N°</td>
                        </tr>
                        <tr>
                            <td colspan="11" style="border: 1px solid #000; line-height: 3; text-align: left;">Domicilio personal:</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border: 1px solid #000; text-align: left;">Distrito:</td>
                            <td colspan="5" style="border: 1px solid #000; text-align: left;">Provincia:</td>
                            <td colspan="4" style="border: 1px solid #000; text-align: left;">Departamento:</td>
                        </tr>
                        <tr>
                            <td colspan="5" style="border: 1px solid #000; line-height: 1.5; text-align: left;">Precise si tiene algún familiar que es o fue alto funcionario público en los últimos 0 3 años: (colocar nombres)</td>
                            <td colspan="6" style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td colspan="9" style="border: 1px solid #000; width:90%; text-align: left;">¿Registra usted alguna sentencia judicial penal?</td>
                            <td style="border: 1px solid #000; text-align: center; width:5%">SI</td>
                            <td style="border: 1px solid #000; text-align: center; width:5%">NO</td>
                        </tr>
                        <tr>
                            <td colspan="5" style="border: 1px solid #000; line-height: 3; text-align: left; width:46%;">¿A qué actividad económica se dedica?</td>
                            <td colspan="6" style="border: 1px solid #000; width:54%"></td>
                        </tr>
                        <tr>
                            <td colspan="8" style="border: 1px solid #000; width: 76%; text-align: left;">¿Presentó declaración jurada anual de impuestos el año anterior?</td>
                            <td style="border: 1px solid #000; text-align: center; width: 5%">Sí</td>
                            <td style="border: 1px solid #000; text-align: center; width: 5%">No</td>
                            <td style="border: 1px solid #000; text-align: center; width: 14%">No requiero</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border: 1px solid #000; line-height: 7; vertical-align: top; text-align: left;">El importe invertido proviene de (precise):</td>
                            <td colspan="3" style="border: 1px solid #000;">Trabajo Independiente</td>
                            <td colspan="2" style="border: 1px solid #000;">Trabajo dependiente</td>
                            <td colspan="2" style="border: 1px solid #000;">Otro:</td>
                        </tr>
                    </table>


                    <p class="clausula">
                    Adicionalmente, declaro a la fecha:
                    </p>
        
                    <table style="table-layout: auto; border-collapse: collapse; width: 100%;font-family: serif; font-size: 12px; text-align: center;">
                        <tr>
                            <td style="border: 1px solid #000; line-height: 1.5; text-align: justify; width:90%;">Tener vínculo de parentesco dentro del segundo grado de consanguinidad (padres o hermanos) 
                            o primero de afinidad (esposo o conviviente) con una persona sentencia por los delitos de lavado de activos, tráfico ilícito de drogas, minería ilegal, terrorismo, 
                            trata de personas, extorsión, sicariato o crimen organizado:</td>
                            <td style="border: 1px solid #000; line-height: 1.5; text-align: left; width: 5%;">SI</td>
                            <td style="border: 1px solid #000; line-height: 1.5; text-align: left; width: 5%;">NO</td>
                        </tr>
                    </table>

                    <p class="clausula">
                    Afirmo y ratifico todo lo manifestado en la presente declaración jurada, en señal de lo cual la firmo,
                    en el lugar y fecha que a continuacion indico:
                    </p>

                    <table style="table-layout: auto; border-collapse: collapse; width: 100%; font-family: serif; font-size: 12px; text-align: center;">
                        <tr>
                            <td style="border: 1px solid #000; width: 18%;"></td>
                            <td style="border: 1px solid #000; width: 19%;"></td>
                            <td style="border: 1px solid #000; width: 19%;"></td>
                            <td style="width: 18%;"></td> <!-- Espacio en blanco -->
                            <td rowspan="2" style="border: 1px solid #000; width: 8%;">FECHA</td>
                            <td style="border: 1px solid #000; width: 6%;"></td>
                            <td style="border: 1px solid #000; width: 6%;"></td>
                            <td style="border: 1px solid #000; width: 6%;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000;">DISTRITO</td>
                            <td style="border: 1px solid #000;">PROVINCIA</td>
                            <td style="border: 1px solid #000;">DEPARTAMENTO</td>
                            <td></td> <!-- Espacio en blanco -->
                            <td style="border: 1px solid #000;">DD</td> <!-- celda vacía debajo de DD -->
                            <td style="border: 1px solid #000;">MM</td> <!-- celda vacía debajo de MM -->
                            <td style="border: 1px solid #000;">AA</td> <!-- celda vacía debajo de AA -->
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="4"style="border: 1px solid #000; line-height: 3"></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="4"style="border: 1px solid #000;">FIRMA</td>
                        </tr>
                    </table>


                EOD;

        return $html;
    }


    function numeroATexto($numero)
    {
        $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $especiales = [10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince'];
        $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
        $centenas = ['', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

        $numero = number_format($numero, 2, '.', '');
        list($entero, $decimal) = explode('.', $numero);

        if ($entero == 0) {
            $texto = 'cero';
        } else {
            $texto = '';
            if ($entero >= 1000) {
                $miles = floor($entero / 1000);
                $resto = $entero % 1000;

                if ($miles == 1) {
                    $texto .= 'mil';
                } else {
                    $texto .= $this->convertirCentenas($miles) . ' mil';
                }

                if ($resto > 0) {
                    $texto .= ' ' . $this->convertirCentenas($resto);
                }
            } else {
                $texto .= $this->convertirCentenas($entero);
            }
        }

        return trim($texto) . ' y ' . str_pad($decimal, 2, '0', STR_PAD_RIGHT) . '/100';
    }

    function convertirCentenas($num)
    {
        $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $especiales = [10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince'];
        $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
        $centenas = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

        if ($num == 100) return 'cien';

        $c = floor($num / 100);
        $d = floor(($num % 100) / 10);
        $u = $num % 10;

        $texto = '';

        if ($c > 0) $texto .= $centenas[$c] . ' ';

        if ($d == 1 && $u <= 5) {
            $texto .= $especiales[$d * 10 + $u];
        } elseif ($d == 2 && $u != 0) {
            $texto .= 'veinti' . $unidades[$u];
        } else {
            if ($d > 0) $texto .= $decenas[$d];
            if ($u > 0 && $d != 0) $texto .= ' y ';
            if ($u > 0) $texto .= $unidades[$u];
        }

        return trim($texto);
    }

    function numeroATextoMeses($numero)
    {
        $unidad = [
            '',
            'uno',
            'dos',
            'tres',
            'cuatro',
            'cinco',
            'seis',
            'siete',
            'ocho',
            'nueve',
            'diez',
            'once',
            'doce',
            'trece',
            'catorce',
            'quince',
            'dieciséis',
            'diecisiete',
            'dieciocho',
            'diecinueve',
            'veinte'
        ];

        $decenas = [
            '',
            '',
            'veinti',
            'treinta',
            'cuarenta',
            'cincuenta',
            'sesenta',
            'setenta',
            'ochenta',
            'noventa'
        ];

        $centenas = [
            '',
            'ciento',
            'doscientos',
            'trescientos',
            'cuatrocientos',
            'quinientos',
            'seiscientos',
            'setecientos',
            'ochocientos',
            'novecientos'
        ];

        if ($numero == 0) return 'cero';
        if ($numero == 100) return 'cien';

        $c = floor($numero / 100);
        $d = floor(($numero % 100) / 10);
        $u = $numero % 10;
        $texto = '';

        if ($c > 0) {
            $texto .= $centenas[$c] . ' ';
        }

        $resto = $numero % 100;
        if ($resto <= 20) {
            $texto .= $unidad[$resto];
        } else {
            $texto .= $decenas[$d];
            if ($d >= 3 && $u > 0) {
                $texto .= ' y ';
            }
            if ($d == 2 && $u > 0) {
                $texto .= $unidad[$u]; // veintiuno, veintidós, etc.
            } elseif ($d != 2) {
                $texto .= $unidad[$u];
            }
        }

        return trim($texto);
    }

    function fechaATexto($fechaISO)
    {
        // Crear objeto DateTime
        $meses = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        ];

        $fecha = new DateTime($fechaISO);
        $dia = $fecha->format('d');
        $mes = (int)$fecha->format('m');
        $anio = $fecha->format('Y');

        return "{$dia} {$meses[$mes]} {$anio}";
    }
}
