<?php
require_once '../app/config/Database.php';

class Carrito {

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
     * Marca como abandonados los carritos con más de 30 días.
     */
    public function verificarCarritosAbandonados() {

        $sql = "UPDATE Carrito
                SET estadoCarrito = 'Abandonado'
                WHERE estadoCarrito = 'Activo'
                AND fechaCreacion < DATE_SUB(NOW(), INTERVAL 30 DAY)";

        $stmt = $this->ejecutarConsulta($sql);
        $stmt->close();

    }

    /**
     * Obtiene el carrito activo del usuario.
     *
     * @param int $idUsuario
     * @return array|null
     */
    public function getCarritoActivo($idUsuario) {
        $this->verificarCarritosAbandonados();

        $sql = "SELECT *
                FROM Carrito
                WHERE idUsuario = ?
                AND estadoCarrito = 'Activo'
                LIMIT 1";

        $stmt = $this->ejecutarConsulta($sql, "i", $idUsuario);
        $resultado = $stmt->get_result();
        $carrito = $resultado->fetch_assoc();
        $stmt->close();

        return $carrito;
    }

    /**
     * Crea un nuevo carrito.
     *
     * @param int $idUsuario
     * @return int
     */
    public function crearCarrito($idUsuario) {
        $sql = "INSERT INTO Carrito (idUsuario)
                VALUES (?)";

        $stmt = $this->ejecutarConsulta($sql, "i", $idUsuario);
        $stmt->close();

        return $this->db->insert_id;
    }

    /**
     * Agrega un producto al carrito.
     *
     * @param int $idUsuario
     * @param int $idProducto
     * @param int $cantidad
     */
    public function agregarProducto($idUsuario, $idProducto, $cantidad = 1) {
        $carrito = $this->getCarritoActivo($idUsuario);

        if (!$carrito) {
            $idCarrito = $this->crearCarrito($idUsuario);
        } else {
            $idCarrito = $carrito['idCarrito'];
        }

        $sql = "SELECT *
                FROM DetalleCarrito
                WHERE idCarrito = ?
                AND idProducto = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ii",
            $idCarrito,
            $idProducto
        );

        $resultado = $stmt->get_result();
        $detalle = $resultado->fetch_assoc();
        $stmt->close();

        if ($detalle) {
            $cantidadNueva = $detalle['cantidadCarrito'] + $cantidad;

            $this->actualizarCantidad(
                $detalle['idDetalleCarrito'],
                $cantidadNueva
            );

        } else {
            $sql = "INSERT INTO DetalleCarrito
                    (cantidadCarrito, idCarrito, idProducto)
                    VALUES (?, ?, ?)";

            $stmt = $this->ejecutarConsulta(
                $sql,
                "iii",
                $cantidad,
                $idCarrito,
                $idProducto
            );

            $stmt->close();
        }
    }

    /**
     * Actualiza la cantidad de un producto.
     *
     * @param int $idDetalleCarrito
     * @param int $cantidad
     * @return bool
     */
    public function actualizarCantidad($idDetalleCarrito, $cantidad) {
        $sql = "UPDATE DetalleCarrito SET cantidadCarrito = ? WHERE idDetalleCarrito = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ii",
            $cantidad,
            $idDetalleCarrito
        );

        $correcto = $stmt->affected_rows >= 0;
        $stmt->close();
        return $correcto;
    }

    /**
    * Disminuye la cantidad de un producto del carrito.
    * Si la cantidad llega a 0, elimina el registro.
    *
    * @param int $idDetalleCarrito
    * @return bool
    */
    public function eliminarProducto($idDetalleCarrito) {
        $sql = "SELECT cantidadCarrito FROM DetalleCarrito WHERE idDetalleCarrito = ?";
        $stmt = $this->ejecutarConsulta($sql, "i", $idDetalleCarrito);

        $resultado = $stmt->get_result();
        $detalle = $resultado->fetch_assoc();
        $stmt->close();

        if (!$detalle) {
            return false;
        }

        if ($detalle['cantidadCarrito'] > 1) {
            $cantidadNueva = $detalle['cantidadCarrito'] - 1;
            
            $sql = "UPDATE DetalleCarrito SET cantidadCarrito = ? WHERE idDetalleCarrito = ?";
            $stmt = $this->ejecutarConsulta( $sql, "ii", $cantidadNueva, $idDetalleCarrito);

        } else {
            $sql = "DELETE FROM DetalleCarrito WHERE idDetalleCarrito = ?";
            $stmt = $this->ejecutarConsulta($sql, "i", $idDetalleCarrito);
        }

        $correcto = $stmt->affected_rows > 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Obtiene todos los productos del carrito.
     *
     * @param int $idUsuario
     * @return array
     */
    public function obtenerProductos($idUsuario) {
        $carrito = $this->getCarritoActivo($idUsuario);

        if (!$carrito) {
            return [];
        }

        $sql = "SELECT
                    dc.idDetalleCarrito,
                    dc.cantidadCarrito,
                    p.idProducto,
                    p.nombreProducto,
                    p.descripcion,
                    p.precio,
                    p.cantidadStock,
                    p.ventaEmpresarial,
                    c.nombreCategoria,
                    pr.tipoEmpaque,
                    pr.peso,
                    pr.tamaño
                FROM DetalleCarrito dc
                INNER JOIN Producto p
                    ON dc.idProducto = p.idProducto
                INNER JOIN Categoria c
                    ON p.idCategoria = c.idCategoria
                INNER JOIN Presentacion pr
                    ON p.idPresentacion = pr.idPresentacion
                WHERE dc.idCarrito = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $carrito['idCarrito']
        );

        $resultado = $stmt->get_result();
        $productos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        $stmt->close();
        return $productos;
    }

    /**
     * Calcula el total del carrito.
     *
     * @param int $idUsuario
     * @return float
     */
    public function calcularTotal($idUsuario) {
        $productos = $this->obtenerProductos($idUsuario);
        $total = 0;

        foreach ($productos as $producto) {
            $total += (
                $producto['precio'] *
                $producto['cantidadCarrito']
            );
        }

        return $total;
    }

    /**
     * Cuenta la cantidad total de productos.
     *
     * @param int $idUsuario
     * @return int
     */
    public function contarProductos($idUsuario) {
        $carrito = $this->getCarritoActivo($idUsuario);

        if (!$carrito) {
            return 0;
        }

        $sql = "SELECT SUM(cantidadCarrito) AS total FROM DetalleCarrito WHERE idCarrito = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $carrito['idCarrito']
        );

        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        return (int)($fila['total'] ?? 0);
    }

    /**
     * Elimina todos los productos y marca el carrito
     * como Abandonado.
     *
     * @param int $idUsuario
     */
    public function eliminarCarrito($idUsuario) {
        $carrito = $this->getCarritoActivo($idUsuario);

        if (!$carrito) {
            return;
        }

        $sql = "DELETE FROM DetalleCarrito WHERE idCarrito = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $carrito['idCarrito']
        );

        $stmt->close();

        $sql = "UPDATE Carrito
                SET estadoCarrito = 'Abandonado'
                WHERE idCarrito = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $carrito['idCarrito']
        );

        $stmt->close();
    }

    /**
     * Finaliza el carrito.
     *
     * @param int $idUsuario
     */
    public function finalizarCarrito($idUsuario) {
        $carrito = $this->getCarritoActivo($idUsuario);

        if (!$carrito) {
            return;
        }

        $sql = "UPDATE Carrito SET estadoCarrito = 'Finalizado' WHERE idCarrito = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "i",
            $carrito['idCarrito']
        );
        $stmt->close();
    }

}

