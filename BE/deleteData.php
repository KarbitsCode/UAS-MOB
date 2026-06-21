<?php
if (isTheseParameterAvailable(array('IDMhs', 'Nama', 'Umur', 'MyFoto1', 'MyFoto2'))) {
    $IDMhs = $_POST['IDMhs'];
    $Nama = $_POST['Nama'];
    $Umur = $_POST['Umur'];
    $MyFoto1 = $_POST['MyFoto1'];
    $MyFoto2 = $_POST['MyFoto2'];
    $stmt = $conn->prepare('DELETE FROM mahasiswa WHERE Nim=?');
    try {
        $path = "public/assets/photo/" . $MyFoto1;
        if (file_exists($path)) {
            unlink($path);
        }
        $stmt->bind_param("s", $IDMhs);
        if ($stmt->execute()) {
            $response['error'] = false;
            $response['message'] = 'delete success';
        } else {
            $response['error'] = true;
            $response['message'] = 'delete failed';
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    } finally {
        $stmt->close();
    }
}
?>
