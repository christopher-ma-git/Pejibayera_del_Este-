<?php
require_once '../app/config/Database.php';

// app/models/Producto.php
class Producto {
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
     * Obtiene todos los productos disponibles para clientes.
     *
     * @return array
     */
    public function getAll() {
        $sql = "SELECT
                    p.idProducto,
                    p.nombreProducto,
                    p.descripcion,
                    p.precio,
                    p.cantidadStock,
                    p.ventaEmpresarial,
                    c.idCategoria,
                    c.nombreCategoria,
                    pr.idPresentacion,
                    pr.tipoEmpaque,
                    pr.peso,
                    pr.tamaño
                FROM Producto p
                INNER JOIN Categoria c
                    ON p.idCategoria = c.idCategoria
                INNER JOIN Presentacion pr
                    ON p.idPresentacion = pr.idPresentacion
                WHERE p.ventaEmpresarial = FALSE
                ORDER BY p.idProducto ASC";

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
     * Obtiene un producto por ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT
                    p.idProducto,
                    p.nombreProducto,
                    p.descripcion,
                    p.precio,
                    p.cantidadStock,
                    p.ventaEmpresarial,
                    c.idCategoria,
                    c.nombreCategoria,
                    pr.idPresentacion,
                    pr.tipoEmpaque,
                    pr.peso,
                    pr.tamaño
                FROM Producto p
                INNER JOIN Categoria c
                    ON p.idCategoria = c.idCategoria
                INNER JOIN Presentacion pr
                    ON p.idPresentacion = pr.idPresentacion
                WHERE p.idProducto = ?
                LIMIT 1";

        $stmt = $this->ejecutarConsulta($sql, "i", $id);

        $producto = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $producto;
    }

    /**
     * Registra un nuevo producto.
     *
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO Producto
                (
                    nombreProducto,
                    descripcion,
                    precio,
                    cantidadStock,
                    ventaEmpresarial,
                    idCategoria,
                    idPresentacion
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ssdiiii",
            $data['nombreProducto'],
            $data['descripcion'],
            $data['precio'],
            $data['cantidadStock'],
            $data['ventaEmpresarial'],
            $data['idCategoria'],
            $data['idPresentacion']
        );

        $resultado = $stmt->affected_rows > 0;

        $stmt->close();

        return $resultado;
    }

    /**
     * Actualiza un producto.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE Producto
                SET
                    nombreProducto = ?,
                    descripcion = ?,
                    precio = ?,
                    cantidadStock = ?,
                    ventaEmpresarial = ?,
                    idCategoria = ?,
                    idPresentacion = ?
                WHERE idProducto = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ssdiiiii",
            $data['nombreProducto'],
            $data['descripcion'],
            $data['precio'],
            $data['cantidadStock'],
            $data['ventaEmpresarial'],
            $data['idCategoria'],
            $data['idPresentacion'],
            $id
        );

        $resultado = $stmt->affected_rows > 0;

        $stmt->close();

        return $resultado;
    }

    /**
     * Reduce la cantidad en stock de un producto.
     *
     * @param int $idProducto
     * @param int $cantidadVendida
     * @return bool
     */
    public function updateStock($idProducto, $nuevoStock) {
        $producto = $this->getById($idProducto);
        if (!$producto) {
            return false;
        }

        if ($nuevoStock < 0) {
            return false;
        }

        $sql = "UPDATE Producto SET cantidadStock = ? WHERE idProducto = ?";

        $stmt = $this->ejecutarConsulta(
            $sql,
            "ii",
            $nuevoStock,
            $idProducto
        );

        $correcto = $stmt->affected_rows >= 0;
        $stmt->close();
        return $correcto;
    }

    /**
     * Elimina un producto.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM Producto
                WHERE idProducto = ?";

        $stmt = $this->ejecutarConsulta($sql, "i", $id);

        $resultado = $stmt->affected_rows > 0;

        $stmt->close();

        return $resultado;
    }

    /**
     * Obtiene los productos de una categoría.
     *
     * @param int $idCategoria
     * @return array
     */
    public function getByCategoria($idCategoria) {
        $sql = "SELECT
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
                FROM Producto p
                INNER JOIN Categoria c
                    ON p.idCategoria = c.idCategoria
                INNER JOIN Presentacion pr
                    ON p.idPresentacion = pr.idPresentacion
                WHERE p.idCategoria = ?
                AND p.ventaEmpresarial = FALSE
                ORDER BY p.nombreProducto";

        $stmt = $this->ejecutarConsulta($sql, "i", $idCategoria);
        $resultado = $stmt->get_result();
        $productos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        $stmt->close();
        return $productos;
    }

    /**
     * Obtiene todos los productos disponibles para venta empresarial.
     *
     * @return array
     */
    public function getEmpresariales() {
        $sql = "SELECT
                    p.*,
                    c.nombreCategoria,
                    pr.tipoEmpaque,
                    pr.peso,
                    pr.tamaño
                FROM Producto p
                INNER JOIN Categoria c
                    ON p.idCategoria = c.idCategoria
                INNER JOIN Presentacion pr
                    ON p.idPresentacion = pr.idPresentacion
                WHERE p.ventaEmpresarial = 1
                ORDER BY p.nombreProducto ASC";

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
     * Obtiene todas las categorías.
     *
     * @return array
     */
    public function getCategorias() {
        $sql = "SELECT * FROM Categoria ORDER BY nombreCategoria";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = $fila;
        }

        $stmt->close();
        return $categorias;
    }

    /**
     * Obtiene todas las presentaciones.
     *
     * @return array
     */
    public function getPresentaciones() {
        $sql = "SELECT * FROM Presentacion ORDER BY tipoEmpaque, tamaño";

        $stmt = $this->ejecutarConsulta($sql);
        $resultado = $stmt->get_result();
        $presentaciones = [];

        while ($fila = $resultado->fetch_assoc()) {
            $presentaciones[] = $fila;
        }

        $stmt->close();
        return $presentaciones;
    }
}