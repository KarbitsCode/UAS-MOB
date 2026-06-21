<?php
if (isTheseParameterAvailable(array('IDMhs', 'Nama', 'Umur', 'MyFoto1', 'MyFoto2'))) {
    $IDMhs = $_POST['IDMhs'];
    $Nama = $_POST['Nama'];
    $Umur = $_POST['Umur'];
    $MyFoto1 = $_POST['MyFoto1'];
    $MyFoto2 = $_POST['MyFoto2'];

    $stmt = $conn->prepare('UPDATE mahasiswa SET Nama=?, Umur=?, MyFoto=? WHERE Nim=?');
    try {
        if (strcmp($MyFoto1, $MyFoto2) != 0) {
            $path = "public/assets/photo/" . $MyFoto1;
            unlink($path);
        }
        $stmt->bind_param("siss", $Nama, $Umur, $MyFoto2, $IDMhs);
        if ($stmt->execute()) {
            $response['error'] = false;
            $response['message'] = 'update success';
        } else {
            $response['error'] = true;
            $response['message'] = 'update failed';
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    } finally {
        $stmt->close();
    }
}
?>
