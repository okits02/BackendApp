<?php
require_once "../Include/DBOperation.php";

$dbOperation = new DBOperation();
$result = $dbOperation->getGenre();

$movies = array();
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo json_encode($movies);
?>
