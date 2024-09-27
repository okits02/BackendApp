<?php
require_once '../Include/DBOperation.php';
$response=array();
if($_SERVER['REQUEST_METHOD']=='GET')
{
    $db=new DBOperation();
    if(isset($_GET['genreid']))
    {
        $genreid=$_GET['genreid'];
        $movies=$db->getMoviesByGenre($genreid);
        $moviesList=array();
        while($row=$movies->fetch_assoc())
        {
            $genre_ids = explode(",", $row['genre_ids']);
            $genre_name=array();
            foreach($genre_ids as $genre_id)
            {
                $genreData=$db->getGenreByID($genre_id);
                if($genreData!=null)
                {
                    $genre_name[]=$genreData['name'];
                }
            }
            $row['genre_ids']=$genre_name;
            $moviesList[]=$row;
        }
        $response['item']=$moviesList;
    }else
    {
        $response['message']=="Missing parameters";
    }
}
$json = json_encode($response);
echo $json;

