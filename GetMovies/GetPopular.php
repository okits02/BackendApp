<?php
require_once "../Include/DBOperation.php";

$db = new DBOperation();
$result = $db->getMoviespopular();

$movies = array();
while ($row = $result->fetch_assoc()) {
    $row['genre_ids'] = json_decode($row['genre_ids']);
    
    foreach ($row['genre_ids'] as $key => $id) {
        $genreName = $db->getGenreByID($id)['name'];
        $row['genre_ids'][$key] = $genreName;
    }

    $movies[] = $row;
}
$json=json_encode($movies);
echo '{"item":'.$json.' }';
?>
