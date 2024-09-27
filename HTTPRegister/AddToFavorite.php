<?php
require_once '../Include/DBOperation.php';

$db=new DBOperation();
if($_SERVER['REQUEST_METHOD']=='POST')
{
    if(isset($_POST['username'], $_POST['movies_id']))
    {
        $userName=$_POST['username'];
        $movies_id=$_POST['movies_id'];
        $result=$db->addToFavorite($userName,$movies_id);
        if($result==true)
        {
            echo json_encode(["message" => "Add to Favorite successfully"]);
        }else
        {
            echo json_encode(["message" => "Error updating to Favorite"]);
        }
    }else
    {
        echo json_encode(["message" => "Missing parameters"]);
    }
}