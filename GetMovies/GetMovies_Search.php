<?php
require_once '../Include/DBOperation.php';

$response = array();
$db = new DBOperation();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $movies = $db->getMovies_search();
    $movieList = array();

    while ($movie = $movies->fetch_assoc()) {
        $genre_ids = explode(",", $movie['genre_ids']);
        $genre_name = array();

        foreach ($genre_ids as $genre_id) {
            $genre = $db->getGenreByID($genre_id);
            if ($genre !== null) {
                $genre_name[] = $genre['name'];
            }
        }

        $movie['genre_ids'] = $genre_name;
        $movieList[] = $movie;
    }

    $response['item'] = $movieList;
} else {
    $response['message'] = "Invalid request method";
}

$json = json_encode($response);
echo $json;
