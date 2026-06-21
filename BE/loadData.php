<?php
if (isTheseParameterAvailable(array('IDMhs'))) {
    $IDMhs = $_POST['IDMhs'];
    if (strcmp($IDMhs, 'Kosong') == 0) {
        $stmt = $conn->prepare('SELECT Nim, Nama, Umur, MyFoto FROM mahasiswa');
    } else {
        $stmt = $conn->prepare('SELECT Nim, Nama, Umur, MyFoto FROM mahasiswa WHERE Nim=?');
        $stmt->bind_param("s", $IDMhs);
    }
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($Nim, $Nama, $Umur, $MyFoto);
        $dataMahasiswa = array();
        while ($stmt->fetch()) {
            $tempdata['Nim'] = $Nim;
            $tempdata['Nama'] = $Nama;
            $tempdata['Umur'] = $Umur;
            $tempdata['MyFoto'] = $MyFoto;
            array_push($dataMahasiswa, $tempdata);
        }
        $response['error'] = false;
        $response['message'] = 'success';
        $response['data'] = $dataMahasiswa;

    } else {
        $response['error'] = true;
        if (strcmp($IDMhs, 'Kosong') == 0) {
            $response['message'] = 'Data Tidak Ada/Kosong';
        } else {
            $response['message'] = 'Data Tidak Ada Dengan ID ' . $IDMhs;
        }
    }
}
?>
