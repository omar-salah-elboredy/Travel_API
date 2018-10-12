<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
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

if(!isset($data->username) || !isset($data->password)){
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

$user->username = $data->username;
$user->get_salt();
$user->password = sha1(sha1($data->password).sha1($user->salt));

$sign_in_respose = $user->sign_in();

if($sign_in_respose){
	$user_arr = array(
		"id" => $user->id,
	    "username" => $user->username,
	    "email_address" => $user->email_address,
	    "access_level" => $user->access_level,
	    "first_name" => $user->first_name,
	    "last_name" => $user->last_name,
	    "dob" => $user->dob
	);
	// make it json format
	echo json_encode(
	    array(
	        "success" => true,
	        "data" => $user_arr
	    )
	);
}

else{
	echo '{ "message": "Execution failed, unable to sign in", "Error Info":"' . $sign_in_respose[1] . '" }';
}
?>