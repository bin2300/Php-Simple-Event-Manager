<?php
require_once('components/header.php');
require_once("../includes/admin_only.php");
require_once("../includes/db/Database.php");
require_once("../includes/models/Event.php");

$db = new Database();
$conn = $db->connect();

$eventModel = new Event($conn);

// Récupérer les statistiques
$total_events = $conn->query("SELECT COUNT(*) AS count FROM events")->fetch_assoc()['count'];
$total_organizers = $conn->query("SELECT COUNT(DISTINCT organizer_id) AS count FROM events")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];

// Récupérer les événements à venir
$today = date('Y-m-d');
$query = "SELECT title, description, date, time, venue FROM events WHERE date >= ? ORDER BY date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

// Recherche d'événements
$search_title = '';
$search_location = '';
if (isset($_POST['search']) || isset($_POST['search_location'])) {
    $search_title = trim($_POST['search']);
    $search_location = trim($_POST['search_location']);
    $query = "SELECT title, description, date, time, venue FROM events WHERE 1=1";
    
    if ($search_title) {
        $query .= " AND (title LIKE ? OR description LIKE ?)";
    }
    
    if ($search_location) {
        $query .= " AND venue LIKE ?";
    }
    
    $query .= " ORDER BY date ASC";
    $stmt = $conn->prepare($query);
    
    if ($search_title && $search_location) {
        $search_param = "%$search_title%";
        $search_location_param = "%$search_location%";
        $stmt->bind_param("sss", $search_param, $search_param, $search_location_param);
    } elseif ($search_title) {
        $search_param = "%$search_title%";
        $stmt->bind_param("ss", $search_param, $search_param);
    } elseif ($search_location) {
        $search_location_param = "%$search_location%";
        $stmt->bind_param("s", $search_location_param);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
}

$db->close();
?>

<div class="container mt-4">
    <h2 class="mb-4">Bienvenue, administrateur</h2>
    <p>Bonjour <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> !</p>

    <div class="row mt-4">
        <!-- Statistiques -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Événements</h5>
                    <p class="card-text fs-3"><?= $total_events ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Organisateurs</h5>
                    <p class="card-text fs-3"><?= $total_organizers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-dark mb-3">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text fs-3"><?= $total_users ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de recherche -->
    <div class="mt-5">
        <h3>Rechercher des événements</h3>
        <form method="POST" action="" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Rechercher par titre, description ou lieu" name="search" value="<?= htmlspecialchars($search_title) ?>">
            </div>
            <div class="input-group mt-2">
                <input type="text" class="form-control" placeholder="Rechercher par lieu" name="search_location" value="<?= htmlspecialchars($search_location) ?>">
            </div>
            <button class="btn btn-primary mt-3" type="submit">Rechercher</button>
        </form>
    </div>

    <!-- Affichage des événements -->
    <div class="mt-5">
        <h3>Événements à venir</h3>

        <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Lieu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['venue']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">Aucun événement trouvé.</p>
        <?php endif; ?>
    </div>

    <a href="add_event.php" class="btn btn-success me-2">Ajouter un événement</a>
    <a href="events_list.php" class="btn btn-info me-2">Lister les événements</a>
    <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
</div>

<?php include("components/footer.php"); ?>
