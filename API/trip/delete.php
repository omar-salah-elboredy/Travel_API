<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
 
// include database and object file
include_once '../config/database.php';
include_once '../objects/trip.php';

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
 
// prepare trip object
$trip = new Trip($db);
 
// get trip id
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
$trip->id = $_GET['id'];


// delete the trip
$delete_response = $trip->delete();

if($delete_response[0]){
    echo json_encode(
        array(
            "success" => true,
            "errors" => array(
                "code" => 200,
                "message" => "Trip deleted successfully"
            )
        )
    );
}
 
// if unable to delete the trip
else{
    if(is_null($delete_response[1])){
        echo json_encode(
            array(
                "success" => false,
                "errors" => array(
                    "code" => 204,
                    "message" => "No trips found"
                )
            )
        );
    }
    else{
        echo json_encode(
            array(
                "success" => false,
                "errors" => array(
                    "code" => 500,
                    "message" => "No trips found. \"' . $delete_response[1] . '\""
                )
            )
        );
    }
}
?>