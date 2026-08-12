<?php
require_once '../app/config/Database.php';

class Empresa {
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
     * Obtiene todas las empresas.
     *
     * @return array
     */
    public function getAll() {
        $sql = "SELECT
                    e.cedulaJuridica,
                    e.idUsuario,
                    u.nombre,
                    u.apellido,
                    u.email,
                    u.telefono,
                    u.direccion,
                    u.estadoUsuario
                FROM Empresa e
                INNER JOIN users u
                    ON e.idUsuario = u.id
                ORDER BY u.nombre ASC";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $empresas = [];

        while ($fila = $resultado->fetch_assoc()) {
            $empresas[] = $fila;
        }

        $stmt->close();
        return $empresas;
    }

    /**
     * Obtiene una empresa por usuario.
     *
     * @param int $idUsuario
     * @return array|null
     */
    public function getByUsuario($idUsuario) {
        $sql = "SELECT * FROM Empresa WHERE idUsuario = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idUsuario
        );

        $resultado = $stmt->get_result();
        $empresa = $resultado->fetch_assoc();
        $stmt->close();
        return $empresa;
    }

    /**
     * Obtiene una empresa por cédula jurídica.
     *
     * @param string $cedulaJuridica
     * @return array|null
     */
    public function getByCedula($cedulaJuridica) {
        $sql = "SELECT * FROM Empresa WHERE cedulaJuridica = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "s",
            $cedulaJuridica
        );

        $resultado = $stmt->get_result();
        $empresa = $resultado->fetch_assoc();
        $stmt->close();
        return $empresa;
    }

    /**
     * Registra una empresa.
     *
     * @param string $cedulaJuridica
     * @param int $idUsuario
     * @return bool
     */
    public function create($cedulaJuridica, $idUsuario) {
        $sql = "INSERT INTO Empresa (cedulaJuridica, idUsuario) VALUES (?, ?)";
        $stmt = $this->ejecutarConsulta($sql, "si", $cedulaJuridica, $idUsuario);
        
        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Actualiza la cédula jurídica.
     *
     * @param int $idUsuario
     * @param string $cedulaJuridica
     * @return bool
     */
    public function update($idUsuario, $cedulaJuridica) {
        $sql = "UPDATE Empresa SET cedulaJuridica = ? WHERE idUsuario = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "si",
            $cedulaJuridica,
            $idUsuario
        );

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Elimina una empresa.
     *
     * @param int $idUsuario
     * @return bool
     */
    public function delete($idUsuario) {
        $sql = "DELETE FROM Empresa WHERE idUsuario = ?";

        $stmt = $this->ejecutarConsulta($sql, "i", $idUsuario);
        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

}