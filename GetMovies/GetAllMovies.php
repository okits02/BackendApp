<?php
require_once '../Include/DBOperation.php';
$db=new DBOperation();
$reponse=array();
if($_SERVER['REQUEST_METHOD']=='GET')
{
    $result=$db->getAllMovies();
    while($row=$result->fetch_assoc())
    {
        $genre_ids = explode(",", $movies_result['genre_ids']);
        $genre_name=array();
        foreach($genre_ids as $genre_id)
        {
            $genre=$db->getGenreByID($genre_id);
            if($genre!=null)
            {
                $genre_name[]=$genre['name'];
            }
        } 
    }
}