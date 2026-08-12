<?php
require_once '../app/config/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Ejecuta una consulta preparada.
     *
     * @param string $sql
     * @param string $tipos
     * @param mixed ...$parametros
     * @return mysqli_stmt
     */
    private function ejecutarConsulta($sql, $tipos = "", ...$parametros) {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->db->error);
        }

        if (!empty($tipos)) {
            $stmt->bind_param($tipos, ...$parametros);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtiene todos los usuarios.
     *
     * @return array
     */
    public function getAll() {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $usuarios = [];

        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }

        $stmt->close();
        return $usuarios;
    }

    /**
     * Obtiene un usuario por ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->ejecutarConsulta($sql, "i", $id);
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        return $usuario;
    }

    /**
     * Obtiene un usuario por email.
     *
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->ejecutarConsulta($sql, "s", $email);
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        return $usuario;
    }

    /**
     * Registra un nuevo usuario.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $passwordHash = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users (nombre, apellido, email, password,
                    rol, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "sssssss",
            $data['nombre'],
            $data['apellido'],
            $data['email'],
            $passwordHash,
            $data['rol'],
            $data['telefono'],
            $data['direccion']
        );

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Actualiza un usuario.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        if (!empty($data['password'])) {
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            $sql = "UPDATE users
                    SET
                        nombre = ?,
                        apellido = ?,
                        email = ?,
                        password = ?,
                        telefono = ?,
                        direccion = ?,
                        rol = ?
                    WHERE id = ?";

            $stmt = $this->ejecutarConsulta(
                $sql,
                "sssssssi",
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $passwordHash,
                $data['telefono'],
                $data['direccion'],
                $data['rol'],
                $id
            );
        } else {
            $sql = "UPDATE users
                    SET
                        nombre = ?,
                        apellido = ?,
                        email = ?,
                        telefono = ?,
                        direccion = ?,
                        rol = ?
                    WHERE id = ?";

            $stmt = $this->ejecutarConsulta(
                $sql,
                "ssssssi",
                $data['nombre'],
                $data['apellido'],
                $data['email'],
                $data['telefono'],
                $data['direccion'],
                $data['rol'],
                $id
            );
        }

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Elimina un usuario.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->ejecutarConsulta($sql, "i", $id);
        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Actualiza los datos de un Cliente.
     *
     * @param int $idUsuario
     * @param array $data
     * @return bool
     */
    public function updateCliente($idUsuario, $data) {

        $usuario = $this->getById($idUsuario);

        $nombre = !empty($data['nombre'])
            ? $data['nombre']
            : $usuario['nombre'];

        $email = !empty($data['email'])
            ? $data['email']
            : $usuario['email'];

        $apellido = !empty($data['apellido'])
            ? $data['apellido']
            : $usuario['apellido'];

        $telefono = !empty($data['telefono'])
            ? $data['telefono']
            : $usuario['telefono'];

        $direccion = !empty($data['direccion'])
            ? $data['direccion']
            : $usuario['direccion'];

        if (!empty($data['password'])) {
            $password = password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );

        } else {
            $password = $usuario['password'];
        }

        $sql = "UPDATE users
                SET nombre = ?,
                    email = ?,
                    password = ?,
                    apellido = ?,
                    telefono = ?,
                    direccion = ?
                WHERE id = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ssssssi",
            $nombre,
            $email,
            $password,
            $apellido,
            $telefono,
            $direccion,
            $idUsuario
        );

        $correcto = $stmt->affected_rows >= 0;
        $stmt->close();
        return $correcto;
    }
    
    /**
     * Actualiza los datos de una Empresa.
     *
     * @param int $idUsuario
     * @param array $data
     * @return bool
     */
    public function updateEmpresa($idUsuario, $data) {

        $usuario = $this->getById($idUsuario);
        //$empresa = $this->empresaModel->getById($idUsuario); //esta linea provoca el error

        $nombre = !empty($data['nombre'])
            ? $data['nombre']
            : $usuario['nombre'];

        $email = !empty($data['email'])
            ? $data['email']
            : $usuario['email'];

        $telefono = !empty($data['telefono'])
            ? $data['telefono']
            : $usuario['telefono'];

        $direccion = !empty($data['direccion'])
            ? $data['direccion']
            : $usuario['direccion'];

        $cedulaJuridica = !empty($data['cedulaJuridica'])
            ? $data['cedulaJuridica']
            : $empresa['cedulaJuridica'];

        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);

        } else {
            $password = $usuario['password'];
        }

        $sql = "UPDATE users
                SET nombre = ?,
                    email = ?,
                    password = ?,
                    telefono = ?,
                    direccion = ?
                WHERE id = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "sssssi",
            $nombre,
            $email,
            $password,
            $telefono,
            $direccion,
            $idUsuario
        );

        $correcto = $stmt->affected_rows >= 0;
        $stmt->close();

        if (!$correcto) {
            return false;
        }

        $sql = "UPDATE Empresa SET cedulaJuridica = ? WHERE idUsuario = ?";
        $stmt = $this->ejecutarConsulta(
            $sql,
            "si",
            $cedulaJuridica,
            $idUsuario
        );

        $correcto = $stmt->affected_rows >= 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Calcula el porcentaje de crecimiento.
     *
     * @param int $valorInicial
     * @param int $valorFinal
     * @return float
     */
    private function calcularCrecimiento($valorInicial, $valorFinal) {
        if ($valorInicial <= 0) {
            return ($valorFinal > 0) ? 100.00 : 0.00;
        }

        return round((($valorFinal - $valorInicial) / $valorInicial) * 100, 2);
    }

    /**
     * Obtiene el total de clientes y el crecimiento mensual.
     *
     * @return array
     */
    public function getContarClientes() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE rol = 'Cliente'";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $totalActual = $resultado->fetch_assoc()['total'];
        $stmt->close();

        $sql = "SELECT COUNT(*) AS nuevos
                FROM users
                WHERE rol = 'Cliente'
                AND MONTH(fechaRegistro) = MONTH(CURDATE())
                AND YEAR(fechaRegistro) = YEAR(CURDATE())";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $nuevos = $resultado->fetch_assoc()['nuevos'];
        $stmt->close();

        $valorInicial = $totalActual - $nuevos;
        return ['total' => $totalActual, 'crecimiento' => $this->calcularCrecimiento($valorInicial, $totalActual)];
    }

    /**
     * Obtiene el total de empresas y el crecimiento mensual.
     *
     * @return array
     */
    public function getContarEmpresas() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE rol = 'Empresa'";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $totalActual = $resultado->fetch_assoc()['total'];
        $stmt->close();

        $sql = "SELECT COUNT(*) AS nuevos
                FROM users
                WHERE rol = 'Empresa'
                AND MONTH(fechaRegistro) = MONTH(CURDATE())
                AND YEAR(fechaRegistro) = YEAR(CURDATE())";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $nuevos = $resultado->fetch_assoc()['nuevos'];
        $stmt->close();

        $valorInicial = $totalActual - $nuevos;
        return ['total' => $totalActual, 'crecimiento' => $this->calcularCrecimiento($valorInicial, $totalActual)];
    }
}