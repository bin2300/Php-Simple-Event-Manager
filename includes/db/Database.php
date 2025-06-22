<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $dbname = "EventManager";
    private $conn;

    public function connect() {
         
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Erreur de connexion à la base de données: " . $this->conn->connect_error);
        }
        return $this->conn;
        
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
