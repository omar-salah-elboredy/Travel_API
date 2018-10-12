<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate trip object
include_once '../objects/trip.php';

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
    return;
}

$database = new Database();
$db = $database->getConnection();
 
$trip = new Trip($db);
 
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
if(!isset($data->trip_source) || !isset($data->trip_destination) || !isset($data->trip_start_date) || !isset($data->trip_end_date) || !isset($data->trip_user_id)){
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 400,
                "message" => "Error, one or more variables not set in request\'s body"
            )
        )
    );
    die();
}

// set trip property values
$trip->trip_source = $data->trip_source;
$trip->trip_destination = $data->trip_destination;
$trip->trip_start_date = $data->trip_start_date;
$trip->trip_end_date = $data->trip_end_date;
$trip->trip_user_id = $data->trip_user_id;
 
// create the trip
$create_response = $trip->create();
if($create_response[0]){
	echo json_encode(
        array(
            "success" => true,
            "data" => array(
                "code" => 201,
                "message" => "Trip successfully created"
            )
        )
    );
}
 
// if unable to create the trip, tell the user
else{
	echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 500,
                "message" => "Execution failed, unable to create trip. Info:' . $create_response[1] . '"
            )
        )
    );
}
?>