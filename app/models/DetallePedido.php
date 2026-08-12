<?php
require_once '../app/config/Database.php';

class DetallePedido {

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
     * Crea un detalle de pedido.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO DetallePedido
                (
                    precioUnitario,
                    cantidad,
                    subtotal,
                    idPedido,
                    idProducto
                )
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ddiii",
            $data['precioUnitario'],
            $data['cantidad'],
            $data['subtotal'],
            $data['idPedido'],
            $data['idProducto']
        );

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Obtiene un detalle por su id.
     *
     * @param int $idDetallePedido
     * @return array|null
     */
    public function getById($idDetallePedido) {
        $sql = "SELECT * FROM DetallePedido WHERE idDetallePedido = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idDetallePedido
        );

        $resultado = $stmt->get_result();
        $detalle = $resultado->fetch_assoc();
        $stmt->close();
        return $detalle;
    }

    /**
     * Obtiene todos los pedidos registrados.
     *
     * @return array
     */
    public function getAll() {
        $sql = "SELECT * FROM DetallePedido ORDER BY idDetallePedido DESC";
    
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
     * Obtiene todos los productos de un pedido.
     *
     * @param int $idPedido
     * @return array
     */
    public function getByPedido($idPedido) {
        $sql = "SELECT
                    dp.*,
                    p.nombreProducto
                FROM DetallePedido dp
                INNER JOIN Producto p
                    ON dp.idProducto = p.idProducto
                WHERE dp.idPedido = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idPedido
        );

        $resultado = $stmt->get_result();
        $detalle = [];

        while ($fila = $resultado->fetch_assoc()) {
            $detalle[] = $fila;
        }

        $stmt->close();
        return $detalle;
    }

    /**
     * Elimina un detalle de pedido.
     *
     * @param int $idDetallePedido
     * @return bool
     */
    public function delete($idDetallePedido) {
        $sql = "DELETE FROM DetallePedido WHERE idDetallePedido = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idDetallePedido
        );

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

}