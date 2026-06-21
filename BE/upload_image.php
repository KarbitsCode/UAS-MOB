<?php
if (isTheseParameterAvailable(array('filename'))) {
    if (isset($_FILES['file'])) {
        try {
            $target_dir = "public/assets/photo/";
            $target_file = $target_dir . $_POST['filename'];
            $response = array();
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                $error = false;
                $message = 'Success Upload';
            } else {
                $error = true;
                $message = 'Error While Uploading';
            }
        } catch (Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }
    } else {
        $error = true;
        $message = "Required File Missing";
    }
    $response['error'] = $error;
    $response['message'] = $message;
}
?>
