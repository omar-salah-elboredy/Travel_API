<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
 
// include database and object file
include_once '../config/database.php';
include_once '../objects/user.php';

if(!($_SERVER['REQUEST_METHOD'] === 'DELETE')){
    echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "Invalid request method"
            )
        )
    );
    die();
}

// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare user object
$user = new User($db);
 
// get user id
if(!isset($_GET['id'])){
    echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "No id to delete is sent"
            )
        )
    );
    die();
}

// set user id to be deleted
$user->id = $_GET['id'];
 
// delete the user
$delete_response = $user->delete();

if($delete_response[0]){
    echo json_encode(
        array(
            "success" => true,
            "errors" => array(
                "code" => 200,
                "message" => "User deleted successfully"
            )
        )
    );
    die();
}
 
// if unable to delete the user
else{
    if(is_null($delete_response[1])){
        echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 204,
                "message" => "No users found"
            )
        )
    );
    die();
    }
    else{
        echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 500,
                "message" => "No users found. Info: \"' . $delete_response[1] .'\""
            )
        )
    );
    die();
    }
}
?>