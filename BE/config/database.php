<?php
class Database {
    private $server = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "mhs";
    private $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->server, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die(json_encode(array(
                'error' => true,
                'message' => 'Connection failed: ' . $this->conn->connect_error
            )));
        }
        return $this->conn;
    }
}
?>