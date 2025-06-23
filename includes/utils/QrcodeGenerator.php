<?php
// includes/utils/QrCodeGenerator.php
require_once __DIR__ . '/../phpqrcode/qrlib.php';

class QrCodeGenerator {
    public static function generate($booking_id, $user_id) {
        $qr_data = "BookingID={$booking_id};UserID={$user_id};Timestamp=" . time();
        $qr_dir = __DIR__ . '/../../public/qr_codes/';
        $qr_filename = "booking_{$booking_id}.png";
        $qr_path = $qr_dir . $qr_filename;

        if (!file_exists($qr_dir)) {
            mkdir($qr_dir, 0755, true);
        }

        QRcode::png($qr_data, $qr_path, QR_ECLEVEL_L, 4);

        if (file_exists($qr_path)) {
            return $qr_path;
        } else {
            throw new Exception("QR code generation failed.");
        }
    }

    public static function getPublicUrl($booking_id) {
        return '/Events_Management/public/qr_codes/booking_' . $booking_id . '.png';
    }
}
