<?php
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $userModel;


    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }


    public function register($name, $email, $password, &$error_message)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "L'adresse email est invalide";
            return false;
        }

        if (strlen($password) < 4 || strlen($password) > 20) {
            $error_message = "Le mot de passe doit contenir entre 4 et 20 caractères.";
            return false;
        }

        if ($this->userModel->emailExists($email)) {
            $error_message = "Cet email est deja utilisé";
            return false;
        }

        return $this->userModel->register($name, $email, $password);
    }

    public function login($email, $password, &$error_message)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Adresse email invalide.";
            return false;
        }

        $user = $this->userModel->authenticate($email, $password);
        if ($user) {
            // Stocker en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }

        $error_message = "Email ou mot de passe incorrect.";
        return false;
    }
}
