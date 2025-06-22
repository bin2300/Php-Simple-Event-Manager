<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Ticket.php");
require_once("../includes/models/Booking.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Si le formulaire est soumis (l'ajout d'un ticket au panier)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['quantity'])) {
    $ticket_id = intval($_POST['ticket_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    if ($ticket_id <= 0 || $quantity <= 0) {
        $_SESSION['error'] = "Quantité ou identifiant de ticket invalide.";
        header("Location: event_info.php?id=" . $ticket_id);
        exit();
    }

    $db = new Database();
    $conn = $db->connect();

    $ticketModel = new Ticket($conn);
    $bookingModel = new Booking($conn);

    // Récupérer les détails du ticket
    $ticket = $ticketModel->getTicketById($ticket_id);
    if (!$ticket) {
        $_SESSION['error'] = "Ticket introuvable.";
        header("Location: event_info.php?id=" . $ticket_id);
        exit();
    }

    // Vérifier le stock disponible
    if ($ticket['stock'] < $quantity) {
        $_SESSION['error'] = "Stock insuffisant pour ce ticket.";
        header("Location: event_info.php?id=" . $ticket_id);
        exit();
    }

    // Ajouter le ticket au panier
    if ($bookingModel->addToCart($user_id, $ticket_id, $quantity)) {
        $_SESSION['success'] = "Ticket ajouté au panier.";
        header("Location: cart.php");
        exit();
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout au panier.";
        header("Location: event_info.php?id=" . $ticket_id);
        exit();
    }

    $db->close();
    exit();
}

// Si l'utilisateur n'a pas soumis de formulaire, on affiche les tickets
if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $db = new Database();
    $conn = $db->connect();
    
    $ticketModel = new Ticket($conn);
    
    // Récupérer les tickets pour cet événement
    $tickets = $ticketModel->getTicketsByEvent($event_id);
    
    if ($tickets->num_rows === 0) {
        echo "DEBUG: Aucun ticket trouvé pour l'événement avec l'ID $event_id";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choisir un ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Afficher un message de succès ou d'erreur avec un délai de fermeture
        window.onload = function () {
            <?php if (isset($_SESSION['success'])): ?>
                alert("<?= $_SESSION['success'] ?>");
                <?php unset($_SESSION['success']); ?>
            <?php elseif (isset($_SESSION['error'])): ?>
                alert("<?= $_SESSION['error'] ?>");
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Choisissez votre ticket</h2>

    <!-- Formulaire pour sélectionner les tickets -->
    <form method="POST" action="book.php">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">

        <div class="list-group">
            <?php while ($ticket = $tickets->fetch_assoc()): ?>
                <div class="list-group-item">
                    <h5><?= htmlspecialchars($ticket['type']) ?> - <?= number_format($ticket['price'], 2) ?>€</h5>
                    <p>Stock disponible : <?= $ticket['stock'] ?></p>
                    <div class="form-group">
                        <label for="quantity-<?= $ticket['id'] ?>">Quantité :</label>
                        <input type="number" id="quantity-<?= $ticket['id'] ?>" name="quantity" class="form-control" min="1" max="<?= $ticket['stock'] ?>" value="1" required>
                    </div>
                    <button type="submit" name="ticket_id" value="<?= $ticket['id'] ?>" class="btn btn-primary mt-2">Ajouter au panier</button>
                </div>
            <?php endwhile; ?>
        </div>
    </form>
</div>
</body>
</html>
