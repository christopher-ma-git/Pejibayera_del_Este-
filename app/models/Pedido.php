<?php
require_once '../app/config/Database.php';

class Pedido {

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

        if (!empty($tipos)) {
            $stmt->bind_param($tipos, ...$parametros);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Crea un nuevo pedido.
     *
     * @param int $idUsuario
     * @param string $tipoPedido
     * @return int
     */
    
    public function create($data) { //esta linea da error 38
        $sql = "INSERT INTO Pedido
                (
                    fechaPedido,
                    tipoPedido,
                    idUsuario
                )
                VALUES
                (
                    CURDATE(),
                    ?,
                    ?
                )";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "si",
            $data['tipoPedido'],
            $data['idUsuario']
        );

        $stmt->close();
        return $this->db->insert_id;
    }

    /**
     * Obtiene un pedido por su id.
     *
     * @param int $idPedido
     * @return array|null
     */
    public function getById($idPedido) {
        $sql = "SELECT * FROM Pedido WHERE idPedido = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idPedido
        );

        $resultado = $stmt->get_result();
        $pedido = $resultado->fetch_assoc();
        $stmt->close();
        return $pedido;
    }

    /**
     * Obtiene todos los pedidos de un usuario.
     *
     * @param int $idUsuario
     * @return array
     */
    public function getByUsuario($idUsuario) {
        $sql = "SELECT
                    p.idPedido,
                    p.fechaPedido,
                    p.estadoPedido,
                    p.tipoPedido,
                    COALESCE(SUM(dp.subtotal), 0) AS totalPedido
                FROM Pedido p
                LEFT JOIN DetallePedido dp
                    ON p.idPedido = dp.idPedido
                WHERE p.idUsuario = ?
                GROUP BY
                    p.idPedido,
                    p.fechaPedido,
                    p.estadoPedido,
                    p.tipoPedido
                ORDER BY
                    p.fechaPedido DESC,
                    p.idPedido DESC";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idUsuario
        );

        $resultado = $stmt->get_result();
        $pedidos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $pedidos[] = $fila;
        }

        $stmt->close();
        return $pedidos;
    }

    /**
     * Obtiene todos los pedidos registrados.
     *
     * @return array
     */
    public function getAll() {
        $sql = "SELECT *
                FROM Pedido
                ORDER BY fechaPedido DESC,
                         idPedido DESC";
    
        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $pedidos = [];
    
        while ($fila = $resultado->fetch_assoc()) {
            $pedidos[] = $fila;
        }
    
        $stmt->close();
        return $pedidos;
    }

    /**
     * Actualiza el estado de un pedido.
     *
     * @param int $idPedido
     * @param string $estado
     * @return bool
     */
    public function updateEstado($idPedido, $estado) {
        $sql = "UPDATE Pedido SET estadoPedido = ? WHERE idPedido = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "si",
            $estado,
            $idPedido
        );

        $correcto = $stmt->affected_rows >= 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Elimina un pedido.
     *
     * @param int $idPedido
     * @return bool
     */
    public function delete($idPedido) {
        $sql = "DELETE FROM Pedido WHERE idPedido = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idPedido
        );

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

}