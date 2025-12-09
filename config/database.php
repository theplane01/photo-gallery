<?php
// database.php - HAPUS session_start() dari sini
// JANGAN ada session_start() di sini karena sudah dipanggil di file lain

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "gallery";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset
        $this->conn->set_charset("utf8mb4");
    }

    public function escape_string($string) {
        return $this->conn->real_escape_string($string);
    }
}

$db = new Database();
// JANGAN ada spasi atau karakter setelah ?>