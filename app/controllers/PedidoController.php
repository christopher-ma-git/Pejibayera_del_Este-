<?php
require_once '../app/core/Controller.php';

class PedidoController extends Controller {
    private $pedidoModel;
    private $detallePedidoModel;
    private $carritoModel;
    private $productoModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        $this->pedidoModel = $this->model('Pedido');
        $this->detallePedidoModel = $this->model('DetallePedido');
        $this->carritoModel = $this->model('Carrito');
        $this->productoModel = $this->model('Producto');
    }

    /**
     * Finaliza una compra.
     */
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/carrito/comprar');
        }

        if (!$this->validarTarjeta($_POST)) {
            $this->redirect('/carrito/comprar');
        }

        $carrito = $this->carritoModel->getCarritoActivo($_SESSION['user_id']);
        if (!$carrito) {
            $this->redirect('/carrito/index');
        }

        $productos = $this->carritoModel->obtenerProductos($carrito['idUsuario']);
        if (empty($productos)) {
            $this->redirect('/carrito/index');
        }


        /*
         * Validar stock antes de realizar la compra.
         */
        foreach ($productos as $producto) {
            if ($producto['cantidadCarrito'] > $producto['cantidadStock']) {
                $_SESSION['error'] = "No hay suficiente stock para " . $producto['nombreProducto'];
                $this->redirect('/carrito/index');
            }

            if ($_SESSION['user_role'] == 'Cliente' && $producto['ventaEmpresarial']) {
                $_SESSION['error'] = "El producto {$producto['nombreProducto']} es exclusivo para empresas.";
                $this->redirect('/carrito/index');
            }

            if ($_SESSION['user_role'] == 'Empresa' && !$producto['ventaEmpresarial']) {
                $_SESSION['error'] = "El producto {$producto['nombreProducto']} es exclusivo para clientes.";
                $this->redirect('/carrito/index');
            }
        }

        /*
         * Crear Pedido.
         */
        $tipoPedido = ($_SESSION['user_role'] == 'Empresa') ? 'Empresa' : 'Individual';

        $idPedido = $this->pedidoModel->create([
            'idUsuario' => $_SESSION['user_id'],
            'tipoPedido' => $tipoPedido,
            'fechaEntrega' => $carrito['fechaEntrega'],
            'observaciones' => $carrito['observaciones']
        ]);


        /*
         * Crear DetallePedido y actualizar stock.
         */
        foreach ($productos as $producto) {
            $this->detallePedidoModel->create([
                'precioUnitario' => $producto['precio'],
                'cantidad' => $producto['cantidadCarrito'],
                'subtotal' => $producto['precio'] * $producto['cantidadCarrito'],
                'idPedido' => $idPedido,
                'idProducto' => $producto['idProducto']
            ]);

            $nuevoStock = $producto['cantidadStock'] - $producto['cantidadCarrito'];
            $this->productoModel->updateStock($producto['idProducto'], $nuevoStock);
        }

        /*
         * Finalizar carrito.
         */
        $this->carritoModel->finalizarCarrito(
            $carrito['idUsuario']
        );

        /*
         * Redireccionar según el rol.
         */
        switch ($_SESSION['user_role']) {
            case 'Administrador':
                $this->redirect('/admin/index');
                break;
            case 'Empresa':
                $this->redirect('/empresa/index');
                break;
            default:
                $this->redirect('/user/index');
                break;
        }
    }

    /**
     * Muestra todos los pedidos.
     * (Administrador)
     */
    public function index() {
        $pedidos = $this->pedidoModel->getAll();

        $this->view('admin/pedidos', [
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Muestra el detalle de un pedido.
     *
     * @param int $idPedido
     */
    public function detalle($idPedido) {
        $pedido = $this->pedidoModel->getById($idPedido);

        if (!$pedido) {
            $this->redirect('/admin/index');
        }

        $detalle = $this->detallePedidoModel
            ->getByPedido($idPedido);

        $this->view('other/detallePedido', [
            'pedido' => $pedido,
            'detalle' => $detalle
        ]);
    }

    private function validarTarjeta($data) { 
        if (empty($data['numeroTarjeta']) || empty($data['fechaVencimiento']) || empty($data['pin'])) {
            $_SESSION['error'] = "Todos los datos de la tarjeta son obligatorios.";
            return false;
        }

        if (!ctype_digit($data['numeroTarjeta']) || strlen($data['numeroTarjeta']) != 16) {
            $_SESSION['error'] = "El número de tarjeta debe contener exactamente 16 dígitos.";
            return false;
        }

        if (!ctype_digit($data['pin']) || strlen($data['pin']) != 4) {
            $_SESSION['error'] = "El PIN debe contener exactamente 4 dígitos.";
            return false;
        }

        if (strtotime($data['fechaVencimiento']) < strtotime(date('Y-m-d'))) {
            $_SESSION['error'] = "La tarjeta se encuentra vencida.";
            return false;
        }
        return true;
    }


}

