<?php
// echo("okay1");
require_once("../includes/db/Database.php");
require_once("../includes/controllers/AuthController.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = new Database();

$conn = $db->connect();
$auth = new AuthController($conn);

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($auth->register($name, $email, $password, $error_message)) {
        header('Location: login.php');
        exit();
    }
}

$db->close();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Formulaire d'inscription</h2>

    <!-- Message d'erreur -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="name" name="name" required maxlength="100">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" required maxlength="100">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required minlength="4" maxlength="20">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">👁</button>
            </div>
            <small class="form-text text-muted">Entre 4 et 20 caractères.</small>
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
        <p class="mt-3">Déjà inscrit ? <a href="login.php">Connexion</a></p>
    </form>
</div>

<!-- JS Bootstrap + Toggle Password -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
