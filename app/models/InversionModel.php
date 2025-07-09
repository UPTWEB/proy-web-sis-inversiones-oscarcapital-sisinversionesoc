<?php
class InversionModel
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function listar()
    {
        try {
            $sql = "SELECT 
                        i.id, 
                        (c.datos_personales).nombres,
                        (c.datos_personales).apellido_paterno,
                        (c.datos_personales).apellido_materno,
                        i.fecha_inicio, 
                        i.fecha_calculada,
                        i.plan_inversion, 
                        i.meses,
                        i.monto,
                        i.estado
                    FROM 
                        inversion i
                    INNER JOIN 
                        cliente c ON i.cliente_id = c.id
                    WHERE 
                        i.estado != 'cancelado' 
                    ORDER BY
                        i.id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $inversiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $inversiones;
        } catch (PDOException $e) {
            die("Error al listar inversiones: " . $e->getMessage());
        }
    }

    public function crear()
    {
        try {
            // Evaluamos si hay beneficiario 2
            $tieneBen2 = !empty($_POST['ben2_dni']);

            $sql = "INSERT INTO inversion(
                cliente_id, 
                plan_inversion, 
                porcentaje, 
                moneda, 
                monto, 
                meses, 
                fecha_inicio, 
                fecha_calculada, 
                nombre_banco, 
                cuenta_bancaria, 
                cuenta_interbancaria,
                billetera_movil, 
                celular,
                beneficiario1,
                parentesco1,
                beneficiario2,
                parentesco2,
                estado
            ) VALUES (
                :cliente_id,
                :plan_inversion, 
                :porcentaje, 
                :moneda, 
                :monto, 
                :meses, 
                :fecha_inicio, 
                :fecha_calculada, 
                :nombre_banco, 
                :cuenta_bancaria, 
                :cuenta_interbancaria,
                :billetera_movil, 
                :celular,
                ROW(:ben1_dni, :ben1_apellido_paterno, :ben1_apellido_materno, :ben1_nombres, :ben1_direccion, :ben1_celular1, :ben1_celular2)::persona,
                :parentesco1,
                :beneficiario2,
                :parentesco2,
                'pendiente'
            )";

            $stmt = $this->conn->prepare($sql);

            // Construye el beneficiario2 si aplica
            $beneficiario2 = $tieneBen2
                ? sprintf(
                    '(%s,%s,%s,%s,%s,%s,%s)',
                    $this->conn->quote($_POST['ben2_dni']),
                    $this->conn->quote($_POST['ben2_apellido_paterno']),
                    $this->conn->quote($_POST['ben2_apellido_materno']),
                    $this->conn->quote($_POST['ben2_nombres']),
                    $this->conn->quote($_POST['ben2_direccion']),
                    $this->conn->quote($_POST['ben2_celular1']),
                    $this->conn->quote($_POST['ben2_celular2'])
                ) . '::persona'
                : null;

            $stmt->execute([
                ':cliente_id'           => $_POST['cliente_id'],
                ':plan_inversion'       => $_POST['plan_inversion'],
                ':porcentaje'           => $_POST['porcentaje'],
                ':moneda'               => $_POST['moneda'],
                ':monto'                => $_POST['monto'],
                ':meses'                => $_POST['meses'],
                ':fecha_inicio'         => $_POST['fecha_inicio'],
                ':fecha_calculada'      => $_POST['fecha_calculada'],
                ':nombre_banco'         => $_POST['nombre_banco'],
                ':cuenta_bancaria'      => $_POST['cuenta_bancaria'],
                ':cuenta_interbancaria' => $_POST['cuenta_interbancaria'],
                ':billetera_movil'      => $_POST['billetera_movil'],
                ':celular'              => $_POST['celular'],

                // Beneficiario 1
                ':ben1_dni'              => $_POST['ben1_dni'],
                ':ben1_apellido_paterno' => $_POST['ben1_apellido_paterno'],
                ':ben1_apellido_materno' => $_POST['ben1_apellido_materno'],
                ':ben1_nombres'          => $_POST['ben1_nombres'],
                ':ben1_direccion'        => $_POST['ben1_direccion'],
                ':ben1_celular1'         => $_POST['ben1_celular1'],
                ':ben1_celular2'         => $_POST['ben1_celular2'],
                ':parentesco1'           => $_POST['parentesco1'],

                // Beneficiario 2 como tipo compuesto o null
                ':beneficiario2'         => $beneficiario2,
                ':parentesco2'           => $_POST['parentesco2'] ?? null,
            ]);
        } catch (PDOException $e) {
            die("Error al insertar inversión: " . $e->getMessage());
        }
    }

    public function ver($id)
    {
        try {
            $sql = "SELECT
                i.id,
                i.cliente_id,
                (c.datos_personales).dni as dni,
                (c.datos_personales).nombres as nombres,
                (c.datos_personales).apellido_paterno,
                (c.datos_personales).apellido_materno,
                (c.datos_personales).direccion as direccion,
                c.estado_civil,
                i.plan_inversion,
                i.porcentaje,
                i.moneda,
                i.monto,
                i.meses,
                i.fecha_inicio,
                i.fecha_calculada,
                i.nombre_banco,
                i.cuenta_bancaria,
                i.cuenta_interbancaria,
                i.billetera_movil,
                i.celular,

                -- Datos del Beneficiario Principal
                (i.beneficiario1).dni as ben1_dni,
                (i.beneficiario1).apellido_paterno as ben1_apellido_paterno,
                (i.beneficiario1).apellido_materno as ben1_apellido_materno,
                (i.beneficiario1).nombres as ben1_nombres,
                (i.beneficiario1).direccion as ben1_direccion,
                (i.beneficiario1).celular1 as ben1_celular1,
                (i.beneficiario1).celular2 as ben1_celular2,
                i.parentesco1 as parentesco_beneficiario1,

                -- Datos del Beneficiario Secundario
                (i.beneficiario2).dni as ben2_dni,
                (i.beneficiario2).apellido_paterno as ben2_apellido_paterno,
                (i.beneficiario2).apellido_materno as ben2_apellido_materno,
                (i.beneficiario2).nombres as ben2_nombres,
                (i.beneficiario2).direccion as ben2_direccion,
                (i.beneficiario2).celular1 as ben2_celular1,
                (i.beneficiario2).celular2 as ben2_celular2,
                i.parentesco2 as parentesco_beneficiario2

            FROM inversion i
            INNER JOIN cliente c ON c.id = i.cliente_id
            WHERE i.id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $inversion = $stmt->fetch(PDO::FETCH_ASSOC);
            return $inversion;
        } catch (PDOException $e) {
            die("Error al ver inversión: " . $e->getMessage());
        }
    }


    public function editar($inversion)
    {
        try {
            $sql = "UPDATE inversion SET
                    plan_inversion = :plan_inversion,
                    porcentaje = :porcentaje,
                    moneda = :moneda,
                    monto = :monto,
                    meses = :meses,
                    fecha_inicio = :fecha_inicio,
                    fecha_calculada = :fecha_calculada,
                    nombre_banco = :nombre_banco,
                    cuenta_bancaria = :cuenta_bancaria,
                    cuenta_interbancaria = :cuenta_interbancaria,
                    billetera_movil = :billetera_movil,
                    celular = :celular
                    -- contrato = :contrato
                WHERE id = :id"; // id de la inversión

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':plan_inversion'       => $inversion['plan_inversion'],
                ':porcentaje'           => $inversion['porcentaje'],
                ':moneda'               => $inversion['moneda'],
                ':monto'                => $inversion['monto'],
                ':meses'                => $inversion['meses'],
                ':fecha_inicio'         => $inversion['fecha_inicio'],
                ':fecha_calculada'      => $inversion['fecha_calculada'],
                ':nombre_banco'         => $inversion['nombre_banco'],
                ':cuenta_bancaria'      => $inversion['cuenta_bancaria'],
                ':cuenta_interbancaria' => $inversion['cuenta_interbancaria'],
                ':billetera_movil'      => $inversion['billetera_movil'],
                ':celular'              => $inversion['celular'],
                // ':contrato'          => $inversion['contrato'],
                ':id'                   => $inversion['id']
            ]);
        } catch (PDOException $e) {
            die("Error al actualizar inversión: " . $e->getMessage());
        }
    }

    public function select_cliente($q)
    {
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }

        // Consulta
        $sql = "
                SELECT id, (datos_personales).dni AS dni
                FROM cliente
                WHERE estado = true and (datos_personales).dni ILIKE :term
                LIMIT 20
            ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['term' => "%$q%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver JSON
        header('Content-Type: application/json');
        echo json_encode($results);
    }

    public function eliminar($id)
    {
        try {
            $sql = "UPDATE inversion SET estado = 'cancelado' WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die("Error al eliminar inversion: " . $e->getMessage());
        }
    }

    function contarContratos()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM inversion WHERE estado != 'cancelado'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            die("Error al contar contratos: " . $e->getMessage());
        }
    }

    public function obtenerIngresosMensuales()
    {
        try {
            $sql = "SELECT 
                    TO_CHAR(fecha_inicio, 'YYYY-MM') AS mes,
                    SUM(monto) AS total
                FROM inversion
                WHERE fecha_inicio >= (CURRENT_DATE - INTERVAL '11 months')
                  AND estado != 'cancelado'
                GROUP BY mes
                ORDER BY mes";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener ingresos: " . $e->getMessage());
        }
    }

    public function listarPorCliente($clienteId)
    {
        try {
            $sql = "SELECT 
                    i.id, 
                    (c.datos_personales).nombres,
                    (c.datos_personales).apellido_paterno,
                    (c.datos_personales).apellido_materno,
                    i.fecha_inicio, 
                    i.fecha_calculada,
                    i.plan_inversion, 
                    i.meses 
                FROM 
                    inversion i
                INNER JOIN 
                    cliente c ON i.cliente_id = c.id
                WHERE 
                    i.estado != 'cancelado' AND
                    i.cliente_id = :cliente_username
                ORDER BY
                    i.id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_username', $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al listar inversiones del cliente: " . $e->getMessage());
        }
    }

    public function verPorCliente($id, $clienteId)
    {
        try {
            $sql = "SELECT
                i.id,
                i.cliente_id,
                (c.datos_personales).dni as dni,
                (c.datos_personales).nombres as nombres,
                (c.datos_personales).apellido_paterno,
                (c.datos_personales).apellido_materno,
                (c.datos_personales).direccion as direccion,
                c.estado_civil,
                i.plan_inversion,
                i.porcentaje,
                i.moneda,
                i.monto,
                i.meses,
                i.fecha_inicio,
                i.fecha_calculada,
                i.nombre_banco,
                i.cuenta_bancaria,
                i.cuenta_interbancaria,
                i.billetera_movil,
                i.celular,

                -- Datos del Beneficiario Principal
                (i.beneficiario1).dni as ben1_dni,
                (i.beneficiario1).apellido_paterno as ben1_apellido_paterno,
                (i.beneficiario1).apellido_materno as ben1_apellido_materno,
                (i.beneficiario1).nombres as ben1_nombres,
                (i.beneficiario1).direccion as ben1_direccion,
                (i.beneficiario1).celular1 as ben1_celular1,
                (i.beneficiario1).celular2 as ben1_celular2,
                i.parentesco1 as parentesco_beneficiario1,

                -- Datos del Beneficiario Secundario
                (i.beneficiario2).dni as ben2_dni,
                (i.beneficiario2).apellido_paterno as ben2_apellido_paterno,
                (i.beneficiario2).apellido_materno as ben2_apellido_materno,
                (i.beneficiario2).nombres as ben2_nombres,
                (i.beneficiario2).direccion as ben2_direccion,
                (i.beneficiario2).celular1 as ben2_celular1,
                (i.beneficiario2).celular2 as ben2_celular2,
                i.parentesco2 as parentesco_beneficiario2

            FROM inversion i
            INNER JOIN cliente c ON c.id = i.cliente_id
            WHERE i.id = :id AND i.cliente_id = :cliente_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id, ':cliente_id' => $clienteId]);
            $inversion = $stmt->fetch(PDO::FETCH_ASSOC);
            return $inversion;
        } catch (PDOException $e) {
            die("Error al ver inversión: " . $e->getMessage());
        }
    }

    public function obtenerInversionesMensualesPorCliente($clienteId)
    {
        try {
            $sql = "
            SELECT TO_CHAR(i.fecha_inicio, 'YYYY-MM') AS mes,
                   COALESCE(SUM(i.monto), 0) AS total
            FROM inversion i
            WHERE i.cliente_id = :cliente_id
              AND i.fecha_inicio >= (CURRENT_DATE - INTERVAL '12 months')
              AND i.estado IS DISTINCT FROM 'cancelado'
            GROUP BY mes
            ORDER BY mes;
        ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;
        } catch (PDOException $e) {
            error_log("Error al obtener inversiones mensuales: " . $e->getMessage());
            return [];
        }
    }
    // Obtener cantidad de inveriones realizadas por cliente
    public function contarInversionesActivasPorCliente($clienteId)
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM inversion
                    WHERE cliente_id = :cliente_id AND estado IS DISTINCT FROM 'cancelado'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($resultado['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al contar inversiones: " . $e->getMessage());
            return 0;
        }
    }
    // Sumar el monto de inveriones realizadas por cliente
    public function sumarInversionesPorCliente($clienteId)
    {
        try {
            $sql = "SELECT COALESCE(SUM(monto), 0) AS total
                    FROM inversion
                    WHERE cliente_id = :cliente_id AND estado IS DISTINCT FROM 'cancelado'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($resultado['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al sumar inversiones: " . $e->getMessage());
            return 0;
        }
    }

    public function obtenerTop5ClientesPorInversion()
    {
        try {
            $sql = "
            SELECT 
                c.id,
                CONCAT(
                    (c.datos_personales).nombres, ' ',
                    (c.datos_personales).apellido_paterno
                ) AS cliente,
                ROUND(SUM(
                    CASE 
                        WHEN i.moneda = 'PEN' THEN i.monto
                        ELSE i.monto * COALESCE(tc.tasa_pen, 1)
                    END
                ), 2) AS monto_pen
            FROM inversion i
            JOIN cliente c ON i.cliente_id = c.id
            LEFT JOIN tipo_cambio tc 
                ON tc.moneda = i.moneda AND tc.fecha = i.fecha_inicio
            WHERE i.estado IS DISTINCT FROM 'cancelado'
            GROUP BY c.id, cliente
            ORDER BY monto_pen DESC
            LIMIT 5;
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener top 5 clientes: " . $e->getMessage());
            return [];
        }
    }
}
