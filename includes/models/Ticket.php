<?php

class Ticket
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Récupérer les tickets pour un événement donné
    public function getTicketsByEvent($event_id)
    {
        $query = "SELECT id, type, price, stock FROM tickets WHERE event_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Récupérer un ticket par son ID
    public function getTicketById($ticket_id)
    {
        $query = "SELECT id, event_id, type, price, stock FROM tickets WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Mettre à jour le stock après une réservation
    public function decreaseStock($ticket_id, $quantity)
    {
        $query = "UPDATE tickets SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $quantity, $ticket_id, $quantity);
        return $stmt->execute();
    }

    // Ajouter un nouveau ticket
    public function createTicket($event_id, $type, $price, $stock)
    {
        $query = "INSERT INTO tickets (event_id, type, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isdi", $event_id, $type, $price, $stock);
        return $stmt->execute();
    }

    // Supprimer un ticket
    public function deleteTicket($ticket_id)
    {
        $query = "DELETE FROM tickets WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $ticket_id);
        return $stmt->execute();
    }

    // Mettre à jour un ticket
    public function updateTicket($id, $type, $price, $stock)
    {
        $query = "UPDATE tickets SET type = ?, price = ?, stock = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sdii", $type, $price, $stock, $id);
        return $stmt->execute();
    }
}
