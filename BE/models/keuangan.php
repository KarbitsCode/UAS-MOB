<?php

class Keuangan {
    private $conn;
    private $table_name = "tabel_keuangan";
// table
    public $id_keuangan;
    public $id_transaksi;
    public $tanggal;
    public $jenis; 
    public $nominal;
    public $keterangan;

    public function __construct($db) {
        $this->conn = $db;
    }

  // create
    public function catatKeuangan() {
        $query = "INSERT INTO " . $this->table_name . " 
                 (id_transaksi, jenis, nominal, keterangan) 
                 VALUES (:id_transaksi, :jenis, :nominal, :keterangan)";

        $stmt = $this->conn->prepare($query);
        $data = [
            ':id_transaksi' => $this->id_transaksi ?? null,
            ':jenis' => $this->jenis,
            ':nominal' => $this->nominal,
            ':keterangan' => htmlspecialchars(strip_tags($this->keterangan))
        ];

        return $stmt->execute($data);
    }

    // read
    public function getLaporanBulanIni() {
        $query = "SELECT id_keuangan, id_transaksi, tanggal, jenis, nominal, keterangan 
                  FROM " . $this->table_name . "
                  WHERE EXTRACT(MONTH FROM tanggal) = EXTRACT(MONTH FROM CURRENT_DATE)
                  AND EXTRACT(YEAR FROM tanggal) = EXTRACT(YEAR FROM CURRENT_DATE)
                  ORDER BY tanggal DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // hitung total
    public function getRingkasan() {
        $query = "SELECT jenis, SUM(nominal) as total 
                  FROM " . $this->table_name . " 
                  GROUP BY jenis";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
}
?>