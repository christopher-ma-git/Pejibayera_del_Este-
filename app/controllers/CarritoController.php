<?php
require_once '../app/core/Controller.php';

class CarritoController extends Controller {

    private $carritoModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        $this->carritoModel = $this->model('Carrito');
    }

    /**
     * Obtiene la cantidad de productos en el carrito.
     *
     * @return int
     */
    private function obtenerCantidadCarrito() {
        return $this->carritoModel->contarProductos(
            $_SESSION['user_id']
        );
    }

    /**
     * Muestra el carrito.
     */
    public function index() {
        $productos = $this->carritoModel->obtenerProductos($_SESSION['user_id']);
        $total = $this->carritoModel->calcularTotal($_SESSION['user_id']);
        $cantidadTotal = $this->carritoModel->contarProductos($_SESSION['user_id']);

        $this->view('other/carrito', [
            'productos' => $productos,
            'total' => $total,
            'cantidadTotal' => $cantidadTotal
        ]);
    }

    /**
     * Agrega un producto al carrito.
     */
    public function add($idProducto = null) {
        if ($idProducto) {
            $this->carritoModel->agregarProducto(
                $_SESSION['user_id'],
                $idProducto
            );
        }

        $this->redirect('/user/index');
    }

    /**
     * Actualiza la cantidad.
     */
    public function updateCantidad() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idDetalle = $_POST['idDetalleCarrito'];
            $cantidad = $_POST['cantidad'];

            $this->carritoModel->actualizarCantidad(
                $idDetalle,
                $cantidad
            );
        }

        $this->redirect('/carrito/index');
    }

    /**
     * Elimina un producto.
     */
    public function delete($idDetalle = null) {
        if ($idDetalle) {
            $this->carritoModel->eliminarProducto($idDetalle);
        }

        $this->redirect('/carrito/index');
    }

    /**
     * Elimina todo el carrito.
     */
    public function eliminarCarrito() {
        $this->carritoModel->eliminarCarrito(
            $_SESSION['user_id']
        );

        $this->redirect('/user/index');
    }

    /**
    * Muestra la factura del carrito antes de realizar la compra.
    */
    public function comprar() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        $carrito = $this->carritoModel->getCarritoActivo(
            $_SESSION['user_id']
        );

        if (!$carrito) {
            $this->redirect('/carrito/index');
        }

        $productos = $this->carritoModel->obtenerProductos(
            $carrito['idUsuario']
        );

        $total = $this->carritoModel->calcularTotal(
            $carrito['idUsuario']
        );

        $this->view('other/compra', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'productos' => $productos,
            'fecha' => date('Y-m-d'),
            'total' => $total
        ]);
    }

}

