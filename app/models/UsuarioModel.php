<?php
class UsuarioModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listar()
    {
        try {
            $sql = "SELECT * FROM usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $usuarios;
        } catch (PDOException $e) {
            die("Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function iniciarSesion($username)
    {
        try {
            // $sql = "SELECT * FROM usuario WHERE (username = :username or  email = :username) AND password = crypt(:password, password)" ;
            $sql = "SELECT * FROM usuario WHERE username = :username or  email = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            // $stmt->bindParam(':password', $password);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function buscarPorCorreo($email)
    {
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarSesion($id_usuario, $ip, $user_agent)
    {
        $sql = "INSERT INTO sesiones (id_usuario, ip, user_agent) 
            VALUES (:id_usuario, :ip, :user_agent) RETURNING id_sesion";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id_sesion'] ?? null;
    }

    public function cerrarSesion($id_sesion, $tiempo)
    {
        $sql = "UPDATE sesiones SET duracion=:tiempo,fin = CURRENT_TIMESTAMP, tipo='cerrada' WHERE id_sesion = :id_sesion";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->bindParam(':tiempo', $tiempo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function cerrarSesionDesconectado($id_sesion, $tiempo)
    {
        $sql = "UPDATE sesiones SET duracion=:tiempo,fin = CURRENT_TIMESTAMP, tipo='desconectado' WHERE id_sesion = :id_sesion";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->bindParam(':tiempo', $tiempo, PDO::PARAM_STR);
        return $stmt->execute();
    }


    public function intentoLogin($id_usuario, $ip, $user_agent, $exito)
    {
        $sql = "INSERT INTO intentos_login (id_usuario, ip,user_agent,exito) 
            VALUES (:id_usuario, :ip, :user_agent, :exito)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
        $stmt->bindParam(':exito', $exito, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function intentosFallidos($id_usuario)
    {
        $sql = "SELECT COUNT(*) as intentos FROM intentos_login WHERE id_usuario = :id_usuario AND exito = false AND fecha_hora > (NOW() - INTERVAL '5 minutes')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['intentos'] ?? 0;
    }


    public function actualizarUltimoAcceso($id_sesion, $tiempo)
    {
        $sql = "UPDATE sesiones 
                SET fin = NOW(), duracion = :tiempo, tipo = 'activo'
                WHERE id_sesion = :id_sesion AND tipo != 'cerrado'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->bindParam(':tiempo', $tiempo, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerTiempoSesion($id_sesion)
    {
        $sql = "SELECT duracion FROM sesiones WHERE id_sesion = :id_sesion";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['duracion'] ?? '00:00:00';
    }

    function obtenerIdCliente($id_sesion){
        $sql = "SELECT c.id as id_cliente
                FROM cliente c
                INNER JOIN usuario u
                ON c.usuario_id = u.id 
                INNER JOIN sesiones s
                ON u.id = s.id_usuario
                WHERE s.id_sesion = :id_sesion";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id_cliente'];}

    public function modificarUsuario($id_usuario, $nuevoNombre, $nuevoCorreo, $nuevaContraseña = null, $nuevaFoto = null)
    {
        try {
            $campos = [];
            $params = [':id_usuario' => $id_usuario];
            
            $campos[] = "username = :nuevoNombre";
            $params[':nuevoNombre'] = $nuevoNombre;
            
            $campos[] = "email = :nuevoCorreo";
            $params[':nuevoCorreo'] = $nuevoCorreo;
            
            if ($nuevaContraseña !== null && $nuevaContraseña !== '') {
                $contraseñaEncriptada = password_hash($nuevaContraseña, PASSWORD_BCRYPT);
                $campos[] = "password = :nuevaContrasena";
                $params[':nuevaContrasena'] = $contraseñaEncriptada;
            }
            
            if ($nuevaFoto !== null && $nuevaFoto !== '') {
                $campos[] = "foto = :nuevaFoto";
                $params[':nuevaFoto'] = $nuevaFoto;
            }
            
            $sql = "UPDATE usuario SET " . implode(", ", $campos) . " WHERE id = :id_usuario";
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $resultado = $stmt->execute();
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'affected_rows' => $stmt->rowCount()];
            } elseif ($resultado && $stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'No se encontró el usuario o los datos son idénticos'];
            } else {
                return ['success' => false, 'message' => 'Error al ejecutar la consulta'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }


    public function buscarIdUsuario($id_usuario)
    {
        $sql = "SELECT * FROM usuario WHERE id = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
}
