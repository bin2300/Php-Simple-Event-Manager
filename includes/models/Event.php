<?php

class Event
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Ajouter un événement
    public function addEvent($title, $description, $date, $time, $venue, $organizer_id, $image)
    {
        $query = "INSERT INTO events (title, description, date, time, venue, organizer_id, image)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) throw new Exception("Erreur de préparation : " . $this->conn->error);
        $stmt->bind_param("sssssis", $title, $description, $date, $time, $venue, $organizer_id, $image);
        return $stmt->execute();
    }

    // Obtenir tous les événements
    public function getAllEvents()
    {
        $query = "SELECT * FROM events ORDER BY date ASC, time ASC";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) throw new Exception("Erreur de préparation : " . $this->conn->error);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Obtenir un événement par son ID
    public function getEventById($id)
    {
        $query = "SELECT * FROM events WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) throw new Exception("Erreur de préparation : " . $this->conn->error);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Supprimer un événement
    public function deleteEvent($id)
    {
        $query = "DELETE FROM events WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) throw new Exception("Erreur de préparation : " . $this->conn->error);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Mettre à jour un événement
    public function updateEvent($id, $title, $description, $date, $time, $venue, $organizer_id, $image = null)
    {
        if ($image) {
            $query = "UPDATE events SET title = ?, description = ?, date = ?, time = ?, venue = ?, organizer_id = ?, image = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssssssi", $title, $description, $date, $time, $venue, $organizer_id, $image, $id);
        } else {
            $query = "UPDATE events SET title = ?, description = ?, date = ?, time = ?, venue = ?, organizer_id = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssssi", $title, $description, $date, $time, $venue, $organizer_id, $id);
        }

        if (!$stmt) throw new Exception("Erreur de préparation : " . $this->conn->error);
        return $stmt->execute();
    }
}
