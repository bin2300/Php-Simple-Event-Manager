<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Booking.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$bookingModel = new Booking($conn);
$user_id = $_SESSION['user_id'];

// Vérifier si l'ID du panier a été envoyé
if (isset($_GET['cart_id']) && is_numeric($_GET['cart_id'])) {
    $cart_id = intval($_GET['cart_id']);
    
    // Supprimer l'élément du panier
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Ticket retiré du panier.";
    } else {
        $_SESSION['error'] = "Erreur lors du retrait du ticket.";
    }
} else {
    $_SESSION['error'] = "Ticket non trouvé.";
}

$conn->close();
header("Location: cart.php");
exit();
