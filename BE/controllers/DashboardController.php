<?php
class DashboardController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function loadData() {
        require_once __DIR__ . '/../models/dashboard.php';

        try {
            $dashboard = new Dashboard($this->conn);

            $pendapatan = $dashboard->getPendapatanHariIni();
            $produkTerlaris = $dashboard->getProdukTerlaris();
            $stokMenipis = $dashboard->getStokMenipis();
            $jumlahMauHabis = $dashboard->getJumlahBarangMauHabis();

            return array(
                'error' => false,
                'message' => 'load dashboard success',
                'data' => array(
                    'pendapatan_hari_ini' => (float) $pendapatan['total'],
                    'produk_terlaris' => $produkTerlaris,
                    'stok_menipis' => $stokMenipis,
                    'jumlah_barang_mau_habis' => (int) $jumlahMauHabis['jumlah']
                )
            );
        } catch (\PDOException $e) {
            return array(
                'error' => true,
                'message' => 'load dashboard failed',
                'data' => array()
            );
        }
    }
}
?>
