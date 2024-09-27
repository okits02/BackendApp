<?php
require_once '../Include/DBOperation.php';
    $response=array();
    if($_SERVER['REQUEST_METHOD']=='POST')
    {
        if(isset($_POST['username']) and isset($_POST['password']) and isset($_POST['email']) and isset($_POST['phone']))
        {
            $db=new DBOperation();
            $result=$db->createUser($_POST['username'], $_POST['password'], $_POST['email'], $_POST['phone']);
            
            if($result==1)
            {
                $response['error']=false;
                $response['message']="User registered successfully";
            }else if($result==2)
            {
                $response['error']=true;
                $response['message']="Some error orcurrend pleas try agian";
            }else if($result==0)
            {
                $response['error']=true;
                $response['message']="It seems you are already registered, pleas chose a different email and userName";
            }
        }else
        {
            $response['error']=true;
            $response['message']='Required fields are missing';
        }
    }else 
    {   
        $response['error']=true;
        $response['message']='Invalid Request';
    }
echo json_encode($response);