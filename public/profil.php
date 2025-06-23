<?php
// Vérifier si la session a déjà été démarrée avant d'appeler session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("../includes/db/Database.php");

function getUserDetails($user_id) {
    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$user = isset($_SESSION['user_id']) ? getUserDetails($_SESSION['user_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission and update user data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Assurez-vous que la validation des données est effectuée avant la mise à jour
    if ($password) {
        // Hash the password before updating
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $password_hash = null;  // No password change
    }

    $db = new Database();
    $conn = $db->connect();
    
    // Mise à jour du profil
    if ($password_hash) {
        $sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $password_hash, $_SESSION['user_id']);
    } else {
        $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $_SESSION['user_id']);
    }

    $stmt->execute();

    // Rediriger vers la page du profil après la mise à jour
    header("Location: profil.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier votre profil</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Footer fixé en bas */
        html, body {
            height: 100%;
        }
        .content {
            min-height: 80vh;
        }
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <div class="col-3">
                    <a href="index.php">
                        <img src="logo.png" alt="Logo" class="logo-img" style="height: 50px;">
                    </a>
                </div>

                <div class="col-6 text-center">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php">Événements</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="booking.php">Réservations</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="help.php">Aide</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <div class="col-3 text-right">
                    <?php if ($user): ?>
                        <span class="navbar-text">Bonjour, <?= htmlspecialchars($user['name']) ?></span><br>
                        <a href="profil.php" class="btn btn-primary btn-sm">Mon Profil</a>
                        <a href="logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-success btn-sm">Se connecter</a>
                        <a href="register.php" class="btn btn-secondary btn-sm">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container my-5 content">
        <h2 class="text-center">Modifier votre profil</h2>
        <?php if ($user): ?>
        <form action="profil.php" method="POST" class="mt-4">
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe (laissez vide si vous ne souhaitez pas le modifier)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Mettre à jour</button>
        </form>
        <?php else: ?>
        <p>Utilisateur non trouvé. Veuillez vous reconnecter.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Votre Événement - Tous droits réservés.</p>
    </footer>

    <!-- Lien vers Bootstrap JS et ses dépendances -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
