<?php
class PesananController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function loadData() {
        return array(
            'error' => false,
            'message' => 'load pesanan success',
            'data' => array()
        );
    }

    public function insertData() {
        return array(
            'error' => false,
            'message' => 'insert pesanan success',
            'data' => $_POST
        );
    }

    public function updateData() {
        return array(
            'error' => false,
            'message' => 'update pesanan success',
            'data' => $_POST
        );
    }

    public function deleteData() {
        return array(
            'error' => false,
            'message' => 'delete pesanan success',
            'data' => $_POST
        );
    }
}
?>
