<?php
session_start();
require_once("../includes/db/Database.php");
require_once("../includes/controllers/AuthController.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = new Database();
$conn = $db->connect();
$auth = new AuthController($conn);

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($auth->login($email, $password, $error_message)) {
        if ($_SESSION['is_admin']) {
            header('Location: ../admin/admin.php');
        } else {
            header('Location: index.php');
        }
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Connexion</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" required maxlength="100">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">👁</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
            <p class="mt-3">Pas encore de compte ? <a href="register.php">Inscription</a></p>
        </form>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>

</html>