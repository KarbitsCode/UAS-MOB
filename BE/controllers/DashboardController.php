<?php
class DashboardController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function loadData()
    {
        require_once __DIR__ . '/../models/dashboard.php';
        require_once __DIR__ . '/../models/keuangan.php';

        try {
            $dashboard = new Dashboard($this->conn);
            $keuangan = new Keuangan($this->conn);

            $pendapatan = $keuangan->getRingkasan()->fetchAll();
            $produkTerlaris = $dashboard->getProdukTerlaris();
            $stokMenipis = $dashboard->getStokMenipis();
            $jumlahMauHabis = $dashboard->getJumlahBarangMauHabis();

            return array(
                'error' => false,
                'message' => 'load dashboard success',
                'data' => array(
                    'pendapatan_hari_ini' => $pendapatan[0]['total'],
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
