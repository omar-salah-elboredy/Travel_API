<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

if(!($_SERVER['REQUEST_METHOD'] === 'PUT')){
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
 
// get id of user to be edited
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

// set ID property of user to be edited
$user->id = $data->id;

$user->read_one();

// set user property values
if(isset($data->username)){
	$user->username = $data->username;
}

if(isset($data->access_level)){
	$user->access_level= $data->access_level;
}else{
	$user->access_level = 1;
}
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

$update_response;
if(isset($data->password)){
	$salt = $user->generate_salt();
	$user->salt = $salt;
	$user->password = sha1(sha1($data->password).sha1($salt));
	$update_response = $user->update(true); //Update password too
}
$update_response = $user->update(false); //Don't update password


// update the user
if($update_response[0]){
    echo json_encode(
        array(
            "success" => true,
            "errors" => array(
                "code" => 200,
                "message" => "User has been updated successfully"
            )
        )
    );
}
// if unable to update the trip, tell the user
else{
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 500,
                "message" => "Unable to update user. '.$update_response[1].'"
            )
        )
    );
}
?>