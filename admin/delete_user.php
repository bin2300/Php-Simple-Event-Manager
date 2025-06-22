<?php
require_once('../includes/admin_only.php');
require_once('../includes/db/Database.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Empêcher la suppression de soi-même
if ($user_id == $_SESSION['user_id']) {
    header("Location: users.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$db->close();

header("Location: users.php");
exit();
