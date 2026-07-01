<?php

class Transaksi {
    private $conn;
    //table
    private $table_transaksi = "tabel_transaksi";
    private $table_detail = "tabel_detail_transaksi";
    public $id_transaksi;
    public $no_nota;
    public $total_harga;
    public $metode_pembayaran;
    public $status_pesanan;
    public $keterangan;

    public function __construct($db) {
        $this->conn = $db;
    }

  //create
    public function createTransaction($detail_items) {
        try {
            $this->conn->beginTransaction();
            $query_transaksi = "INSERT INTO " . $this->table_transaksi . "
                               (no_nota, total_harga, metode_pembayaran, status_pesanan, keterangan)
                               VALUES (:no_nota, :total_harga, :metode_pembayaran, :status_pesanan, :keterangan)
                               RETURNING id_transaksi";

            $stmt = $this->conn->prepare($query_transaksi);
            $this->no_nota = htmlspecialchars(strip_tags($this->no_nota));
            $this->keterangan = htmlspecialchars(strip_tags($this->keterangan));
            $stmt->execute([
                ':no_nota' => $this->no_nota,
                ':total_harga' => $this->total_harga,
                ':metode_pembayaran' => $this->metode_pembayaran,
                ':status_pesanan' => $this->status_pesanan ?? 'Menunggu',
                ':keterangan' => $this->keterangan
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_id_transaksi = $row['id_transaksi'];
            $query_detail = "INSERT INTO " . $this->table_detail . "
                            (id_transaksi, id_produk, jumlah_keluar, subtotal)
                            VALUES (:id_transaksi, :id_produk, :jumlah_keluar, :subtotal)";

            $stmt_detail = $this->conn->prepare($query_detail);

            foreach ($detail_items as $item) {
                $stmt_detail->execute([
                    ':id_transaksi' => $new_id_transaksi,
                    ':id_produk' => $item['id_produk'],
                    ':jumlah_keluar' => $item['jumlah_keluar'],
                    ':subtotal' => $item['subtotal']
                ]);
            }
            $this->conn->commit();
            return true;

        } catch (\PDOException $e) {
            $this->conn->rollBack();
            error_log("Error Transaksi: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        $query = "SELECT t.id_transaksi, t.no_nota, t.tanggal, t.total_harga, t.metode_pembayaran,
                         t.status_pesanan, t.keterangan, d.id_produk, d.jumlah_keluar, d.subtotal,
                         p.nama_produk, p.harga
                  FROM " . $this->table_transaksi . " t
                  JOIN " . $this->table_detail . " d ON t.id_transaksi = d.id_transaksi
                  JOIN tabel_produk p ON d.id_produk = p.id_produk
                  ORDER BY t.id_transaksi DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
    
    // read
    public function getDetailNota($id_transaksi) {
        $query = "SELECT t.no_nota, t.tanggal, t.total_harga, t.metode_pembayaran, t.status_pesanan,
                         d.jumlah_keluar, d.subtotal, p.nama_produk, p.harga
                  FROM " . $this->table_transaksi . " t
                  JOIN " . $this->table_detail . " d ON t.id_transaksi = d.id_transaksi
                  JOIN tabel_produk p ON d.id_produk = p.id_produk
                  WHERE t.id_transaksi = :id_transaksi";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_transaksi", $id_transaksi);
        $stmt->execute();

        return $stmt;
    }

// update
    public function updateStatus() {
        $query = "UPDATE " . $this->table_transaksi . "
                  SET status_pesanan = :status_pesanan
                  WHERE id_transaksi = :id_transaksi";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':status_pesanan' => $this->status_pesanan,
            ':id_transaksi' => $this->id_transaksi
        ]);

        return $stmt->rowCount() > 0;
    }
}
?>
