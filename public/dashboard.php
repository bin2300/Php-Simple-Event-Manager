<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Booking.php");
require_once("../includes/models/Ticket.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->connect();
$bookingModel = new Booking($conn);

// Récupérer les réservations de l'utilisateur, incluant les tickets associés
$bookings = $bookingModel->getUserBookings($user_id);

?>
<?php require_once("../includes/components/headers.php");?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tableau de bord de l'utilisateur</h2>

    <h4>Réservations passées et à venir</h4>

    <?php if ($bookings && $bookings->num_rows > 0): ?>
        <div class="list-group">
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="list-group-item">
                    <!-- Affichage des informations disponibles dans la réservation -->
                    <h5>Réservation #<?= htmlspecialchars($booking['booking_id']) ?></h5>
                    <p>Statut : <?= htmlspecialchars($booking['status']) ?></p>
                    <p>Prix total : <?= number_format($booking['total_price'], 2) ?>€</p>
                    <p>Date de réservation : <?= htmlspecialchars($booking['booking_date']) ?></p>

                    <!-- Affichage des détails des tickets associés à cette réservation -->
                    <?php
                    $bookingItems = $bookingModel->getBookingItems($booking['booking_id']);
                    if ($bookingItems && $bookingItems->num_rows > 0): ?>
                        <ul>
                            <?php while ($item = $bookingItems->fetch_assoc()): ?>
                                <li>
                                    Ticket: <?= htmlspecialchars($item['type']) ?> - 
                                    Quantité: <?= htmlspecialchars($item['quantity']) ?> - 
                                    Prix unitaire: <?= number_format($item['price'], 2) ?>€ - 
                                    Total: <?= number_format($item['price'] * $item['quantity'], 2) ?>€
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Aucun ticket associé à cette réservation.</p>
                    <?php endif; ?>

                    <a href="view_booking.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-primary">Voir les détails</a>
                    <a href="download_ticket.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-success">Télécharger le billet</a>
                    <a href="generate_qr.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-info">Générer QR Code</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Aucune réservation trouvée.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
