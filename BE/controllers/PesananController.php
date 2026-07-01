<?php
class PesananController {
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
        require_once __DIR__ . '/../models/transaksi.php';

        $requestData = $this->getRequestData();
        $id_transaksi = isset($requestData['id_transaksi']) ? trim((string) $requestData['id_transaksi']) : '';

        try {
            $transaksi = new Transaksi($this->conn);

            if ($id_transaksi !== '') {
                $stmt = $transaksi->getDetailNota($id_transaksi);
                $data = $stmt->fetchAll();

                if (empty($data)) {
                    return array(
                        'error' => true,
                        'message' => 'detail pesanan tidak ditemukan',
                        'data' => array()
                    );
                }

                return array(
                    'error' => false,
                    'message' => 'load detail pesanan success',
                    'data' => $data
                );
            }

            $stmt = $transaksi->getAll();

            return array(
                'error' => false,
                'message' => 'load pesanan success',
                'data' => $stmt->fetchAll()
            );
        } catch (\PDOException $e) {
            return array(
                'error' => true,
                'message' => 'load pesanan failed',
                'data' => array()
            );
        }
    }

    public function insertData() {
        require_once __DIR__ . '/../models/transaksi.php';
        require_once __DIR__ . '/../models/product.php';

        $requestData = $this->getRequestData();

        $no_nota = isset($requestData['no_nota']) ? trim($requestData['no_nota']) : '';
        $metode_pembayaran = isset($requestData['metode_pembayaran']) ? trim($requestData['metode_pembayaran']) : '';
        $keterangan = isset($requestData['keterangan']) ? trim($requestData['keterangan']) : '';
        $id_produk = isset($requestData['id_produk']) ? trim((string) $requestData['id_produk']) : '';
        $jumlah = isset($requestData['jumlah']) ? trim((string) $requestData['jumlah']) : '';

        if ($no_nota === '' || $metode_pembayaran === '' || $id_produk === '' || $jumlah === '') {
            return array(
                'error' => true,
                'message' => 'no_nota, metode_pembayaran, id_produk, dan jumlah wajib diisi',
                'data' => $requestData
            );
        }

        if (!is_numeric($jumlah) || (int) $jumlah <= 0) {
            return array(
                'error' => true,
                'message' => 'jumlah harus berupa angka lebih dari 0',
                'data' => $requestData
            );
        }

        $produk = new Produk($this->conn);
        $productData = $produk->getById($id_produk);

        if (!$productData) {
            return array(
                'error' => true,
                'message' => 'produk tidak ditemukan',
                'data' => $requestData
            );
        }

        $harga = (float) $productData['harga'];
        $jumlahInt = (int) $jumlah;
        $subtotal = $harga * $jumlahInt;

        $transaksi = new Transaksi($this->conn);
        $transaksi->no_nota = $no_nota;
        $transaksi->total_harga = $subtotal;
        $transaksi->metode_pembayaran = $metode_pembayaran;
        $transaksi->status_pesanan = 'Menunggu';
        $transaksi->keterangan = $keterangan;

        $detail_items = array(
            array(
                'id_produk' => $productData['id_produk'],
                'jumlah_keluar' => $jumlahInt,
                'subtotal' => $subtotal
            )
        );

        if ($transaksi->createTransaction($detail_items)) {
            return array(
                'error' => false,
                'message' => 'insert pesanan success',
                'data' => array(
                    'no_nota' => $no_nota,
                    'metode_pembayaran' => $metode_pembayaran,
                    'status_pesanan' => 'Menunggu',
                    'keterangan' => $keterangan,
                    'nama_produk' => $productData['nama_produk'],
                    'harga' => $harga,
                    'jumlah' => $jumlahInt,
                    'total_harga' => $subtotal
                )
            );
        }

        return array(
            'error' => true,
            'message' => 'insert pesanan failed',
            'data' => $requestData
        );
    }

    public function updateData() {
        require_once __DIR__ . '/../models/transaksi.php';

        $requestData = $this->getRequestData();
        $id_transaksi = isset($requestData['id_transaksi']) ? trim((string) $requestData['id_transaksi']) : '';

        if ($id_transaksi === '') {
            return array(
                'error' => true,
                'message' => 'id_transaksi wajib diisi',
                'data' => $requestData
            );
        }

        $transaksi = new Transaksi($this->conn);
        $transaksi->id_transaksi = $id_transaksi;

        $result = $transaksi->completeTransaction();

        if ($result['success']) {
            return array(
                'error' => false,
                'message' => $result['message'],
                'data' => $result['data']
            );
        }

        return array(
            'error' => true,
            'message' => $result['message'],
            'data' => $requestData
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
