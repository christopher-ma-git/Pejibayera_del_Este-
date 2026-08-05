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
     * Obtiene la empresa junto con la información del usuario.
     *
     * @param int $idUsuario
     * @return array|null
     */
    public function getByUsuario($idUsuario) {

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
                WHERE e.idUsuario = ?";

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

        $sql = "SELECT * FROM Empresa
                WHERE cedulaJuridica = ?";

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

        $sql = "INSERT INTO Empresa
                (cedulaJuridica, idUsuario)
                VALUES (?, ?)";

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
     * Actualiza la empresa.
     *
     * @param int $idUsuario
     * @param string $cedulaJuridica
     * @return bool
     */
    public function update($idUsuario, $cedulaJuridica) {

        $sql = "UPDATE Empresa
                SET cedulaJuridica = ?
                WHERE idUsuario = ?";

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

        $sql = "DELETE FROM Empresa
                WHERE idUsuario = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $idUsuario
        );

        $correcto = $stmt->affected_rows > 0;

        $stmt->close();

        return $correcto;
    }

    /**
     * Obtiene todos los productos empresariales.
     *
     * @return array
     */
    public function getProductos() {

        $sql = "SELECT *
                FROM Producto
                WHERE ventaEmpresarial = TRUE
                ORDER BY nombreProducto ASC";

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
     * Obtiene los pedidos realizados por la empresa.
     *
     * @param int $idUsuario
     * @return array
     */
    public function getPedidos($idUsuario) {

        $sql = "SELECT
                    idPedido,

                    fechaPedido,

                    fechaEntrega,

                    estadoPedido,

                    pedidoTotal

                FROM Pedido

                WHERE idUsuario = ?
                
                ORDER BY fechaPedido DESC";

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
 * Registra un nuevo pedido.
 */
public function crearPedido(
    $idUsuario,
    $idProducto,
    $cantidad,
    $fechaEntrega,
    $observaciones
) {

    $sql = "SELECT precio
            FROM Producto
            WHERE idProducto = ?";

    $stmt = $this->ejecutarConsulta(
        $sql,
        "i",
        $idProducto
    );

    $resultado = $stmt->get_result();

    $producto = $resultado->fetch_assoc();

    $stmt->close();

    $pedidoTotal = $producto['precio'] * $cantidad;

    $sql = "INSERT INTO Pedido(

                fechaEntrega,
                estadoPedido,
                tipoPedido,
                pedidoTotal,
                observaciones,
                idUsuario

            )

            VALUES(

                ?,
                'Pendiente',
                'Empresa',
                ?,
                ?,
                ?

            )";

    $stmt = $this->ejecutarConsulta(

        $sql,

        "sdsi",

        $fechaEntrega,

        $pedidoTotal,

        $observaciones,

        $idUsuario

    );

    $idPedido = $this->db->insert_id;

    $stmt->close();

    $sql = "INSERT INTO DetallePedido(

                precioUnitario,
                cantidad,
                subtotal,
                idPedido,
                idProducto

            )

            VALUES(

                ?,
                ?,
                ?,
                ?,
                ? )";

    $this->ejecutarConsulta(

        $sql,

        "didii",

        $producto['precio'],    //decimal

        $cantidad,  //entero

        $pedidoTotal,//decimal

        $idPedido,  //entero

        $idProducto  //entero

    );

    return true;



}
}