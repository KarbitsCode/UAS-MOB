<?php
class InventoryController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function getRequestData() {
        if (!empty($_POST)) {
            return $_POST;
        }

        $rawInput = file_get_contents('php://input');
        if (!$rawInput) {
            return array();
        }

        $jsonData = json_decode($rawInput, true);
        return is_array($jsonData) ? $jsonData : array();
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

        $requestData = $this->getRequestData();

        $nama_produk = isset($requestData['nama_produk']) ? trim($requestData['nama_produk']) : '';
        $harga = isset($requestData['harga']) ? trim((string) $requestData['harga']) : '';
        $stok = isset($requestData['stok']) ? trim((string) $requestData['stok']) : '';

        if ($nama_produk === '' || $harga === '' || $stok === '') {
            return array(
                'error' => true,
                'message' => 'nama_produk, harga, dan stok wajib diisi',
                'data' => $requestData
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
            'data' => $requestData
        );
    }

    public function updateData() {
        require_once __DIR__ . '/../models/product.php';

        $requestData = $this->getRequestData();

        $id_produk = isset($requestData['id_produk']) ? trim((string) $requestData['id_produk']) : '';
        $nama_produk = isset($requestData['nama_produk']) ? trim($requestData['nama_produk']) : '';
        $harga = isset($requestData['harga']) ? trim((string) $requestData['harga']) : '';
        $stok = isset($requestData['stok']) ? trim((string) $requestData['stok']) : '';

        if ($id_produk === '' || $nama_produk === '' || $harga === '' || $stok === '') {
            return array(
                'error' => true,
                'message' => 'id_produk, nama_produk, harga, dan stok wajib diisi',
                'data' => $requestData
            );
        }

        $produk = new Produk($this->conn);
        $produk->id_produk = $id_produk;
        $produk->nama_produk = $nama_produk;
        $produk->harga = $harga;
        $produk->stok = $stok;

        if ($produk->update()) {
            return array(
                'error' => false,
                'message' => 'update inventory success',
                'data' => array(
                    'id_produk' => $id_produk,
                    'nama_produk' => $nama_produk,
                    'harga' => $harga,
                    'stok' => $stok
                )
            );
        }

        return array(
            'error' => true,
            'message' => 'update inventory failed',
            'data' => $requestData
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
