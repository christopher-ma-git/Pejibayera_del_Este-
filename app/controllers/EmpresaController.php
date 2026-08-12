<?php
require_once '../app/core/Controller.php';

class EmpresaController extends Controller {
    private $empresaModel;
    private $carritoModel;
    private $productoModel;

    public function __construct() {
        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Empresa') {
            $this->redirect('/auth/index');
        }

        $this->empresaModel = $this->model('Empresa');
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
     * Pantalla principal de la empresa.
     */
    public function index() {
        $this->view('empresa/index', [
            'cantidadTotal' => $this->obtenerCantidadCarrito(),
            'productos' => $this->productoModel->getEmpresariales()
        ]);
    }

}

