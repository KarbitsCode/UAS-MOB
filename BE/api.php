<?php
require_once 'koneksi.php';
$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'loadData' :
            require_once 'loadData.php';
        break;
        case 'insertData' :
            require_once 'insertData.php';
        break;
        case 'updateData' :
            require_once 'updateData.php';
        break;
        case 'deleteData' :
            require_once 'deleteData.php';
        break;
        case 'upload' :
            require_once 'upload_image.php';
        break;
        default:
            $response['error'] = true;
            $response['message'] = 'Invalid Operation Called';
            break;
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid API Call';
}
echo json_encode($response);

function isTheseParameterAvailable($params)
{
    foreach ($params as $param) {
        if (!isset($_POST[$param])) {
            return false;
        }
    }
    return true;
}
?>
