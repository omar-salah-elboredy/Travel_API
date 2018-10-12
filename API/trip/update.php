<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/trip.php';

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
 
// prepare trip object
$trip = new Trip($db);
 
// get id of trip to be edited
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

// set ID property of trip to be edited
$trip->id = $data->id;
$trip->readOne();
// set trip property values
$trip->trip_end_date = $data->trip_end_date;
$trip->trip_start_date = $data->trip_start_date;
$trip->trip_source = $data->trip_source;
$trip->trip_destination = $data->trip_destination;

$update_response = $trip->update();
// update the trip
if($update_response[0]){
    echo json_encode(
        array(
            "success" => true,
            "errors" => array(
                "code" => 200,
                "message" => "Trip has been updated successfully"
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
                "message" => "Unable to update trip. '.$update_response[1].'"
            )
        )
    );
}
?>