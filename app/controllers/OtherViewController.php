<?php
require_once '../app/core/Controller.php';

class OtherViewController extends Controller {

    private $carritoModel;
    private $userModel;
    private $empresaModel;
    private $pedidoModel;

    public function __construct() {
        session_start();
        $this->carritoModel = $this->model('Carrito');
        $this->userModel = $this->model('User');
        $this->empresaModel = $this->model('Empresa');
        $this->pedidoModel = $this->model('Pedido');
    }

    private function obtenerCantidadCarrito() {
        if(!isset($_SESSION['user_id'])) {
            return 0;
        }

        return $this->carritoModel->contarProductos($_SESSION['user_id']);
    }

    public function index() {
        $productoModel = $this->model('Producto');
        $productos = $productoModel->getAll();

        $this->view('users/index',[
            'productos' => $productos,
            'cantidadTotal' => $this->obtenerCantidadCarrito()
        ]);
    }

    public function contacto() {
        $this->view('other/contacto', [
            'cantidadTotal' => $this->obtenerCantidadCarrito()
        ]);
    }

    public function temporada() {
        $this->view('other/temporada', [
            'cantidadTotal' => $this->obtenerCantidadCarrito()
        ]);
    }

    //Metodos Privados (el usuario ocupa sí o sí estar logueado)
    public function perfil() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        $pedidos = $this->pedidoModel->getByUsuario($_SESSION['user_id']);

        $this->view('other/perfil', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'pedidos' => $pedidos
        ]);
    }

    public function editPrf() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        $usuario = $this->userModel->getById($_SESSION['user_id']);
        $empresa = null;

        if ($_SESSION['user_role'] == 'Empresa') {
            $empresa = $this->empresaModel->getByUsuario($_SESSION['user_id']);
        }

        $this->view('other/editPrf', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'usuario' => $usuario,
            'empresa' => $empresa
        ]);
    }


}

