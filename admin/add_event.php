<?php require_once('components/header.php'); ?>

<?php
require_once("../includes/admin_only.php");
require_once("../includes/db/Database.php");
require_once("../includes/models/Event.php");
require_once("../includes/models/Ticket.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = new Database();
$conn = $db->connect();

$eventModel = new Event($conn);
$ticketModel = new Ticket($conn);

$message = "";
$error = "";

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $date        = $_POST['date'];
    $time        = $_POST['time'];
    $venue       = $_POST['venue'];
    $price       = floatval($_POST['price']);
    $organizer_id = 1; // à adapter si multi-organisateurs

    // Gérer l'image
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../public/uploads/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    // Ajouter l'événement
    if ($eventModel->addEvent($title, $description, $date, $time, $venue, $organizer_id, $imageName)) {
        $event_id = $conn->insert_id; // Récupérer l'ID de l'événement ajouté
        $message = "Événement ajouté avec succès.";

        // Ajouter les tickets associés à l'événement
        if (isset($_POST['ticket_type'])) {
            $ticket_types = $_POST['ticket_type'];
            $ticket_prices = $_POST['ticket_price'];
            $ticket_stocks = $_POST['ticket_stock'];

            foreach ($ticket_types as $index => $ticket_type) {
                $price = floatval($ticket_prices[$index]);
                $stock = intval($ticket_stocks[$index]);
                if (!$ticketModel->createTicket($event_id, $ticket_type, $price, $stock)) {
                    $error = "Erreur lors de l'ajout du ticket : $ticket_type";
                    break;
                }
            }

            if (!$error) {
                $message .= " Tickets ajoutés avec succès.";
            }
        }
    } else {
        $error = "Erreur lors de l'ajout de l'événement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un événement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Ajouter un événement</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="add_event.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Heure</label>
            <input type="time" name="time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Lieu</label>
            <input type="text" name="venue" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix du ticket (par défaut)</label>
            <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image de l'événement</label>
            <input type="file" name="image" class="form-control">
        </div>

        <!-- Ajouter des types de tickets -->
        <h5>Ajouter des tickets</h5>
        <div id="ticket-fields">
            <div class="ticket-field">
                <div class="mb-3">
                    <label class="form-label">Type de ticket</label>
                    <input type="text" name="ticket_type[]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prix du ticket</label>
                    <input type="number" name="ticket_price[]" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock de tickets</label>
                    <input type="number" name="ticket_stock[]" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-info" onclick="addTicketField()">Ajouter un autre ticket</button>
        <br><br>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="admin.php" class="btn btn-secondary">Retour</a>
    </form>
</div>

<script>
    function addTicketField() {
        const ticketFields = document.getElementById('ticket-fields');
        const newTicketField = document.createElement('div');
        newTicketField.classList.add('ticket-field');
        newTicketField.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Type de ticket</label>
                <input type="text" name="ticket_type[]" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prix du ticket</label>
                <input type="number" name="ticket_price[]" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock de tickets</label>
                <input type="number" name="ticket_stock[]" class="form-control" required>
            </div>
        `;
        ticketFields.appendChild(newTicketField);
    }
</script>

</body>
</html>
