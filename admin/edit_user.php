<?php
require_once('../includes/admin_only.php');
require_once('../includes/db/Database.php');
require_once('../includes/controllers/UserController.php');

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérification si l'utilisateur existe
$db = new Database();
$conn = $db->connect();
$controller = new UserController($conn);
$user = $controller->userModel->getById($user_id);

if (!$user) {
    header("Location: users.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    // Validation de l'email
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } else {
        if ($controller->userModel->updateInfo($user_id, $new_name, $new_email)) {
            $message = "Informations mises à jour avec succès.";
            $user['name'] = $new_name;
            $user['email'] = $new_email;
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    }
}

require_once('components/header.php');
?>

<div class="container mt-5">
    <h2>Modifier un utilisateur</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="users.php" class="btn btn-secondary">Retour</a>
    </form>
</div>

<?php $db->close(); ?>
