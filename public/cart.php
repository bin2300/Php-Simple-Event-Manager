<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Booking.php");
require_once("../includes/models/Ticket.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();
$bookingModel = new Booking($conn);

// Récupérer les articles du panier de l'utilisateur
$user_id = $_SESSION['user_id'];
$cartItems = $bookingModel->getCart($user_id);

$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2><a href="index.php"><-</a><span>____</span>Mon Panier</h2>

    <?php if ($cartItems->num_rows > 0): ?>
        <div class="alert alert-info">
            Vous avez <?= $cartItems->num_rows ?> ticket(s) dans votre panier.
        </div>
        <form method="POST" action="update_cart.php">
            <div class="list-group">
                <?php while ($item = $cartItems->fetch_assoc()): ?>
                    <div class="list-group-item">
                        <h5><?= htmlspecialchars($item['type']) ?> - <?= number_format($item['price'], 2) ?>€</h5>
                        <p>Événement : <?= htmlspecialchars($item['title']) ?></p>
                        <p>Quantité : <input type="number" name="quantity[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width: 100px;"></p>
                        <p>Total pour cet article : <?= number_format($item['price'] * $item['quantity'], 2) ?>€</p>
                        <a href="remove_from_cart.php?cart_id=<?= $item['cart_id'] ?>" class="btn btn-danger">Supprimer</a>
                    </div>
                    <?php $total += $item['price'] * $item['quantity']; ?>
                <?php endwhile; ?>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Mettre à jour le panier</button>
            </div>
        </form>

        <hr>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Total : <?= number_format($total, 2) ?>€</strong></p>
            </div>
            <div class="col-md-6 text-end">
                <form method="POST" action="confirm_booking.php">
                    <button type="submit" class="btn btn-success">Confirmer la réservation</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            Votre panier est vide.
        </div>
        <a href="index.php" class="btn btn-primary">Retour aux événements</a>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
