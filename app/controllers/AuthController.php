<?php
require_once '../core/Controller.php';

class AuthController extends Controller
{
    public function index()
    {
        if ($_SERVER['REQUEST_URI'] != "/") {
            header("Location: /");
        } else {
            $this->view('auth/index');
        }
    }

    public function iniciarSesion() {}

    public function logout()
    {
        session_destroy();
        header("Location: /auth/index");
        exit;
    }

    public function registro() {}

    public function registrar() {}
}
