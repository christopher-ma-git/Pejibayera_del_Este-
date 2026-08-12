<?php
require_once '../app/core/Controller.php';

class CarritoController extends Controller {
    private $carritoModel;
    private $productoModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        $this->carritoModel = $this->model('Carrito');
        $this->productoModel = $this->model('Producto');
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
     * Agrega un producto al carrito para usuarios Empresa.
     */
    public function addEmpresa() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/empresa/index');
        }

        $idProducto = (int) $_POST['idProducto'];
        $cantidad = (int) $_POST['cantidad'];
        $fechaEntrega = $_POST['fechaEntrega'];
        $observaciones = trim($_POST['observaciones'] ?? '');

        /*
         * Validar cantidad.
         */
        if ($cantidad < 10 || $cantidad > 50) {
            $_SESSION['error'] = "La cantidad debe estar entre 10 y 50 unidades.";
            $this->redirect('/empresa/index');
        }

        /*
         * Validar fecha de entrega.
         */
        if (!$this->validarFechaEntrega($fechaEntrega)) {
            $this->redirect('/empresa/index');
        }

        /*
         * Validar stock.
         */
        $producto = $this->productoModel->getById($idProducto);
        if (!$producto) {
            $_SESSION['error'] = "El producto seleccionado no existe.";
            $this->redirect('/empresa/index');
        }

        if ($cantidad > $producto['cantidadStock']) {
            $_SESSION['error'] = "No hay suficiente stock disponible.";
            $this->redirect('/empresa/index');
        }

        /*
         * Validar que sea un producto empresarial.
         */
        if (!$producto['ventaEmpresarial']) {
            $_SESSION['error'] = "El producto seleccionado no está disponible para empresas.";
            $this->redirect('/empresa/index');
        }

        /*
         * Agregar al carrito.
         */
        $this->carritoModel->agregarProducto(
            $_SESSION['user_id'],
            $idProducto,
            $cantidad,
            $fechaEntrega,
            $observaciones
        );

        $this->redirect('/empresa/index');
    }


    /**
     * Actualiza la cantidad.
     */
    public function updateCantidad() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idDetalle = $_POST['idDetalleCarrito'];
            $cantidad = (int) $_POST['cantidad']; //evita que llege un str

            $detalle = $this->carritoModel->getDetalleById($idDetalle);
            if (!$detalle) {
                $_SESSION['error'] = "El producto no existe.";
                $this->redirect('/carrito/index');
            }

            if (!$this->validarCantidad($cantidad, $detalle['cantidadStock'])) {
                $this->redirect('/carrito/index');
            }

            $this->carritoModel->actualizarCantidad($idDetalle, $cantidad);
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

        switch ($_SESSION['user_role']) {
            case 'Empresa':
                $this->redirect('/empresa/index');
                break;
            default:
                $this->redirect('/user/index');
                break;
        }
    }

    /**
    * Muestra la factura del carrito antes de realizar la compra.
    */
    public function comprar() {
        $carrito = $this->carritoModel->getCarritoActivo($_SESSION['user_id']);
        if (!$carrito) {
            $this->redirect('/carrito/index');
        }

        $productos = $this->carritoModel->obtenerProductos($carrito['idUsuario']);
        $total = $this->carritoModel->calcularTotal($carrito['idUsuario']);
        $this->view('other/compra', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'productos' => $productos,
            'fecha' => date('Y-m-d'),
            'total' => $total
        ]);
    }

    /**
     * Valida la fecha de entrega.
     *
     * Debe ser posterior al día actual
     * y no puede ser sábado ni domingo.
     */
    private function validarFechaEntrega($fechaEntrega) {
        if (empty($fechaEntrega)) {
            $_SESSION['error'] = "Debe seleccionar una fecha de entrega.";
            return false;
        }

        if (strtotime($fechaEntrega) <= strtotime(date('Y-m-d'))) {
            $_SESSION['error'] =
                "La fecha de entrega debe ser posterior a la fecha actual.";
            return false;
        }

        $diaSemana = date('N', strtotime($fechaEntrega)); // 'N' : numero de semana (1-7)
        if ($diaSemana >= 6) {
            $_SESSION['error'] =
                "No se realizan entregas sábado ni domingo.";
            return false;
        }

        return true;
    }

    private function validarCantidad($cantidad, $stock) {
        if ($_SESSION['user_role'] == 'Empresa') {
            if ($cantidad < 10 || $cantidad > 50) {
                $_SESSION['error'] = "Los pedidos empresariales deben ser entre 10 y 50 unidades.";
                return false;
            }

        } else {
            if ($cantidad < 1) {
                $_SESSION['error'] = "La cantidad debe ser mayor a 0.";
                return false;
            }
        }

        if ($cantidad > $stock) {
            $_SESSION['error'] = "La cantidad solicitada supera el stock disponible.";
            return false;
        }

        return true;
    }

}

