<?php
class User
{
    private $conn;

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    public function emailExists($email)
    {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function register($name, $email, $password)
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $name, $email, $hashed);
        return $stmt->execute();
    }

    public function authenticate($email, $password)
    {
        $query = "SELECT id, name, password, is_admin FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function getAll()
    {
        $query = "SELECT id, name, email, is_admin, created_at FROM users ORDER BY created_at DESC";
        return $this->conn->query($query);
    }

    public function delete($id)
    {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function toggleAdmin($id, $new_status)
    {
        $query = "UPDATE users SET is_admin = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $new_status, $id);
        return $stmt->execute();
    }

    public function getById($id)
    {
        $query = "SELECT id, name, email, is_admin FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateInfo($id, $name, $email)
    {
        $query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $name, $email, $id);
        return $stmt->execute();
    }
}
