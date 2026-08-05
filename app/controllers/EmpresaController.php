<?php
require_once '../app/core/Controller.php';

class EmpresaController extends Controller {

    private $empresaModel;
    private $carritoModel;

    public function __construct() {

        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Empresa') {
            $this->redirect('/auth/index');
        }

        $this->empresaModel = $this->model('Empresa');
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
     * Pantalla principal de la empresa.
     */
    public function index() {

        $productos = $this->empresaModel->getProductos();

        $pedidos = $this->empresaModel->getPedidos(
            $_SESSION['user_id']
        );

        $empresa = $this->empresaModel->getByUsuario(
            $_SESSION['user_id']
        );

        $this->view('empresa/index', [

            'cantidadTotal' => $this->obtenerCantidadCarrito(),

            'productos' => $productos,

            'pedidos' => $pedidos,

            'empresa' => $empresa

        ]);

    }
 /**
 * Crea un nuevo pedido empresarial.
 */
public function crearPedido() {

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        $this->redirect('/empresa/index');
    }

    $idUsuario = $_SESSION['user_id'];

    $idProducto = $_POST['idProducto'];

    $cantidad = $_POST['cantidad'];

    $fechaEntrega = $_POST['fechaEntrega'];

    $observaciones = trim($_POST['comentario']);

    $this->empresaModel->crearPedido(

        $idUsuario,

        $idProducto,

        $cantidad,

        $fechaEntrega,

        $observaciones

    );

    $this->redirect('/empresa/index');

}

}