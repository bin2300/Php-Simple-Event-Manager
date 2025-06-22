<?php
require_once('../includes/admin_only.php');
require_once('../includes/db/Database.php');
require_once('../includes/controllers/UserController.php');

if (isset($_GET['id']) && isset($_GET['admin'])) {
    $userId = (int)$_GET['id'];
    $newStatus = (int)$_GET['admin'];

    $db = new Database();
    $conn = $db->connect();
    $controller = new UserController($conn);
    $controller->userModel->toggleAdmin($userId, $newStatus);  // Appel direct ici
}

header('Location: users.php');
exit();
