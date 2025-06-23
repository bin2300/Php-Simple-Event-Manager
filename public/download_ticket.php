<?php
session_start();
require_once("../includes/db/Database.php");
require_once('../includes/fpdf/fpdf.php');
require_once('../includes/utils/QrcodeGenerator.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->connect();

if (!$conn) exit("Erreur de connexion à la base de données.");

$sql_user = "SELECT name, email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

if (!$user) exit("Utilisateur non trouvé.");

$sql_booking_items = "SELECT ticket_id, quantity FROM booking_items WHERE booking_id = ?";
$stmt_booking_items = $conn->prepare($sql_booking_items);
$stmt_booking_items->bind_param("i", $booking_id);
$stmt_booking_items->execute();
$bookingItems = $stmt_booking_items->get_result();

if ($bookingItems->num_rows === 0) exit("Aucun ticket trouvé pour cette réservation.");

function generateGlobalTicket($user, $booking_id, $conn, $bookingItems, $user_id) {
    $pdf = new FPDF();
    $pdf->AddPage();

    // En-tête
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(0, 10, 'Votre Billet de Réservation', 0, 1, 'C');

    // QR Code
    $qr_path = QrCodeGenerator::generate($booking_id, $user_id);
    if (file_exists($qr_path)) {
        $pdf->Image($qr_path, 160, 10, 35);
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, 'Nom : ' . $user['name'], 0, 1);
    $pdf->Cell(0, 8, 'Email : ' . $user['email'], 0, 1);
    $pdf->Ln(10);

    // Tableau des tickets
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(80, 10, 'Type de Ticket', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Quantité', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Prix unitaire', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Total', 1, 1, 'C', true);

    $total_price = 0;
    $pdf->SetFont('Arial', '', 12);
    $bookingItems->data_seek(0);
    while ($item = $bookingItems->fetch_assoc()) {
        $ticket_id = $item['ticket_id'];
        $quantity = $item['quantity'];

        $sql_ticket_info = "SELECT type, price FROM tickets WHERE id = ?";
        $stmt_ticket_info = $conn->prepare($sql_ticket_info);
        $stmt_ticket_info->bind_param("i", $ticket_id);
        $stmt_ticket_info->execute();
        $ticket_info_result = $stmt_ticket_info->get_result();
        $ticket_info = $ticket_info_result->fetch_assoc();

        $line_total = $ticket_info['price'] * $quantity;
        $total_price += $line_total;

        $pdf->Cell(80, 10, $ticket_info['type'], 1);
        $pdf->Cell(30, 10, $quantity, 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($ticket_info['price'], 2) . '€', 1, 0, 'R');
        $pdf->Cell(40, 10, number_format($line_total, 2) . '€', 1, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(150, 12, 'Prix total', 1);
    $pdf->Cell(40, 12, number_format($total_price, 2) . '€', 1, 1, 'R');

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(0, 10, 'Merci pour votre réservation. Veuillez présenter ce billet à l\'entrée.', 0, 1, 'C');

    // Génération du PDF
    $file_name = 'ticket_global_' . $booking_id . '.pdf';
    $pdf->Output('D', $file_name);
}

generateGlobalTicket($user, $booking_id, $conn, $bookingItems, $user_id);
exit();
