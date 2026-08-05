<?php
require_once '../app/core/Controller.php';

class AuthController extends Controller {
    
    public function __construct() {
        session_start();
    }

    /**
     * Inicia la sesión del usuario y redirecciona según su rol.
     *
     * @param array $user
     */
    private function iniciarSesion($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_role'] = $user['rol'];

        switch ($user['rol']) {
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

    public function index() {
        if (isset($_SESSION['user_id'])) {
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

        $this->view('auth/login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirect('/auth/index');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $userModel = $this->model('User');
        $user = $userModel->getByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->view('auth/login', [
                'error' => 'Credenciales incorrectas.'
            ]);
            return;
        }

        if ($user['estadoUsuario'] != 'Activo') {
            $this->view('auth/login', [
                'error' => 'La cuenta se encuentra inactiva.'
            ]);
            return;
        }

        $this->iniciarSesion($user);
    }

    public function registro() {
        if (isset($_SESSION['user_id'])) {
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
                
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->view('auth/registro');
            return;
        }
                
        $userModel = $this->model('User');
        $empresaModel = $this->model('Empresa');
                
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'rol' => $_POST['rol'] ?? '',
            'cedulaJuridica' => trim($_POST['cedulaJuridica'] ?? '')
        ];
                
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password']) || empty($data['rol'])) {
            $this->view('auth/registro', [
                'error' => 'Complete todos los campos obligatorios.'
            ]);
            return;
        }
                
        if ($userModel->getByEmail($data['email'])) {
            $this->view('auth/registro', [
                'error' => 'El correo electrónico ya está registrado.'
            ]);
            return;
        }
                
        if ($data['rol'] == 'Empresa' && empty($data['cedulaJuridica'])) {
            $this->view('auth/registro', [
                'error' => 'La cédula jurídica es obligatoria.'
            ]);
            return;
        }
                
        $correcto = $userModel->create($data);
        if (!$correcto) {
            $this->view('auth/registro', [
                'error' => 'No fue posible crear el usuario.'
            ]);
            return;
        }
                
        $user = $userModel->getByEmail($data['email']);
        if (!$user) {
            $this->view('auth/registro', [
                'error' => 'Ocurrió un error al recuperar el usuario.'
            ]);
            return;
        }
                
        if ($data['rol'] == 'Empresa') {
            $correcto = $empresaModel->create(
                $data['cedulaJuridica'],
                $user['id']
            );
                
            if (!$correcto) {
                // Rollback manual
                $userModel->delete($user['id']);
                $this->view('auth/registro', [
                    'error' => 'No fue posible registrar la empresa.'
                ]);
                return;
            }
        }
                
        $this->iniciarSesion($user);
    }

    public function logout() {
        session_destroy();
        $this->redirect('/otherview/index');
    }
}

