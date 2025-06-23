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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Ticket</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Général */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Header */
header {
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 10px 20px;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo img {
    height: 50px;
}

/* Navigation */
.nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.nav ul li {
    margin-right: 20px;
}

.nav ul li a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

.nav ul li a:hover {
    color: #007bff;
}

/* Section utilisateur */
.user-info {
    display: flex;
    align-items: center; /* Aligner les éléments horizontalement */
}

.user-name {
    font-weight: bold;
    margin-right: 15px; /* Ajouter un espace entre le nom et les boutons */
}

.btn {
    background-color: #007bff;
    color: white;
    padding: 8px 16px;
    margin-left: 10px; /* Ajouter un espace entre les boutons */
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.btn-danger {
    background-color: #dc3545;
}

.btn:hover {
    opacity: 0.9;
}

/* QR Code */
.qr-code {
    margin-left: 10px; /* Espace entre le QR Code et les boutons */
}

.qr-image {
    width: 50px;
    height: 50px;
}

/* Responsiveness */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        align-items: center;
    }

    .nav ul {
        flex-direction: column;
        margin-top: 10px;
    }

    .nav ul li {
        margin-bottom: 10px;
    }
}

    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="logo.png" alt="Logo" class="logo-img">
                </a>
            </div>

            <nav class="nav">
                <ul>
                    <li><a href="index.php">Événements</a></li>
                    <li><a href="dashboard.php">My_booking</a></li>
                    <li><a href="cart.php">cart</a></li>
                </ul>
            </nav>

            <div class="user-info">
                <?php if ($user): ?>

                    <a href="profil.php" class="btn">Mon Profil</a>
                    <a href="logout.php" class="btn btn-danger">Déconnexion</a>

                <?php else: ?>
                    <a href="login.php" class="btn">Se connecter</a>
                    <a href="register.php" class="btn">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
</body>
</html>
