<?php
class InventoryController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function loadData() {
        require_once __DIR__ . '/../models/product.php';

        try {
            $produk = new Produk($this->conn);
            $stmt = $produk->getAll();

            return array(
                'error' => false,
                'message' => 'load inventory success',
                'data' => $stmt->fetchAll()
            );
        } catch (\PDOException $e) {
            return array(
                'error' => true,
                'message' => 'load inventory failed',
                'data' => array()
            );
        }
    }

    public function insertData() {
        require_once __DIR__ . '/../models/product.php';

        $nama_produk = isset($_POST['nama_produk']) ? trim($_POST['nama_produk']) : '';
        $harga = isset($_POST['harga']) ? trim($_POST['harga']) : '';
        $stok = isset($_POST['stok']) ? trim($_POST['stok']) : '';

        if ($nama_produk === '' || $harga === '' || $stok === '') {
            return array(
                'error' => true,
                'message' => 'nama_produk, harga, dan stok wajib diisi',
                'data' => $_POST
            );
        }

        $produk = new Produk($this->conn);
        $produk->nama_produk = $nama_produk;
        $produk->harga = $harga;
        $produk->stok = $stok;

        if ($produk->create()) {
            return array(
                'error' => false,
                'message' => 'insert inventory success',
                'data' => array(
                    'nama_produk' => $nama_produk,
                    'harga' => $harga,
                    'stok' => $stok
                )
            );
        }

        return array(
            'error' => true,
            'message' => 'insert inventory failed',
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
