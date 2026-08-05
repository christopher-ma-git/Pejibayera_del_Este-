<?php
require_once '../app/core/Controller.php';

class AdminController extends Controller {
    private $carritoModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Administrador') {
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
     * Pantalla principal del administrador.
     */
    public function index() {
        $this->view('admin/index', [
            'cantidadTotal' => $this->obtenerCantidadCarrito()
        ]);
    }

}

