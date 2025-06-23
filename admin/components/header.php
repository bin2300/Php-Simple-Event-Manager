<?php
// Vérification si l'utilisateur est connecté en tant qu'administrateur
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS local -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #f8f9fa;
            padding-top: 20px;
        }

        .sidebar .nav-link.active {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar border-end">
        <h5 class="text-center">Admin Panel</h5>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link" href="../admin/admin.php">🏠 Tableau de bord</a>
            </li>
                   <li class="nav-item">
                <a class="nav-link" href="../admin/booking.php">  📅 Booking</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#eventSubmenu" role="button" aria-expanded="false" aria-controls="eventSubmenu">
                    📅 Événements
                </a>
                <div class="collapse" id="eventSubmenu">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link" href="../admin/add_event.php">Ajouter</a></li>
                        <li><a class="nav-link" href="../admin/edit_event.php">Modifier</a></li>
                        <li><a class="nav-link" href="../admin/events_list.php">Supprimer</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">

                <ul class="nav flex-column ms-3">
                    <li><a class="nav-link" href="../admin/add_event.php">Ajouter</a></li>
                    <li><a class="nav-link" href="../admin/edit_event.php">Modifier</a></li>
                    <li><a class="nav-link" href="../admin/events_list.php">Supprimer</a></li>
                </ul>

            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/reports.php">📊 Rapports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">Utilisateurs</a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-danger" href="../admin/logout.php">🚪 Se déconnecter</a>
            </li>

        </ul>
    </div>

    <!-- Contenu principal -->
    <div class="content">