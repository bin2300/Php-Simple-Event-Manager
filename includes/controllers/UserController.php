<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    public $userModel;

    public function __construct($conn) {
        $this->userModel = new User($conn);
    }

    public function getAllUsers() {
        return $this->userModel->getAll();
    }

    public function deleteUser($id) {
        return $this->userModel->delete($id);
    }

    public function toggleAdmin($id) {
        return $this->userModel->toggleAdmin($id,1);
    }
}
