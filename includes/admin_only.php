<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../public/index.php'); // ou message d'erreur
    exit();
}
