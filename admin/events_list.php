<?php require_once('components/header.php'); ?>

<?php
require_once("../includes/admin_only.php"); // Sécuriser la page
require_once("../includes/db/Database.php");
require_once("../includes/models/Event.php");

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();
$eventModel = new Event($conn);

// Récupérer tous les événements
$events = $eventModel->getAllEvents();

// Si un événement doit être supprimé
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    if($eventModel->deleteEvent($event_id)){
        header("Location: events_list.php"); // Rediriger après suppression
        exit();
    }else{
        echo "Erreur lors de la suppression de l'événement.";
    }

}

$db->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des événements</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien vers Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Liste des événements</h2>

        <!-- Message de succès ou erreur -->
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom de l'événement</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Lieu</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['description']) ?></td>
                        <td><?= $event['date'] ?></td>
                        <td><?= $event['time'] ?></td>
                        <td><?= htmlspecialchars($event['venue']) ?></td>
                        <td>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm">Éditer</a>
                            <a href="events_list.php?delete=<?= $event['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Lien vers Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
