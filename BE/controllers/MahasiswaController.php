<?php
class MahasiswaController {
    private $db;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function isTheseParameterAvailable($params) {
        foreach ($params as $param) {
            if (!isset($_POST[$param])) {
                return false;
            }
        }
        return true;
    }

    public function loadData() {
        if ($this->isTheseParameterAvailable(array('IDMhs'))) {
            $IDMhs = $_POST['IDMhs'];
            if (strcmp($IDMhs, 'Kosong') == 0) {
                $stmt = $this->conn->prepare('SELECT Nim, Nama, Umur, MyFoto FROM mahasiswa');
            } else {
                $stmt = $this->conn->prepare('SELECT Nim, Nama, Umur, MyFoto FROM mahasiswa WHERE Nim=?');
                $stmt->bind_param("s", $IDMhs);
            }
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $Nim = '';
                $Nama = '';
                $Umur = 0;
                $MyFoto = '';
                $stmt->bind_result($Nim, $Nama, $Umur, $MyFoto);
                $dataMahasiswa = array();
                while ($stmt->fetch()) {
                    $tempdata['Nim'] = $Nim;
                    $tempdata['Nama'] = $Nama;
                    $tempdata['Umur'] = $Umur;
                    $tempdata['MyFoto'] = $MyFoto;
                    array_push($dataMahasiswa, $tempdata);
                }
                return array('error' => false, 'message' => 'success', 'data' => $dataMahasiswa);
            } else {
                if (strcmp($IDMhs, 'Kosong') == 0) {
                    return array('error' => true, 'message' => 'Data Tidak Ada/Kosong');
                } else {
                    return array('error' => true, 'message' => 'Data Tidak Ada Dengan ID ' . $IDMhs);
                }
            }
        }
        return array('error' => true, 'message' => 'Required parameters missing');
    }

    public function insertData() {
        if ($this->isTheseParameterAvailable(array('IDMhs', 'Nama', 'Umur', 'MyFoto1', 'MyFoto2'))) {
            $IDMhs = $_POST['IDMhs'];
            $Nama = $_POST['Nama'];
            $Umur = $_POST['Umur'];
            $MyFoto2 = $_POST['MyFoto2'];

            $stmt = $this->conn->prepare('INSERT INTO mahasiswa (Nim,Nama,Umur,MyFoto) VALUES(?,?,?,?)');
            try {
                $stmt->bind_param("ssis", $IDMhs, $Nama, $Umur, $MyFoto2);
                if ($stmt->execute()) {
                    return array('error' => false, 'message' => 'insert success');
                } else {
                    return array('error' => true, 'message' => 'insert failed');
                }
            } catch (Exception $e) {
                return array('error' => true, 'message' => $e->getMessage());
            } finally {
                $stmt->close();
            }
        }
        return array('error' => true, 'message' => 'Required parameters missing');
    }

    public function updateData() {
        if ($this->isTheseParameterAvailable(array('IDMhs', 'Nama', 'Umur', 'MyFoto1', 'MyFoto2'))) {
            $IDMhs = $_POST['IDMhs'];
            $Nama = $_POST['Nama'];
            $Umur = $_POST['Umur'];
            $MyFoto1 = $_POST['MyFoto1'];
            $MyFoto2 = $_POST['MyFoto2'];

            $stmt = $this->conn->prepare('UPDATE mahasiswa SET Nama=?, Umur=?, MyFoto=? WHERE Nim=?');
            try {
                if (strcmp($MyFoto1, $MyFoto2) != 0) {
                    $path = "public/assets/photo/" . $MyFoto1;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $stmt->bind_param("siss", $Nama, $Umur, $MyFoto2, $IDMhs);
                if ($stmt->execute()) {
                    return array('error' => false, 'message' => 'update success');
                } else {
                    return array('error' => true, 'message' => 'update failed');
                }
            } catch (Exception $e) {
                return array('error' => true, 'message' => $e->getMessage());
            } finally {
                $stmt->close();
            }
        }
        return array('error' => true, 'message' => 'Required parameters missing');
    }

    public function deleteData() {
        if ($this->isTheseParameterAvailable(array('IDMhs', 'Nama', 'Umur', 'MyFoto1', 'MyFoto2'))) {
            $IDMhs = $_POST['IDMhs'];
            $MyFoto1 = $_POST['MyFoto1'];

            $stmt = $this->conn->prepare('DELETE FROM mahasiswa WHERE Nim=?');
            try {
                $path = "public/assets/photo/" . $MyFoto1;
                if (file_exists($path)) {
                    unlink($path);
                }
                $stmt->bind_param("s", $IDMhs);
                if ($stmt->execute()) {
                    return array('error' => false, 'message' => 'delete success');
                } else {
                    return array('error' => true, 'message' => 'delete failed');
                }
            } catch (Exception $e) {
                return array('error' => true, 'message' => $e->getMessage());
            } finally {
                $stmt->close();
            }
        }
        return array('error' => true, 'message' => 'Required parameters missing');
    }

    public function upload() {
        if ($this->isTheseParameterAvailable(array('filename'))) {
            if (isset($_FILES['file'])) {
                try {
                    $target_dir = "public/assets/photo/";

                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    $target_file = $target_dir . $_POST['filename'];
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                        return array('error' => false, 'message' => 'Success Upload');
                    } else {
                        return array('error' => true, 'message' => 'Error While Uploading');
                    }
                } catch (Exception $e) {
                    return array('error' => true, 'message' => $e->getMessage());
                }
            } else {
                return array('error' => true, 'message' => 'Required File Missing');
            }
        }
        return array('error' => true, 'message' => 'Required parameters missing');
    }
}
?>
