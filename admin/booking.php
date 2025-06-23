<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once('components/header.php');
require_once("../includes/db/Database.php");

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Récupération des utilisateurs et événements
$users_result = $conn->query("SELECT id, name FROM users ORDER BY name ASC");
$events_result = $conn->query("SELECT id, title FROM events ORDER BY title ASC");

// Récupération des filtres
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$selected_event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Construction dynamique de la requête
$sql = "
    SELECT 
        b.id AS booking_id,
        u.name AS user_name,
        u.email,
        e.title AS event_title,
        e.date,
        e.venue,
        bi.quantity,
        bi.price,
        (bi.quantity * bi.price) AS total_price
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN booking_items bi ON bi.booking_id = b.id
    INNER JOIN tickets t ON bi.ticket_id = t.id
    INNER JOIN events e ON t.event_id = e.id
";

$conditions = [];
if ($selected_user_id > 0) $conditions[] = "u.id = $selected_user_id";
if ($selected_event_id > 0) $conditions[] = "e.id = $selected_event_id";

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY b.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations - Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-bottom: 80px; }
        footer {
            background-color: #343a40; color: #fff;
            text-align: center; padding: 10px 0;
            position: fixed; bottom: 0; width: 100%;
        }
        .table thead th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2 class="mb-4 text-center">📊 Réservations des Utilisateurs</h2>

        <!-- FILTRES -->
        <form method="GET" class="row mb-4 g-3">
            <div class="col-md-4">
                <label for="user_id">Filtrer par utilisateur :</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="0">-- Tous les utilisateurs --</option>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>" <?= ($user['id'] == $selected_user_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="event_id">Filtrer par événement :</label>
                <select name="event_id" id="event_id" class="form-control">
                    <option value="0">-- Tous les événements --</option>
                    <?php while ($event = $events_result->fetch_assoc()): ?>
                        <option value="<?= $event['id'] ?>" <?= ($event['id'] == $selected_event_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-block">🔍 Appliquer les filtres</button>
            </div>
        </form>

        <!-- TABLEAU DES RÉSERVATIONS -->
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Événement</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Qté</th>
                            <th>Prix (€)</th>
                            <th>Total (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['booking_id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['event_title']) ?></td>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['venue'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= number_format($row['price'], 2) ?></td>
                            <td><?= number_format($row['total_price'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">Aucune réservation trouvée.</div>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?= date('Y') ?> Gestion des Réservations - Admin
    </footer>
</body>
</html>
