<?php

class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProdukTerlaris() {
        $query = "SELECT p.nama_produk, SUM(d.jumlah_keluar) as jumlah_terjual
                  FROM tabel_detail_transaksi d
                  JOIN tabel_produk p ON d.id_produk = p.id_produk
                  GROUP BY p.nama_produk
                  ORDER BY jumlah_terjual DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getStokMenipis() {
        $query = "SELECT nama_produk, stok
                  FROM tabel_produk
                  WHERE stok < 10
                  ORDER BY stok ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getJumlahBarangMauHabis() {
        $query = "SELECT COUNT(*) as jumlah
                  FROM tabel_produk
                  WHERE stok < 10";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch();
    }
}
?>
