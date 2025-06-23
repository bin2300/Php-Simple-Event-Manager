<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/models/Event.php");

// debegage
// var_dump($_SESSION);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();
$eventModel = new Event($conn);

// Récupérer les événements à venir
$today = date('Y-m-d');
$query = "SELECT id, title, description, date, time, venue, image FROM events WHERE date >= ? ORDER BY date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$db->close();
?>

<!DOCTYPE html>
<html lang="fr">
  <?php require_once("../includes/components/headers.php");?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements à venir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
   
    <div class="container mt-5">
        <h2>Événements à venir</h2>

        <?php if ($result->num_rows > 0): ?>
            
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
         
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="Event Image">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($row['description'], 0, 100)) ?>...</p>
                                <p class="text-muted">Date: <?= htmlspecialchars($row['date']) ?> | Heure: <?= htmlspecialchars($row['time']) ?></p>
                                <p class="text-muted">Lieu: <?= htmlspecialchars($row['venue']) ?></p>
                                <a href="event_info.php?id=<?= $row['id'] ?>" class="btn btn-primary">Voir plus</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Aucun événement à venir.</p>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
