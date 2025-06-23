<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Booking.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();
$bookingModel = new Booking($conn);

// Confirmer la réservation
if ($bookingModel->checkout($user_id)) {
    $_SESSION['success'] = "Votre réservation a été confirmée avec succès!";
    header("Location: dashboard.php"); // Rediriger vers la page des réservations de l'utilisateur
    exit();
} else {
    $_SESSION['error'] = "Une erreur est survenue lors de la confirmation de la réservation.";
    header("Location: cart.php"); // Rediriger vers le panier en cas d'erreur
    exit();
}
