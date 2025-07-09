<?php
class ClienteModel
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
                        id, 
                        (datos_personales).dni AS dni,
                        (datos_personales).apellido_paterno AS apellido_paterno,
                        (datos_personales).apellido_materno AS apellido_materno,
                        (datos_personales).nombres AS nombres,
                        (datos_personales).direccion AS direccion,
                        (datos_personales).celular1 AS celular1,
                        (datos_personales).celular2 AS celular2,
                        estado_civil,
                        CASE WHEN estado = true THEN 'Activo' ELSE 'Inactivo' END AS estado
                    FROM cliente 
                    WHERE estado = true";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $clientes;
        } catch (PDOException $e) {
            die("Error al listar clientes: " . $e->getMessage());
        }
    }

    public function crear()
    {
        try {
            $sql = 'CALL crear_cliente_con_usuario(
                        :dni, :ap_pat, :ap_mat, :nombres, :direccion, :celular1, :celular2,
                        :estado_civil
                    )';

            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute([
                ':dni' => $_POST['dni'],
                ':ap_pat' => $_POST['apellido_paterno'],
                ':ap_mat' => $_POST['apellido_materno'],
                ':nombres' => $_POST['nombres'],
                ':direccion' => $_POST['direccion'],
                ':celular1' => $_POST['celular1'],
                ':celular2' => $_POST['celular2'],
                ':estado_civil' => $_POST['estado_civil']
            ]);
            
            if ($resultado) {
                require_once __DIR__ . '/../libreries/PDFCredenciales.php';
                
                $nombres = $_POST['nombres'];
                $apellidos = $_POST['apellido_paterno'] . ' ' . $_POST['apellido_materno'];
                $dni = $_POST['dni'];
                
                $pdfCredenciales = new PDFCredenciales($dni, $nombres, $apellidos, 'sc2022073503@virtual.upt.pe');
                $pdfCredenciales->crearYEnviarPDF();
            }
            
        } catch (PDOException $e) {
            error_log("Error al insertar cliente: " . $e->getMessage());
            throw $e;
        }
    }

    public function editar($cliente)
    {
        try {
            $sql = "UPDATE cliente
                SET 
                    datos_personales = ROW(:dni, :ap_pat, :ap_mat, :nombres, :direccion, :celular1, :celular2)::persona,
                    estado_civil = :estado_civil
                    WHERE id = :id";  // Asumiendo que el id del cliente es 'id_cliente'

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':dni' => $cliente['dni'],
                ':ap_pat' => $cliente['apellido_paterno'],
                ':ap_mat' => $cliente['apellido_materno'],
                ':nombres' => $cliente['nombres'],
                ':direccion' => $cliente['direccion'],
                ':celular1' => $cliente['celular1'],
                ':celular2' => $cliente['celular2'],
                ':estado_civil' => $cliente['estado_civil'],
                ':id' => $cliente['id']
            ]);
        } catch (PDOException $e) {
            die("Error al actualizar cliente: " . $e->getMessage());
        }
    }


    public function eliminar($id)
    {
        try {
            $sql = "UPDATE cliente SET estado = FALSE WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die("Error al eliminar cliente: " . $e->getMessage());
        }
    }
    public function ver($id)
    {
        try {
            $sql = "SELECT
                        id, 
                        (datos_personales).dni AS dni,
                        (datos_personales).apellido_paterno AS apellido_paterno,
                        (datos_personales).apellido_materno AS apellido_materno,
                        (datos_personales).nombres AS nombres,
                        (datos_personales).direccion AS direccion,
                        (datos_personales).celular1 AS celular1,
                        (datos_personales).celular2 AS celular2,
                        estado_civil
                    FROM cliente 
                    WHERE id = :id AND estado = true";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cliente;
        } catch (PDOException $e) {
            die("Error al ver cliente: " . $e->getMessage());
        }
    }

    function consultarDNI($dni, $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.factiliza.com/v1/dni/info/{$dni}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception("cURL Error: " . $err);
        } else {
            return json_decode($response, true); // Convertir JSON a array asociativo
        }
    }
    function contarClientes()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM cliente WHERE estado = TRUE";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            die("Error al contar clientes: " . $e->getMessage());
        }
    }

}
