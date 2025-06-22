<?php
// Démarre la session
session_start();

// Supprime toutes les variables de session
session_unset();

// Détruit complètement la session
session_destroy();

// Redirige vers la page de connexion
header("Location: login.php");
exit();
