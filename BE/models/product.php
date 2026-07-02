<?php

class Produk {
    private $conn;
    //table
    private $table_name = "tabel_produk";
    public $id_produk;
    public $nama_produk;
    public $harga;
    public $stok;

    public function __construct($db) {
        $this->conn = $db;
    }

 // read
    public function getAll() {
        $query = "SELECT id_produk, nama_produk, harga, stok 
                  FROM " . $this->table_name . " 
                  ORDER BY id_produk DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getById($id_produk) {
        $query = "SELECT id_produk, nama_produk, harga, stok
                  FROM " . $this->table_name . "
                  WHERE id_produk = :id_produk
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_produk', $id_produk);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // create
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nama_produk, harga, stok) 
                  VALUES (:nama_produk, :harga, :stok)";

        $stmt = $this->conn->prepare($query);

        $this->nama_produk = htmlspecialchars(strip_tags($this->nama_produk));
        $this->harga = htmlspecialchars(strip_tags($this->harga));
        $this->stok = htmlspecialchars(strip_tags($this->stok));


        $stmt->bindParam(":nama_produk", $this->nama_produk);
        $stmt->bindParam(":harga", $this->harga);
        $stmt->bindParam(":stok", $this->stok);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

// update
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nama_produk = :nama_produk, harga = :harga, stok = :stok 
                  WHERE id_produk = :id_produk";

        $stmt = $this->conn->prepare($query);

        $this->nama_produk = htmlspecialchars(strip_tags($this->nama_produk));
        $this->harga = htmlspecialchars(strip_tags($this->harga));
        $this->stok = htmlspecialchars(strip_tags($this->stok));
        $this->id_produk = htmlspecialchars(strip_tags($this->id_produk));

        $stmt->bindParam(":nama_produk", $this->nama_produk);
        $stmt->bindParam(":harga", $this->harga);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":id_produk", $this->id_produk);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

 // delete
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_produk = :id_produk";
        
        $stmt = $this->conn->prepare($query);
        $this->id_produk = htmlspecialchars(strip_tags($this->id_produk));
        $stmt->bindParam(":id_produk", $this->id_produk);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (\PDOException $e) {
            return false; 
        }
        return false;
    }
}
?>
