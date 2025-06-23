<?php

class Booking
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addToCart($user_id, $ticket_id, $quantity)
    {
        $stmt = $this->conn->prepare("INSERT INTO cart (user_id, ticket_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
        $stmt->bind_param("iii", $user_id, $ticket_id, $quantity);
        return $stmt->execute();
    }

    public function getCart($user_id)
    {
        $stmt = $this->conn->prepare("SELECT cart.id AS cart_id, cart.ticket_id, tickets.type, tickets.price, events.title, cart.quantity
            FROM cart
            JOIN tickets ON cart.ticket_id = tickets.id
            JOIN events ON tickets.event_id = events.id
            WHERE cart.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function removeFromCart($cart_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }

    public function checkout($user_id)
    {
        // Obtenir les tickets dans le panier
        $items = $this->getCart($user_id);
        if ($items->num_rows == 0) return false;

        $this->conn->begin_transaction();

        try {
            // Calcul du total
            $total = 0;
            $item_list = [];
            while ($item = $items->fetch_assoc()) {
                $total += $item['price'] * $item['quantity'];
                $item_list[] = $item;
            }

            // Créer la réservation
            $stmt = $this->conn->prepare("INSERT INTO bookings (user_id, total_price) VALUES (?, ?)");
            $stmt->bind_param("id", $user_id, $total);
            $stmt->execute();
            $booking_id = $stmt->insert_id;

            // Ajouter les tickets à booking_items
            foreach ($item_list as $item) {
                $ticket_id = $item['ticket_id'];
                $qty = $item['quantity'];
                $price = $item['price'];

                $stmt = $this->conn->prepare("INSERT INTO booking_items (booking_id, ticket_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $booking_id, $ticket_id, $qty, $price);
                $stmt->execute();

                // Décrémenter le stock
                $updateStock = $this->conn->prepare("UPDATE tickets SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $updateStock->bind_param("iii", $qty, $ticket_id, $qty);
                $updateStock->execute();
            }

            // Simuler un paiement
            $stmt = $this->conn->prepare("INSERT INTO payments (booking_id, method) VALUES (?, 'simulated')");
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();

            // Vider le panier
            $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    // Récupérer les réservations passées et futures de l'utilisateur
    public function getUserBookings($user_id)
    {
        // Suppression de la jointure avec la table 'events'
        $query = "SELECT b.id AS booking_id, b.total_price, b.booking_date, b.status
              FROM bookings b
              WHERE b.user_id = ?
              ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier s'il y a des résultats
        if ($result->num_rows > 0) {
            return $result;
        } else {
            return false;  // Aucun booking trouvé
        }
    }


    // Récupérer les détails d'une réservation
    public function getBookingDetails($booking_id)
    {
        $query = "SELECT * FROM booking_items WHERE booking_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        return $stmt->get_result();
    }




    // Récupérer les éléments de la réservation (les tickets associés)
    public function getBookingItems($booking_id)
    {
        $query = "SELECT bi.ticket_id, bi.quantity, bi.price, t.type
                  FROM booking_items bi
                  JOIN tickets t ON bi.ticket_id = t.id
                  WHERE bi.booking_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
