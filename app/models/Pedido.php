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
    
    public function create($data) {
        $sql = "INSERT INTO Pedido
                    (fechaPedido, fechaEntrega, observaciones, tipoPedido, idUsuario)
                VALUES(CURDATE(), ?, ?, ?, ?)";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "sssi",
            $data['fechaEntrega'],
            $data['observaciones'],
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
                    p.fechaEntrega,
                    p.observaciones,
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
                    p.fechaEntrega,
                    p.observaciones,
                    p.estadoPedido,
                    p.tipoPedido
                ORDER BY
                    p.fechaPedido DESC,
                    p.fechaEntrega DESC,
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

    /**
     * Calcula el porcentaje de crecimiento.
     *
     * @param int|float $valorInicial
     * @param int|float $valorFinal
     * @return float
     */
    private function calcularCrecimiento($valorInicial, $valorFinal) {
        if ($valorInicial <= 0) {
            return ($valorFinal > 0) ? 100.00 : 0.00;
        }

        return round((($valorFinal - $valorInicial) / $valorInicial) * 100, 2);
    }

    /**
     * Obtiene la cantidad de pedidos activos y su crecimiento mensual.
     *
     * @return array
     */
    public function getContarPedidos() {
        $sql = "SELECT COUNT(*) AS total FROM Pedido WHERE estadoPedido IN ('Pendiente', 'En preparación')";
        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $totalActual = $resultado->fetch_assoc()['total'];
        $stmt->close();

        $sql = "SELECT COUNT(*) AS nuevos
                FROM Pedido
                WHERE estadoPedido IN ('Pendiente', 'En preparación')
                AND MONTH(fechaPedido) = MONTH(CURDATE())
                AND YEAR(fechaPedido) = YEAR(CURDATE())";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $nuevos = $resultado->fetch_assoc()['nuevos'];
        $stmt->close();

        $valorInicial = $totalActual - $nuevos;
        return ['total' => $totalActual, 'crecimiento' => $this->calcularCrecimiento($valorInicial, $totalActual)];
    }

    /**
     * Obtiene el total vendido y el crecimiento mensual.
     *
     * @return array
     */
    public function getContarVentas() {
        $sql = "SELECT COALESCE(SUM(dp.subtotal),0) AS total
                FROM Pedido p
                INNER JOIN DetallePedido dp
                    ON p.idPedido = dp.idPedido";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $totalActual = $resultado->fetch_assoc()['total'];
        $stmt->close();

        $sql = "SELECT COALESCE(SUM(dp.subtotal),0) AS nuevos
                FROM Pedido p
                INNER JOIN DetallePedido dp
                    ON p.idPedido = dp.idPedido
                WHERE MONTH(p.fechaPedido) = MONTH(CURDATE())
                AND YEAR(p.fechaPedido) = YEAR(CURDATE())";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $ventasMes = $resultado->fetch_assoc()['nuevos'];
        $stmt->close();

        $valorInicial = $totalActual - $ventasMes;
        return ['total' => $totalActual, 'crecimiento' => $this->calcularCrecimiento($valorInicial, $totalActual)];
    }

    /**
     * Obtiene los cuatro productos más vendidos.
     *
     * @return array
     */
    public function getContarPedidosProducto() {
        $sql = "SELECT
                    p.nombreProducto,
                    SUM(dp.cantidad) AS totalVentas
                FROM DetallePedido dp
                INNER JOIN Producto p
                    ON dp.idProducto = p.idProducto
                GROUP BY
                    p.idProducto,
                    p.nombreProducto
                ORDER BY totalVentas DESC
                LIMIT 4";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();

        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        $stmt->close();
        return $productos;
    }

    /**
     * Obtiene todos los pedidos realizados por clientes.
     *
     * @return array
     */
    public function getPedidosCliente() {
        $sql = "SELECT
                    p.idPedido,
                    u.nombre,
                    p.fechaPedido,
                    p.estadoPedido,
                    pr.nombreProducto,
                    dp.cantidad,
                    dp.precioUnitario,
                    dp.subtotal
                FROM Pedido p
                INNER JOIN users u
                    ON p.idUsuario = u.id
                INNER JOIN DetallePedido dp
                    ON p.idPedido = dp.idPedido
                INNER JOIN Producto pr
                    ON dp.idProducto = pr.idProducto
                WHERE p.tipoPedido = 'Individual'
                ORDER BY
                    p.fechaPedido DESC,
                    p.idPedido DESC";

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
     * Obtiene todos los pedidos realizados por empresas.
     *
     * @return array
     */
    public function getPedidosEmpresa() {
        $sql = "SELECT
                    p.idPedido,
                    u.nombre,
                    p.fechaPedido,
                    p.fechaEntrega,
                    p.estadoPedido,
                    pr.nombreProducto,
                    dp.cantidad,
                    dp.precioUnitario,
                    dp.subtotal,
                    p.observaciones
                FROM Pedido p
                INNER JOIN users u
                    ON p.idUsuario = u.id
                INNER JOIN DetallePedido dp
                    ON p.idPedido = dp.idPedido
                INNER JOIN Producto pr
                    ON dp.idProducto = pr.idProducto
                WHERE p.tipoPedido = 'Empresa'
                ORDER BY
                    p.fechaEntrega ASC,
                    p.fechaPedido DESC,
                    p.idPedido DESC";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $pedidos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $pedidos[] = $fila;
        }

        $stmt->close();
        return $pedidos;
    }

}