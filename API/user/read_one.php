<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

if(!($_SERVER['REQUEST_METHOD'] === 'GET')){
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
 
// set ID property of user to be edited
if(!isset($_GET['id'])){
    echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "No id to search for sent"
            )
        )
    );
    die();
}

// set user id to be read
$user->id = $_GET['id'];
 
// read the details of user to be edited
$readResponse = $user->read_one();

if(!$readResponse){
    echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 204,
                "message" => "No user with given id"
            )
        )
    );
    die();
}

// create array
$user_arr = array(
	"id" => $user->id,
    "username" => $user->username,
    "email_address" => $user->email_address,
    "access_level" => $user->access_level,
    "first_name" => $user->first_name,
    "last_name" => $user->last_name,
    "dob" => $user->dob
);
 
echo json_encode(
    array(
        "success" => true,
        "data" => $user_arr
    )
);
?>