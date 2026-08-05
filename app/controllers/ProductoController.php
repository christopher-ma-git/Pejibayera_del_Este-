<?php
// app/controllers/ProductoController.php

class ProductoController extends Controller {
    private $productoModel;

    public function __construct() {
        $this->model('Producto');
        $this->productoModel = new Producto();
    }

    /**
     * Muestra todos los productos.
     */
    public function index() {
        $productos = $this->productoModel->getAll();

        $this->view('producto/index', [
            'productos' => $productos
        ]);
    }

    /**
     * Muestra un producto específico.
     *
     * @param int $id
     */
    public function show($id) {
        $producto = $this->productoModel->getById($id);

        if (!$producto) {
            header("Location: " . URLROOT . "/producto");
            exit();
        }

        $this->view('producto/show', [
            'producto' => $producto
        ]);
    }

    /**
     * Carga el formulario para registrar un producto.
     */
    public function create() {
        $categorias = $this->productoModel->getCategorias();
        $presentaciones = $this->productoModel->getPresentaciones();

        $this->view('producto/create', [
            'categorias' => $categorias,
            'presentaciones' => $presentaciones
        ]);
    }

    /**
     * Guarda un producto.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'nombreProducto' => trim($_POST['nombreProducto']),
                'descripcion' => trim($_POST['descripcion']),
                'precio' => trim($_POST['precio']),
                'cantidadStock' => trim($_POST['cantidadStock']),
                'ventaEmpresarial' => isset($_POST['ventaEmpresarial']) ? 1 : 0,
                'idCategoria' => trim($_POST['idCategoria']),
                'idPresentacion' => trim($_POST['idPresentacion'])
            ];

            $this->productoModel->create($data);

            header("Location: " . URLROOT . "/producto");
            exit();
        }

        $this->create();
    }

    /**
     * Carga el formulario de edición.
     *
     * @param int $id
     */
    public function edit($id) {
        $producto = $this->productoModel->getById($id);

        if (!$producto) {
            header("Location: " . URLROOT . "/producto");
            exit();
        }

        $categorias = $this->productoModel->getCategorias();
        $presentaciones = $this->productoModel->getPresentaciones();

        $this->view('producto/edit', [
            'producto' => $producto,
            'categorias' => $categorias,
            'presentaciones' => $presentaciones
        ]);
    }

    /**
     * Actualiza un producto.
     *
     * @param int $id
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'nombreProducto' => trim($_POST['nombreProducto']),
                'descripcion' => trim($_POST['descripcion']),
                'precio' => trim($_POST['precio']),
                'cantidadStock' => trim($_POST['cantidadStock']),
                'ventaEmpresarial' => isset($_POST['ventaEmpresarial']) ? 1 : 0,
                'idCategoria' => trim($_POST['idCategoria']),
                'idPresentacion' => trim($_POST['idPresentacion'])
            ];

            $this->productoModel->update($id, $data);

            header("Location: " . URLROOT . "/producto");
            exit();
        }

        $this->edit($id);
    }

    /**
     * Elimina un producto.
     *
     * @param int $id
     */
    public function delete($id) {
        $this->productoModel->delete($id);

        header("Location: " . URLROOT . "/producto");
        exit();
    }
}