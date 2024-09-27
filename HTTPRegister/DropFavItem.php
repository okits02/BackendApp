<?php
require_once '../Include/DBOperation.php';

$db = new DBOperation();
$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {   
    if (isset($_POST['username']) && isset($_POST['movies_id'])) {
        $username = $_POST['username'];
        $movie_ids = $_POST['movies_id'];
        error_log("Username: " . $username);
        error_log("Movie IDs: " . $movie_ids);

        $result = $db->DropFavoriteItem($username, $movie_ids);

        error_log("DropFavoriteItem result: " . ($result ? 'true' : 'false'));

        if ($result == true) {
            $response['error'] = false;
            $response['message'] = "Delete suggestion";
        } else {
            $response['error'] = true;
            $response['message'] = "Error delete items";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Error: missing parameters";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid request method";
}

echo json_encode($response);
?>
