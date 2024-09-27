<?php
require_once '../Include/DBOperation.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $db = new DBOperation();
    $movies_result = null;

    if(isset($_GET['movies_id']))
    {
        $movies_result = $db->getMovies($_GET['movies_id']);
        
        if ($movies_result !== null) {
            $genre_ids = explode(",", $movies_result['genre_ids']);
            $genre_names = array();
            
            foreach ($genre_ids as $genre_id) {
                $genre = $db->getGenreByID($genre_id);
                if ($genre !== null) {
                    $genre_names[] = $genre['name'];
                }
            }
            $movies_result['genre_ids'] = $genre_names;
        }
    }

    if ($movies_result !== null) {
        echo json_encode($movies_result);
    } else {
        echo json_encode(array("message" => "Không tìm thấy kết quả"));
    }
}
?>
