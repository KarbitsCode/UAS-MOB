<?php
if (isTheseParameterAvailable(array('IDMhs', 'Nama', 'Umur', 'MyFoto1', 'MyFoto2'))) {
    $IDMhs = $_POST['IDMhs'];
    $Nama = $_POST['Nama'];
    $Umur = $_POST['Umur'];
    $MyFoto1 = $_POST['MyFoto1'];
    $MyFoto2 = $_POST['MyFoto2'];

    $stmt = $conn->prepare('INSERT INTO mahasiswa (Nim,Nama,Umur,MyFoto) VALUES(?,?,?,?)');
    try {
        $stmt->bind_param("ssis", $IDMhs, $Nama, $Umur, $MyFoto2);
        if ($stmt->execute()) {
            $response['error'] = false;
            $response['message'] = 'insert success';
        } else {
            $response['error'] = true;
            $response['message'] = 'insert failed';
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    } finally {
        $stmt->close();
    }
}
?>
