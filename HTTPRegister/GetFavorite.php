<?php
require_once '../Include/DBOperation.php';

$db = new DBOperation();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['username'])) {
        $username = $_GET['username'];

        $moviesID_Result = $db->getMoviesIDbyUserName($username);

        if ($moviesID_Result->num_rows > 0) {
            $moviesIDs = array();
            while ($row = $moviesID_Result->fetch_assoc()) {
                $moviesIDs[] = $row['movie_ids'];
            }
            $movies = array();
            foreach ($moviesIDs as $movieID) {
                $movie_Result = $db->getMoviesByMoviesID($movieID);
                if ($movie_Result->num_rows > 0) {
                    while ($movieData = $movie_Result->fetch_assoc()) {
                        $genre_ids = explode(',', $movieData['genre_ids']);
                        $genre_names = array();
                        foreach ($genre_ids as $genre_id) {
                            $genre_name = $db->getGenreByID($genre_id);
                            if ($genre_name) {
                                $genre_names[] = $genre_name['name'];
                            }
                        }
                        $movieData['genre_ids'] = $genre_names;
                        $movies[] = $movieData;
                    }
                }
            }
            echo json_encode(["item" => $movies]);
        } else {
            echo json_encode(["message" => "No movies found for this user"]);
        }
    } else {
        echo json_encode(["message" => "Missing userID parameter"]);
    }
}
?>
