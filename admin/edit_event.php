<?php require_once('components/header.php'); ?>

<?php
require_once("../includes/admin_only.php"); // Sécuriser la page
require_once("../includes/db/Database.php");
require_once("../includes/models/Event.php");

$db = new Database();
$conn = $db->connect();
$eventModel = new Event($conn);

// Message d'erreur ou succès
$message = "";
$error = "";

// Vérifier si un ID d'événement est passé dans l'URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Récupérer les détails de l'événement
    $query = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        $error = "Événement non trouvé.";
    }
} else {
    $error = "ID d'événement manquant.";
}

// Si le formulaire est soumis pour mettre à jour l'événement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($event)) {
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $date        = $_POST['date'];
    $time        = $_POST['time'];
    $venue       = $_POST['venue'];
    $price       = floatval($_POST['price']);
    
    // Gérer l'image
    $imageName = $event['image']; // Conserver l'image actuelle si aucune nouvelle image n'est téléchargée
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../public/uploads/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    // Mettre à jour les informations de l'événement
    if ($eventModel->updateEvent($event_id, $title, $description, $date, $time, $venue, 1, $imageName)) {
        $message = "L'événement a été mis à jour avec succès.";
    } else {
        $error = "Erreur lors de la mise à jour de l'événement.";
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'événement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien vers Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier l'événement</h2>

        <!-- Message d'erreur ou de succès -->
        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Formulaire de modification de l'événement -->
        <?php if (isset($event)): ?>
            <form method="POST" action="edit_event.php?id=<?= $event['id'] ?>" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Nom de l'événement</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= $event['date'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Heure</label>
                    <input type="time" class="form-control" id="time" name="time" value="<?= $event['time'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="venue" class="form-label">Lieu</label>
                    <input type="text" class="form-control" id="venue" name="venue" value="<?= htmlspecialchars($event['venue']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image de l'événement</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <small class="form-text text-muted">Laissez vide si vous ne voulez pas changer l'image actuelle.</small>
                    <?php if ($event['image']): ?>
                        <img src="../public/uploads/<?= $event['image'] ?>" alt="Image actuelle" class="img-thumbnail mt-3" width="150">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour l'événement</button>
            </form>
        <?php endif; ?>

        <a href="events_list.php" class="btn btn-secondary mt-3">Retour à la liste des événements</a>
    </div>

    <!-- Lien vers Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
