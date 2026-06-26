<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/MahasiswaController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new MahasiswaController($db);

$response = array();

if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'loadData':
            $response = $controller->loadData();
            break;
        case 'insertData':
            $response = $controller->insertData();
            break;
        case 'updateData':
            $response = $controller->updateData();
            break;
        case 'deleteData':
            $response = $controller->deleteData();
            break;
        case 'upload':
            $response = $controller->upload();
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
?>