<?php
require_once "../Include/DBOperation.php";

$response = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['UserName']) && isset($_POST['UserPassword'])) {
        $db = new DBOperation();
        if ($db->userLogin($_POST['UserName'], $_POST['UserPassword'])) {
            $user = $db->getUserByUserName($_POST['UserName']);
            $response['error'] = false;
            $response['id'] = $user['ID'];
            $response['username'] = $user['UserName'];
            $response['email'] = $user['Email'];
        } else {
            $response['error'] = true;
            $response['message'] = "Invalid username or password";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Required fields are missing";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid request method";
}

echo json_encode($response);
