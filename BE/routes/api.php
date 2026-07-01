<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/MahasiswaController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/PesananController.php';
require_once __DIR__ . '/../controllers/InventoryController.php';

$db = $pdo;

$response = array();

$resource = $_GET['resource'];

if (!$resource) {
    $response['error'] = true;
    $response['message'] = 'Resource Not Found';
    echo json_encode($response);
    exit;
}

switch ($resource) {
    case 'dashboard':
        $controller = new DashboardController($db);
        break;
    case 'pesanan':
        $controller = new PesananController($db);
        break;
    case 'inventory':
        $controller = new InventoryController($db);
        break;
    default:
        $controller = null;
        break;
}

if ($controller === null) {
    $response['error'] = true;
    $response['message'] = 'Invalid Resource';
} elseif (isset($_GET['apicall'])) {
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
            if (method_exists($controller, 'upload')) {
                $response = $controller->upload();
            } else {
                $response['error'] = true;
                $response['message'] = 'Upload not available for this resource';
            }
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
