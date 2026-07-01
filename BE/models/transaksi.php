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

    public function getById($id_transaksi) {
        $query = "SELECT t.id_transaksi, t.no_nota, t.tanggal, t.total_harga, t.metode_pembayaran,
                         t.status_pesanan, t.keterangan, d.id_produk, d.jumlah_keluar, d.subtotal,
                         p.nama_produk, p.harga, p.stok
                  FROM " . $this->table_transaksi . " t
                  JOIN " . $this->table_detail . " d ON t.id_transaksi = d.id_transaksi
                  JOIN tabel_produk p ON d.id_produk = p.id_produk
                  WHERE t.id_transaksi = :id_transaksi
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_transaksi', $id_transaksi);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function completeTransaction() {
        try {

            $transactionData = $this->getById($this->id_transaksi);
            if (!$transactionData) {
                return array(
                    'error' => true,
                    'message' => 'pesanan tidak ditemukan'
                );
            }

            if ($transactionData['status_pesanan'] !== 'Menunggu') {
                return array(
                    'error' => true,
                    'message' => 'status pesanan sudah ' . $transactionData['status_pesanan']
                );
            }

            if ((int) $transactionData['stok'] < (int) $transactionData['jumlah_keluar']) {
                return array(
                    'error' => true,
                    'message' => 'stok produk tidak cukup'
                );
            }

            $this->conn->beginTransaction();

            $updateStatusQuery = "UPDATE " . $this->table_transaksi . "
                                  SET status_pesanan = :status_pesanan
                                  WHERE id_transaksi = :id_transaksi AND status_pesanan = 'Menunggu'";
            $statusStmt = $this->conn->prepare($updateStatusQuery);
            $statusStmt->execute(array(
                ':status_pesanan' => 'Selesai',
                ':id_transaksi' => $this->id_transaksi
            ));

            if ($statusStmt->rowCount() === 0) {
                $this->conn->rollBack();
                return array(
                    'error' => true,
                    'message' => 'gagal mengubah status pesanan'
                );
            }

            $updateStockQuery = "UPDATE tabel_produk
                                 SET stok = stok - :jumlah_keluar
                                 WHERE id_produk = :id_produk AND stok >= :jumlah_keluar";
            $stockStmt = $this->conn->prepare($updateStockQuery);
            $stockStmt->execute(array(
                ':jumlah_keluar' => $transactionData['jumlah_keluar'],
                ':id_produk' => $transactionData['id_produk']
            ));

            if ($stockStmt->rowCount() === 0) {
                $this->conn->rollBack();
                return array(
                    'error' => true,
                    'message' => 'stok produk tidak cukup'
                );
            }

            $keuanganQuery = "INSERT INTO tabel_keuangan (id_transaksi, tanggal, jenis, nominal, keterangan)
                              VALUES (:id_transaksi, CURRENT_TIMESTAMP, :jenis, :nominal, :keterangan)";
            $keuanganStmt = $this->conn->prepare($keuanganQuery);
            $keuanganStmt->execute(array(
                ':id_transaksi' => $transactionData['id_transaksi'],
                ':jenis' => 'Pemasukan',
                ':nominal' => $transactionData['total_harga'],
                ':keterangan' => htmlspecialchars(strip_tags($transactionData['keterangan']))
            ));

            $this->conn->commit();

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
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            error_log('Error Complete Transaksi: ' . $e->getMessage());

            return array(
                'error' => true,
                'message' => 'update pesanan failed'
            );
        }
    }
}
?>
