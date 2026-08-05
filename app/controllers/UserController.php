<?php
require_once '../app/core/Controller.php';

class UserController extends Controller {
    private $userModel;
    private $productoModel;
    private $carritoModel;

    public function __construct() {
        session_start();
        // if (!isset($_SESSION['user_id'])) { de momento no se va a verificar la sesión, se verifica por método que lo ocupe (edit, delete..)
        //     $this->redirect('/auth/index');
        // }
        $this->userModel = $this->model('User');
        $this->productoModel = $this->model('Producto');
        $this->carritoModel = $this->model('Carrito');
    }

    private function obtenerCantidadCarrito() {
        if(!isset($_SESSION['user_id'])) {
            return 0;
        }
        return $this->carritoModel->contarProductos($_SESSION['user_id']);
    }

    public function index() {
        $productos = $this->productoModel->getAll();
        $this->view('users/index', [
            'productos' => $productos,
            'cantidadTotal' => $this->obtenerCantidadCarrito()
        ]);
    }
 
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'apellido' => $_POST['apellido'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'rol' => $_POST['rol'] ?? 'Cliente',
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? ''
            ];
            
            // Validación básica
            if (!empty($data['nombre']) && !empty($data['email']) && !empty($data['password']) && !empty($data['rol'])) {
                // Verificar si el email ya existe
                if($this->userModel->getByEmail($data['email'])){
                    $this->view('users/create', ['error' => 'El email ya está registrado']);
                    return;
                }
                
                $this->userModel->create($data);
                $this->redirect('/user/index');
            } else {
                $this->view('users/create', ['error' => 'El nombre, email, contraseña y rol son obligatorios']);
            }
        } else {
            $this->view('users/create');
        }
    }

    public function edit($id = null) {
        if (!$id) {
            $this->redirect('/user/index');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre'    => $_POST['nombre'] ?? '',
                'apellido'  => $_POST['apellido'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'password'  => $_POST['password'] ?? '',
                'rol'       => $_POST['rol'] ?? 'Cliente',
                'telefono'  => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? ''
            ];
            
            if (!empty($data['nombre']) && !empty($data['email'])) {
                // Verificamos si quiere cambiar el email, que no exista ya en otro usuario
                $existingUser = $this->userModel->getByEmail($data['email']);
                if($existingUser && $existingUser['id'] != $id){
                    $user = $this->userModel->getById($id);
                    $this->view('users/edit', [
                        'user' => $user, 
                        'error' => 'El email ya está en uso por otro usuario'
                    ]);
                    return;
                }

                $this->userModel->update($id, $data);
                $this->redirect('/user/index');
            } else {
                $user = $this->userModel->getById($id);
                $this->view('users/edit', [
                    'user' => $user, 
                    'error' => 'Nombre y Email son obligatorios'
                ]);
            }
        } else {
            $user = $this->userModel->getById($id);
            if ($user) {
                $this->view('users/edit', ['user' => $user]);
            } else {
                $this->redirect('/user/index');
            }
        }
    }


    public function update() {

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
        }

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/otherview/editPrf');
        }

        $data = [
            'nombre' => trim($_POST['nombreUsuario'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'apellido' => trim($_POST['apellido'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'cedulaJuridica' => trim($_POST['cedulaJuridica'] ?? '')
        ];

        if ($_SESSION['user_role'] == 'Empresa') {
            $correcto = $this->userModel->updateEmpresa($_SESSION['user_id'], $data);

        } else {
            $correcto = $this->userModel->updateCliente($_SESSION['user_id'], $data);
        }

        if ($correcto) {
            $usuario = $this->userModel->getById($_SESSION['user_id']);
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_role'] = $usuario['rol'];

            $this->redirect('/otherview/perfil');

        } else {
            $this->redirect('/otherview/editPrf');
        }        

    }

    public function delete($id = null) {
        if ($id) {
            // Evitar que el usuario se elimine a sí mismo
            if ($id != $_SESSION['user_id']) {
                $this->userModel->delete($id);
            }
        }
        $this->redirect('/user/index');
    }
}
