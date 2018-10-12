<?php
// required headers
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

// instantiate database and trip object
$database = new Database();
$db = $database->getConnection();

// initialize object
$trip = new Trip($db);

//Get trips for specific user
$trip->trip_user_id = isset($_GET['id']) ? $_GET['id'] : -1;

$read_trips_response;
if($trip->trip_user_id > 0){
    $read_trips_response = $trip->read(true);
}
else{
    $read_trips_response = $trip->read(false);
}
// query trips

$stmt = $read_trips_response[0];

$num_of_rows = 0;
if(!is_null($stmt)){
    $num_of_rows = $stmt->rowCount();
}

// check if more than 0 record found
if($num_of_rows > 0){
 
    // products array
    $trips_arr=array();
    $trips_arr["success"]=true;
    $trips_arr["data"]=array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $trip_item=array(
            "id" => $id,
            "trip_source" => $trip_source,
            "trip_destination" => $trip_destination,
            "trip_start_date" => $trip_start_date,
            "trip_end_date" => $trip_end_date,
            "trip_user_id" => $trip_user_id,
            "trip_username" => $trip_username
        );
        array_push($trips_arr["data"], $trip_item);
    }
    echo json_encode($trips_arr);
}
else{
    if(is_null($read_trips_response[1])){
        echo json_encode(
            array(
                "success" => false,
                "errors" => array(
                    "code" => 204,
                    "message" => "No trips found"
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
                    "message" => "No trips found, ' . $read_trips_response[1] . '"
                )
            )
        );
        die();
    }
}
?>