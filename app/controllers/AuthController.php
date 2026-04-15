<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function index() {
        $this->login();
    }

    public function login() {
        // Init data
        $data = [
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);

            if (empty($data['email'])) $data['email_err'] = 'Please enter email';
            if (empty($data['password'])) $data['password_err'] = 'Please enter password';

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect or user not found';
                    $this->view('auth/login', $data);
                }
            } else {
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login', $data);
        }
    }

    public function register() {
        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => 'traveler', // default
            'name_err' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data['name'] = trim($_POST['name']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['role'] = trim($_POST['role']); // 'traveler' or 'host'

            // basic validation
            if(empty($data['name'])) $data['name_err'] = 'Please enter name';
            if(empty($data['email'])) { 
                $data['email_err'] = 'Please enter email';
            } else {
                if($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }
            if(empty($data['password']) || strlen($data['password']) < 6) $data['password_err'] = 'Password must be at least 6 chars';

            if(empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err'])) {
                // hash possword
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                
                if($this->userModel->register($data)) {
                    header('Location: ' . URLROOT . '/auth/login');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('auth/register', $data);
            }
        } else {
            $this->view('auth/register', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        header('Location: ' . URLROOT . '/dashboard');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('Location: ' . URLROOT . '/auth/login');
    }
}