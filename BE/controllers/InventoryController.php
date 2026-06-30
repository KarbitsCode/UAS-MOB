<?php
class InventoryController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function loadData() {
        return array(
            'error' => false,
            'message' => 'load inventory success',
            'data' => array()
        );
    }

    public function insertData() {
        return array(
            'error' => false,
            'message' => 'insert inventory success',
            'data' => $_POST
        );
    }

    public function updateData() {
        return array(
            'error' => false,
            'message' => 'update inventory success',
            'data' => $_POST
        );
    }

    public function deleteData() {
        return array(
            'error' => false,
            'message' => 'delete inventory success',
            'data' => $_POST
        );
    }
}
?>
