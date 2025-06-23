<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("../includes/db/Database.php");
require_once("../includes/models/Booking.php");
require_once('../includes/phpqrcode/qrlib.php'); // Bibliothèque QR Code

// Mode de débogage
$debug = true; // Passe à false si tu veux le comportement standard pour <img src="...">

if ($debug) echo "📍 Script lancé<br>";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    if ($debug) echo "❌ Erreur : utilisateur non connecté ou booking_id manquant<br>";
    header("Location: login.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

if ($debug) echo "✔️ Utilisateur connecté : user_id = $user_id, booking_id = $booking_id<br>";

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    if ($debug) echo "❌ Connexion échouée<br>";
    exit();
}
if ($debug) echo "✔️ Connexion DB établie<br>";

// Charger le modèle Booking
$bookingModel = new Booking($conn);

// Vérifier que la réservation existe bien pour cet utilisateur
$bookingDetails = $bookingModel->getBookingDetails($booking_id);

if ($bookingDetails->num_rows > 0) {
    if ($debug) echo "✔️ Réservation trouvée<br>";

    // Créer un texte unique pour le QR code
    $qr_data = "BookingID={$booking_id};UserID={$user_id};Timestamp=" . time();
    if ($debug) echo "📦 Données QR : $qr_data<br>";

    // Dossier et nom de fichier pour le QR
    $qr_dir = __DIR__ . '/../public/qr_codes/';
    $qr_filename = 'booking_' . $booking_id . '.png';
    $qr_file = $qr_dir . $qr_filename;

    // S'assurer que le dossier existe
    if (!file_exists($qr_dir)) {
        mkdir($qr_dir, 0755, true);
        if ($debug) echo "📁 Dossier 'qr_codes/' créé<br>";
    } else {
        if ($debug) echo "📁 Dossier 'qr_codes/' déjà présent<br>";
    }

    // Générer le QR code PNG
    QRcode::png($qr_data, $qr_file, QR_ECLEVEL_L, 4);
    if ($debug) echo "✅ QR Code généré dans : $qr_file<br>";

    // Affichage direct du PNG
    if (file_exists($qr_file)) {
        if ($debug) echo "📷 Fichier QR Code trouvé. Affichage...<br>";

        // Si on est en mode debug, ne pas envoyer d'image mais message
        if (!$debug) {
            header('Content-Type: image/png');
            readfile($qr_file);
        } else {
            echo "<img src='../public/qr_codes/$qr_filename' alt='QR Code'>";
        }
    } else {
        echo "❌ Fichier QR non trouvé après génération<br>";
    }
} else {
    echo "❌ Erreur : réservation non trouvée dans la base<br>";
}
