<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Event.php");
require_once("../includes/models/Ticket.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$eventModel = new Event($conn);
$ticketModel = new Ticket($conn);

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($event_id == 0) {
    header("Location: index.php");
    exit();
}

// Récupération des détails de l'événement
$event = $eventModel->getEventById($event_id);
if (!$event) {
    header("Location: index.php");
    exit();
}

// Récupérer les tickets pour cet événement
$tickets = $ticketModel->getTicketsByEvent($event_id);

$db->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .event-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .badge-date {
            font-size: 1rem;
            padding: 0.5em 1em;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2 class="mb-3"><?= htmlspecialchars($event['title']) ?></h2>

        <img src="uploads/<?= htmlspecialchars($event['image']) ?>" alt="Image de l'événement" class="event-img mb-4 shadow">

        <div class="row g-4">
            <!-- Description -->
            <div class="col-md-8">
                <div class="info-box shadow-sm">
                    <h4>Description</h4>
                    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                </div>
            </div>

            <!-- Infos pratiques -->
            <div class="col-md-4">
                <div class="info-box shadow-sm">
                    <h4>Détails</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Date :</strong> <?= htmlspecialchars($event['date']) ?></li>
                        <li class="list-group-item"><strong>Heure :</strong> <?= htmlspecialchars($event['time']) ?></li>
                        <li class="list-group-item"><strong>Lieu :</strong> <?= htmlspecialchars($event['venue']) ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Organisateur -->
        <div class="mt-5 info-box shadow-sm">
            <h4>Organisateur</h4>
            <p>
                Organisé par <span class="badge bg-primary">Utilisateur ID #<?= htmlspecialchars($event['organizer_id']) ?></span>
            </p>
        </div>

        <hr class="my-4">

        <!-- Sélectionner un ticket et réserver -->
        <div class="info-box shadow-sm mt-5">
            <h4>Choisissez un ticket et réservez votre place</h4>

            <!-- Affichage des tickets -->
            <form method="POST" action="book.php">
                <div class="row">
                    <?php while ($ticket = $tickets->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($ticket['type']) ?></h5>
                                    <p>Prix : <?= number_format($ticket['price'], 2) ?>€</p>
                                    <p>Stock disponible : <?= $ticket['stock'] ?></p>
                                    
                                    <div class="form-group">
                                        <label for="quantity-<?= $ticket['id'] ?>">Quantité :</label>
                                        <input type="number" id="quantity-<?= $ticket['id'] ?>" name="quantity" class="form-control" min="1" max="<?= $ticket['stock'] ?>" value="1" required>
                                    </div>
                                    
                                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                    <button type="submit" class="btn btn-outline-primary mt-2">Réserver</button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </form>

            <div class="text-end">
                <form method="POST" action="adhere_event.php">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                    <button type="submit" class="btn btn-success me-2">Oui, je participe !</button>
                    <a href="index.php" class="btn btn-secondary">Retour</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
