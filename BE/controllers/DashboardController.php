<?php
class DashboardController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function loadData() {
        return array(
            'error' => false,
            'message' => 'load dashboard success',
            'data' => array()
        );
    }

    public function insertData() {
        return array(
            'error' => false,
            'message' => 'insert dashboard success',
            'data' => $_POST
        );
    }

    public function updateData() {
        return array(
            'error' => false,
            'message' => 'update dashboard success',
            'data' => $_POST
        );
    }

    public function deleteData() {
        return array(
            'error' => false,
            'message' => 'delete dashboard success',
            'data' => $_POST
        );
    }
}
?>
