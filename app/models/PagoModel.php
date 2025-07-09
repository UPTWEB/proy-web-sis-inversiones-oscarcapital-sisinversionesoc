<?php
class PagoModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function pagosPendientes()
    {
        try {
            $sql = "SELECT 
                        p.id AS id, 
                        (c.datos_personales).nombres AS nombres, 
                        (c.datos_personales).apellido_paterno AS apellido_paterno, 
                        (c.datos_personales).apellido_materno AS apellido_materno, 
                        p.monto AS monto, 
                        i.moneda AS moneda,
                        p.fecha AS fecha,
                        p.numero_pago,
                        CASE WHEN p.estado = true THEN 'Pendiente' ELSE 'Efectuado' END AS estado
                    FROM 
                        pago p
                    INNER JOIN 
                        inversion i ON i.id = p.inversion_id
                    INNER JOIN 
                        cliente c ON c.id = i.cliente_id
                    WHERE 
                        p.estado = true
                    ORDER BY
                        p.fecha";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pagos;
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }
    public function pagosEfectuados()
    {
        try {
            $sql = "SELECT 
                        p.id AS id, 
                        (c.datos_personales).nombres AS nombres, 
                        (c.datos_personales).apellido_paterno AS apellido_paterno, 
                        (c.datos_personales).apellido_materno AS apellido_materno, 
                        p.monto AS monto, 
                        i.moneda AS moneda,
                        p.fecha AS fecha,
                        p.numero_pago,
                        CASE WHEN p.estado = true THEN 'Pendiente' ELSE 'Efectuado' END AS estado
                    FROM 
                        pago p
                    INNER JOIN 
                        inversion i ON i.id = p.inversion_id
                    INNER JOIN 
                        cliente c ON c.id = i.cliente_id
                    WHERE 
                        p.estado = false
                    ORDER BY
                        p.fecha DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pagos;
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function ver($id)
    {
        try {
            $sql = "SELECT 
                        p.id AS id,
                        (c.datos_personales).dni AS dni, 
                        (c.datos_personales).nombres AS nombres, 
                        (c.datos_personales).apellido_paterno AS apellido_paterno, 
                        (c.datos_personales).apellido_materno AS apellido_materno, 
                        p.monto AS monto, 
                        i.moneda AS moneda,
                        p.numero_pago AS numero_pago,
                        i.plan_inversion AS plan_inversion,
                        p.fecha AS fecha,
                        i.nombre_banco,
                        i.cuenta_bancaria,
                        i.cuenta_interbancaria,
                        i.billetera_movil,
                        i.celular,
                        p.comprobante AS comprobante
                    FROM 
                        pago p
                    INNER JOIN 
                        inversion i ON i.id = p.inversion_id
                    INNER JOIN 
                        cliente c ON c.id = i.cliente_id
                    WHERE 
                        p.id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);
            return $pago;
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }
    public function registrar($id, $rutaArchivo)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE pago SET comprobante = :ruta, estado = 'FALSE' WHERE id = :id");
            $stmt->execute([
                ':ruta' => $rutaArchivo,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }
    function contarPagosEfectuados()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM pago WHERE estado = FALSE";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            die("Error al contar pagos efectuados: " . $e->getMessage());
        }
    }
    function contarPagosPendientes()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM pago WHERE estado = TRUE";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            die("Error al contar pagos pendientes: " . $e->getMessage());
        }
    }
    function obtenerPagosCalendario()
    {
        $sql = "SELECT 
                    p.id AS id, 
                    p.fecha AS fecha, 
                    p.monto AS monto, 
                    p.inversion_id AS inversion_id,
                    (c.datos_personales).nombres AS nombres, 
                    (c.datos_personales).apellido_paterno AS apellido_paterno, 
                    (c.datos_personales).apellido_materno AS apellido_materno,
                    p.numero_pago, 
                    p.comprobante 
                FROM pago p
                INNER JOIN 
                    inversion i ON i.id = p.inversion_id
                INNER JOIN 
                    cliente c ON c.id = i.cliente_id
                WHERE p.estado = true";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verPorInversion($id)
    {
        try {
            $sql = "SELECT 
                        p.id AS id,
                        (c.datos_personales).dni AS dni, 
                        (c.datos_personales).nombres AS nombres, 
                        (c.datos_personales).apellido_paterno AS apellido_paterno, 
                        (c.datos_personales).apellido_materno AS apellido_materno, 
                        p.monto AS monto, 
                        i.moneda AS moneda,
                        p.numero_pago AS numero_pago,
                        i.plan_inversion AS plan_inversion,
                        p.fecha AS fecha,
                        i.nombre_banco,
                        i.cuenta_bancaria,
                        i.cuenta_interbancaria,
                        i.billetera_movil,
                        i.celular,
                        p.comprobante AS comprobante,

                        -- Subconsultas para primer y último pago
                        (SELECT MIN(fecha) FROM pago WHERE inversion_id = p.inversion_id) AS primer_pago,
                        (SELECT MAX(fecha) FROM pago WHERE inversion_id = p.inversion_id) AS ultimo_pago

                    FROM 
                        pago p
                    INNER JOIN 
                        inversion i ON i.id = p.inversion_id
                    INNER JOIN 
                        cliente c ON c.id = i.cliente_id
                    WHERE 
                        p.inversion_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);
            return $pago;
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function obtenerPagosRecaudadosMensuales()
    {
        try {
            $sql = "SELECT 
                    TO_CHAR(fecha, 'YYYY-MM') AS mes,
                    SUM(monto) AS total
                FROM pago
                WHERE estado = false
                  AND fecha >= (CURRENT_DATE - INTERVAL '11 months')
                GROUP BY mes
                ORDER BY mes";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener pagos recaudados: " . $e->getMessage());
        }
    }

    public function obtenerCantidadPagosMesActual()
    {
        try {
            $sql = "SELECT
                    estado,
                    COUNT(*) AS cantidad
                FROM
                    public.pago
                WHERE
                    DATE_TRUNC('month', fecha) = DATE_TRUNC('month', CURRENT_DATE)
                GROUP BY
                    estado";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener cantidad de pagos: " . $e->getMessage());
        }
    }

    public function proximosPagosPendientes()
    {
        try {
            $sql = "SELECT DISTINCT ON (p.inversion_id)
                        p.id AS id, 
                        (c.datos_personales).nombres AS nombres, 
                        (c.datos_personales).apellido_paterno AS apellido_paterno, 
                        (c.datos_personales).apellido_materno AS apellido_materno, 
                        p.monto AS monto, 
                        i.moneda AS moneda,
                        p.fecha AS fecha,
                        p.numero_pago,
                        p.inversion_id
                    FROM 
                        pago p
                    INNER JOIN 
                        inversion i ON i.id = p.inversion_id
                    INNER JOIN 
                        cliente c ON c.id = i.cliente_id
                    WHERE 
                        p.estado = true
                    ORDER BY
                        p.inversion_id, p.numero_pago ASC, p.fecha ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pagos;
        } catch (PDOException $e) {
            die("Error al listar próximos pagos: " . $e->getMessage());
        }
    }

    public function validarOrdenPago($pagoId)
    {
        try {
            $sql = "SELECT 
                        p1.numero_pago as numero_actual,
                        p1.inversion_id,
                        COUNT(p2.id) as pagos_anteriores_pendientes
                    FROM pago p1
                    LEFT JOIN pago p2 ON p2.inversion_id = p1.inversion_id 
                                      AND p2.numero_pago < p1.numero_pago 
                                      AND p2.estado = true
                    WHERE p1.id = :pago_id
                    GROUP BY p1.id, p1.numero_pago, p1.inversion_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':pago_id' => $pagoId]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado['pagos_anteriores_pendientes'] == 0;
        } catch (PDOException $e) {
            die("Error al validar orden de pago: " . $e->getMessage());
        }
    }

    public function proximosPagosPendientesPorInversion($inversion_id)
    {
        try {
            $sql = "SELECT 
                    p.id AS id, 
                    (c.datos_personales).nombres AS nombres, 
                    (c.datos_personales).apellido_paterno AS apellido_paterno, 
                    (c.datos_personales).apellido_materno AS apellido_materno, 
                    p.monto AS monto, 
                    i.moneda AS moneda,
                    p.fecha AS fecha,
                    p.numero_pago,
                    p.inversion_id,
                    p.estado
                FROM 
                    pago p
                INNER JOIN 
                    inversion i ON i.id = p.inversion_id
                INNER JOIN 
                    cliente c ON c.id = i.cliente_id
                WHERE 
                    p.estado = true AND
                    i.id = :inversion_id
                ORDER BY
                    p.fecha ASC, p.numero_pago ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':inversion_id', $inversion_id, PDO::PARAM_INT);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pagos;
        } catch (PDOException $e) {
            die("Error al listar pagos del cliente: " . $e->getMessage());
        }
    }
    public function obtenerPagosPendientesPorCliente($cliente_id)
    {
        try {
            $sql = "SELECT 
                    p.id AS id, 
                    (c.datos_personales).nombres AS nombres, 
                    (c.datos_personales).apellido_paterno AS apellido_paterno, 
                    (c.datos_personales).apellido_materno AS apellido_materno, 
                    p.monto AS monto, 
                    i.moneda AS moneda,
                    p.fecha AS fecha,
                    p.numero_pago,
                    p.inversion_id,
                    p.estado
                FROM 
                    pago p
                INNER JOIN 
                    inversion i ON i.id = p.inversion_id
                INNER JOIN 
                    cliente c ON c.id = i.cliente_id
                WHERE 
                    p.estado = true AND
                    c.id = :cliente_id
                ORDER BY
                    p.fecha ASC, p.numero_pago ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pagos;
        } catch (PDOException $e) {
            die("Error al listar pagos del cliente: " . $e->getMessage());
        }
    }

    public function obtenerPagosEfectuadosporCliente($cliente_id)
    {
        try {
            $sql = "SELECT 
                    p.id AS id, 
                    (c.datos_personales).nombres AS nombres, 
                    (c.datos_personales).apellido_paterno AS apellido_paterno, 
                    (c.datos_personales).apellido_materno AS apellido_materno, 
                    p.monto AS monto, 
                    i.moneda AS moneda,
                    p.fecha AS fecha,
                    p.numero_pago,
                    p.inversion_id,
                    p.estado
                FROM 
                    pago p
                INNER JOIN 
                    inversion i ON i.id = p.inversion_id
                INNER JOIN 
                    cliente c ON c.id = i.cliente_id
                WHERE 
                    p.estado = false AND
                    c.id = :cliente_id
                ORDER BY
                    p.fecha ASC, p.numero_pago ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pagos;
        } catch (PDOException $e) {
            die("Error al listar pagos del cliente: " . $e->getMessage());
        }
    }
    //Metodo para que los clientes solo puedan ver sus inversiones
    public function verPorCliente($id, $cliente_id)
    {
        try {
            $sql = "SELECT 
                        p.id AS id,
                        (c.datos_personales).dni AS dni, 
                        (c.datos_personales).nombres AS nombres, 
                        (c.datos_personales).apellido_paterno AS apellido_paterno, 
                        (c.datos_personales).apellido_materno AS apellido_materno, 
                        p.monto AS monto, 
                        i.moneda AS moneda,
                        p.numero_pago AS numero_pago,
                        i.plan_inversion AS plan_inversion,
                        p.fecha AS fecha,
                        i.nombre_banco,
                        i.cuenta_bancaria,
                        i.cuenta_interbancaria,
                        i.billetera_movil,
                        i.celular,
                        p.comprobante AS comprobante
                    FROM 
                        pago p
                    INNER JOIN 
                        inversion i ON i.id = p.inversion_id
                    INNER JOIN 
                        cliente c ON c.id = i.cliente_id
                    WHERE 
                        p.id = :id AND c.id = :cliente_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id, ':cliente_id' => $cliente_id]);
            $pago = $stmt->fetch(PDO::FETCH_ASSOC);
            return $pago;
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }
    // Obtener Ganancias de Cliente (Vista de Inicio del Cliente)


    public function obtenerPagosMensualesporCliente($clienteId)
    {
        try {
            $sql = "
            SELECT TO_CHAR(p.fecha, 'YYYY-MM') AS mes, 
                   COALESCE(SUM(p.monto), 0) AS total
            FROM pago p
            INNER JOIN inversion i ON p.inversion_id = i.id
            WHERE i.cliente_id = :cliente_id
              AND p.estado = false
              AND p.fecha >= (CURRENT_DATE - INTERVAL '12 months')
            GROUP BY mes
            ORDER BY mes;
        ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;
        } catch (PDOException $e) {
            error_log("Error al obtener pagos mensuales: " . $e->getMessage());
            return [];
        }
    }


    // Metodo para obtener ganancias por cliente (restar pagos efectuados menos inversiones)
    public function obtenerGananciaPorCliente($clienteId)
    {
        try {
            $sql = "SELECT obtener_ganancia_por_cliente(:cliente_id) AS ganancia;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['ganancia'] ?? 0;
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al obtener la ganancia: " . $e->getMessage());
            return 0;
        }
    }

    // Sumar el monto de pagos realizados a cliente
    public function sumarPagosCanceladosPorCliente($clienteId)
    {
        try {
            $sql = "SELECT COALESCE(SUM(p.monto), 0) AS total
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    WHERE i.cliente_id = :cliente_id AND p.estado = false";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($resultado['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al sumar pagos cancelados: " . $e->getMessage());
            return 0;
        }
    }
}
