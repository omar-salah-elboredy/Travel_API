<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate user object
include_once '../objects/user.php';

if(!($_SERVER['REQUEST_METHOD'] === 'POST')){
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

$database = new Database();
$db = $database->getConnection();
 
$user = new User($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));

if(is_null($data)){
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "No parameters sent"
            )
        )
    );
    die();
}

//Check inputs
if(!isset($data->username) || !isset($data->password) || !isset($data->access_level)){
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "Error, one or more variables not set"
            )
        )
    );
    die();
}

if(strlen($data->password) <= 0){
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "Error, password couldn\'t be left blank"
            )
        )
    );
    die();
}

// set product property values
$user->username = $data->username;
$user->access_level = $data->access_level;
if(isset($data->first_name)){
	$user->first_name = $data->first_name;
}
if(isset($data->last_name)){
	$user->last_name = $data->last_name;
}
if(isset($data->dob)){
	$user->dob = $data->dob;
}
if(isset($data->email_address)){
	$user->email_address = $data->email_address;
}
$salt = $user->generate_salt();
$user->salt = $salt;
$user->password = sha1(sha1($data->password).sha1($salt));

// Admin create user with custom level
$create_response = $user->create();
if($create_response[0]){
	echo json_encode(
        array(
            "success" => true,
            "errors" => array(
                "code" => 201,
                "message" => "User created successfully"
            )
        )
    );
    die();
}
// if unable to create the user, tell the admin
else{
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 500,
                "message" => "Execution failed, unable to create user", "Error Info":"' . $create_response[1] . '"
            )
        )
    );
    die();
}
?>