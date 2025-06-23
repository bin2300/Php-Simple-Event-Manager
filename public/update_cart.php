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

// Vérifier si les données de quantités ont été envoyées
if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
    $updated = false;

    // Parcourir les quantités et les mettre à jour dans la base de données
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        $quantity = intval($quantity);
        
        // Vérifier si la quantité est valide (doit être supérieure à zéro)
        if ($quantity > 0) {
            // Mettre à jour la quantité du ticket dans le panier
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            if ($stmt->execute()) {
                $updated = true;
            }
        }
    }

    if ($updated) {
        $_SESSION['success'] = "Panier mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du panier.";
    }
} else {
    $_SESSION['error'] = "Aucune quantité valide reçue.";
}

$conn->close();
header("Location: cart.php");
exit();
