<?php
require_once('../includes/admin_only.php');
require_once('../includes/db/Database.php');
require_once('../includes/controllers/UserController.php');
require_once('components/header.php');

$db = new Database();
$conn = $db->connect();
$controller = new UserController($conn);

// Récupération des utilisateurs
$users = $controller->getAllUsers();
?>

<div class="container mt-4">
    <h2>Gestion des utilisateurs</h2>

    <table class="table table-bordered table-hover mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['is_admin'] ? 'Oui' : 'Non' ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <?php if ($_SESSION['user_id'] != $row['id']): ?>
                            <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger me-1" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>

                            <?php if ($row['is_admin']): ?>
                                <a href="toggle_admin.php?id=<?= $row['id'] ?>&admin=0" class="btn btn-sm btn-secondary">Retirer Admin</a>
                            <?php else: ?>
                                <a href="toggle_admin.php?id=<?= $row['id'] ?>&admin=1" class="btn btn-sm btn-warning">Promouvoir Admin</a>
                            <?php endif; ?>

                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Modifier</a>
                        <?php else: ?>
                            <span class="text-muted">Moi</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php $db->close(); ?>
