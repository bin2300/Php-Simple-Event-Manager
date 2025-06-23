<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Booking.php");
require_once("../includes/models/Ticket.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté et si un booking_id est fourni
if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->connect();
$bookingModel = new Booking($conn);

// Récupérer les détails de la réservation
$bookingDetails = $bookingModel->getUserBookings($user_id);
$booking = null;
while ($b = $bookingDetails->fetch_assoc()) {
    if ($b['booking_id'] == $booking_id) {
        $booking = $b;
        break;
    }
}

// Vérifier si la réservation existe
if ($booking === null) {
    echo "Réservation non trouvée.";
    exit();
}

// Récupérer les éléments de la réservation (les tickets associés)
$bookingItems = $bookingModel->getBookingItems($booking_id);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la réservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Détails de la réservation #<?= htmlspecialchars($booking['booking_id']) ?></h2>

    <p><strong>Statut :</strong> <?= htmlspecialchars($booking['status']) ?></p>
    <p><strong>Date de réservation :</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
    <p><strong>Prix total :</strong> <?= number_format($booking['total_price'], 2) ?>€</p>

    <h4>Tickets associés :</h4>
    <?php if ($bookingItems && $bookingItems->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($item = $bookingItems->fetch_assoc()): ?>
                <div class="list-group-item">
                    <h5>Ticket : <?= htmlspecialchars($item['type']) ?></h5>
                    <p>Quantité : <?= htmlspecialchars($item['quantity']) ?></p>
                    <p>Prix unitaire : <?= number_format($item['price'], 2) ?>€</p>
                    <p><strong>Total : <?= number_format($item['quantity'] * $item['price'], 2) ?>€</strong></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Aucun ticket associé à cette réservation.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-primary">Retour au tableau de bord</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
