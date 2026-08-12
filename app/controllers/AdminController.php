<?php
require_once '../app/core/Controller.php';

class AdminController extends Controller {
    private $carritoModel;
    private $pedidoModel;
    private $detallePedidoModel;
    private $productoModel;
    private $userModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Administrador') {
            $this->redirect('/auth/index');
        }

        $this->carritoModel = $this->model('Carrito');
        $this->pedidoModel = $this->model('Pedido');
        $this->detallePedidoModel = $this->model('DetallePedido');
        $this->productoModel = $this->model('Producto');
        $this->userModel = $this->model('User');
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
     * Pantalla principal del administrador.
     * Debe mostrar:
     * 1. total de clientes (más un porcentaje de crecimiento)
     * 2. total de empresas (más un porcentaje de crecimiento)
     * 3. total pedidos (más un porcentaje de crecimiento)
     * 4. total de dinero ganado en ventas (más un porcentaje de crecimiento)
     * 5. top 4 de productos más vendidos
     */
    public function index() {
        $clientes = $this->userModel->getContarClientes();
        $empresas = $this->userModel->getContarEmpresas();
        $pedidos = $this->pedidoModel->getContarPedidos();
        $ventas = $this->pedidoModel->getContarVentas();
        $productosMasVendidos = $this->pedidoModel->getContarPedidosProducto();

        $this->view('admin/index', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'clientes' => $clientes,
            'empresas' => $empresas,
            'pedidos' => $pedidos,
            'ventas' => $ventas,
            'productosMasVendidos' => $productosMasVendidos
        ]);
    }

    /**
     *  Pantalla secundaria del administrador.
     *      Debe mostrar en una tabla (nombre usuario, fecha pedido, estado pedido, precio, producto, cantidad y accion)
     *      Debe permitir por medio de un select actualizar el estado del pedido o eliminarlo
     *      Toda la información presentada debe corresponder a usuario con rol "Cliente"
     *  IMPORTANTE, SE DEBE ACTUALIZAR EL MÉTODO DEL CONTROLADOR PARA PASAR LOS MÉTODOS DE UPDATE Y DELETE
     */
    public function pedidosCliente() {
        $pedidos = $this->pedidoModel->getPedidosCliente();

        $this->view('admin/pedidosClient', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'pedidos' => $pedidos
        ]);
    }

    /**
     *  Pantalla secundaria del administrador.
     *      Debe mostrar en una tabla (nombre usuario, fecha pedido, fecha entrega, estado pedido, precio, producto, cantidad y accion)
     *      Debe permitir por medio de un select actualizar el estado del pedido o eliminarlo
     *      Toda la información presentada debe corresponder a usuario con rol "Empresa"
     *  IMPORTANTE, SE DEBE ACTUALIZAR EL MÉTODO DEL CONTROLADOR PARA PASAR LOS MÉTODOS DE UPDATE Y DELETE
     */
    public function pedidosEmpresa() {
        $pedidos = $this->pedidoModel->getPedidosEmpresa();

        $this->view('admin/pedidosEmpr', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'pedidos' => $pedidos
        ]);
    }

    public function updateEstado() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/admin/index');
        }

        $idPedido = $_POST['idPedido'];
        $estado = $_POST['estadoPedido'];
        $origen = $_POST['origen'];

        $this->pedidoModel->updateEstado(
            $idPedido,
            $estado
        );

        if ($origen == 'Empresa') {
            $this->redirect('/admin/pedidosEmpresa');
        }

        $this->redirect('/admin/pedidosCliente');
    }

    public function deletePedido() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/admin/index');
        }

        $idPedido = $_POST['idPedido'];
        $origen = $_POST['origen'];
        $this->pedidoModel->delete($idPedido);

        if ($origen == 'Empresa') {
            $this->redirect('/admin/pedidosEmpresa');
        }

        $this->redirect('/admin/pedidosCliente');
    }

}

