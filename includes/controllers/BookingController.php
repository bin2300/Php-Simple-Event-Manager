<?php
require_once __DIR__ . '/../models/Booking.php';

class BookingController {
    private $bookingModel;

    public function __construct($conn) {
        $this->bookingModel = new Booking($conn);
    }

    public function addToCart($user_id, $ticket_id, $quantity) {
        return $this->bookingModel->addToCart($user_id, $ticket_id, $quantity);
    }

    public function getCart($user_id) {
        return $this->bookingModel->getCart($user_id);
    }

    public function removeFromCart($cart_id) {
        return $this->bookingModel->removeFromCart($cart_id);
    }

    public function confirmBooking($user_id) {
        return $this->bookingModel->checkout($user_id);
    }
}
