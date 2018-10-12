<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/trip.php';

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
 
// prepare trip object
$trip = new Trip($db);

// set ID property of trip to be read
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
$trip->id = $_GET['id'];
 
// read the details of trip to be edited
$readResponse = $trip->readOne();

if(!$readResponse){
    echo json_encode(
        array(
            "success" => false,
            "errors" => array(
                "code" => 204,
                "message" => "No trip with given id"
            )
        )
    );
    die();
}

// create array
$trip_object = array(
	"id" => $trip->id,
    "trip_source" => $trip->trip_source,
    "trip_destination" => $trip->trip_destination,
    "trip_start_date" => $trip->trip_start_date,
    "trip_end_date" => $trip->trip_end_date,
    "trip_username" => $trip->trip_username
);

echo json_encode(
    array(
        "success" => true,
        "data" => $trip_object
    )
);
?>