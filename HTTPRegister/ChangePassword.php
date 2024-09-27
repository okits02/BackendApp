<?php
require '../Include/DBOperation.php'; // Đảm bảo đường dẫn đúng

$db = new DBOperation();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['username'], $_POST['old_password'], $_POST['new_password'])){
        $UserName = $_POST['username'];
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];

        $result = $db->changePassword($UserName, $oldPassword, $newPassword);

        if ($result == 1) {
            echo json_encode(["message" => "Password changed successfully"]);
        } elseif ($result == 0) {
            echo json_encode(["message" => "Error updating password"]);
        } else {
            echo json_encode(["message" => "Old password is incorrect"]);
        }
    } else {
        echo json_encode(["message" => "Missing parameters"]);
    }
}
?>
