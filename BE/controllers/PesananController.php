<?php
class PesananController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function getRequestData()
    {
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

    public function loadData()
    {
        require_once __DIR__ . '/../models/transaksi.php';

        try {
            $transaksi = new Transaksi($this->conn);

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

    public function insertData()
    {
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

    public function updateData()
    {
        require_once __DIR__ . '/../models/transaksi.php';
        require_once __DIR__ . '/../models/keuangan.php';

        $requestData = $this->getRequestData();
        $id_transaksi = isset($requestData['id_transaksi']) ? trim((string) $requestData['id_transaksi']) : '';

        if ($id_transaksi === '') {
            return array(
                'error' => true,
                'message' => 'id_transaksi wajib diisi',
                'data' => $requestData
            );
        }

        try {
            $transaksi = new Transaksi($this->conn);
            $stmt = $transaksi->getDetailNota($id_transaksi);
            $transactionData = $stmt->fetch();

            if (!$transactionData) {
                return array(
                    'error' => true,
                    'message' => 'pesanan tidak ditemukan',
                    'data' => $requestData
                );
            }

            if ($transactionData['status_pesanan'] !== 'Menunggu') {
                return array(
                    'error' => true,
                    'message' => 'status pesanan sudah ' . $transactionData['status_pesanan'],
                    'data' => $requestData
                );
            }

            if ((int) $transactionData['stok'] < (int) $transactionData['jumlah_keluar']) {
                return array(
                    'error' => true,
                    'message' => 'stok produk tidak cukup',
                    'data' => $requestData
                );
            }


            if (!$transaksi->updateStatus($id_transaksi, 'Selesai')) {
                return array(
                    'error' => true,
                    'message' => 'gagal mengubah status pesanan',
                    'data' => $requestData
                );
            }

            if (!$transaksi->reduceStok($transactionData['id_produk'], $transactionData['jumlah_keluar'])) {
                return array(
                    'error' => true,
                    'message' => 'stok produk tidak cukup',
                    'data' => $requestData
                );
            }

            $keuangan = new Keuangan($this->conn);
            $keuangan->id_transaksi = $transactionData['id_transaksi'];
            $keuangan->jenis = 'Pemasukan';
            $keuangan->nominal = $transactionData['total_harga'];
            $keuangan->keterangan = $transactionData['keterangan'];
            $keuangan->catatKeuangan();
            return array(
                'error' => false,
                'message' => 'update pesanan success',
                'data' => array(
                    'id_transaksi' => $transactionData['id_transaksi'],
                    'no_nota' => $transactionData['no_nota'],
                    'status_pesanan' => 'Selesai',
                    'id_produk' => $transactionData['id_produk'],
                    'nama_produk' => $transactionData['nama_produk'],
                    'jumlah' => (int) $transactionData['jumlah_keluar'],
                    'stok_tersisa' => (int) $transactionData['stok'] - (int) $transactionData['jumlah_keluar'],
                    'total_harga' => $transactionData['total_harga'],
                    'keterangan' => $transactionData['keterangan']
                )
            );
        } catch (\PDOException $e) {
            error_log('Error Complete Transaksi: ' . $e->getMessage());

            return array(
                'error' => true,
                'message' => 'update pesanan failed',
                'data' => $requestData
            );
        }
    }
}
?>
