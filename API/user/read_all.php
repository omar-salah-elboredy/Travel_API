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

// query trips
$read_users_response = $user->read_all();
$stmt = $read_users_response[0];

$num_of_rows = 0;
if(!is_null($stmt)){
    $num_of_rows = $stmt->rowCount();
}

// check if more than 0 record found
if($num_of_rows > 0){
 
    // products array
    $users_arr=array();
    $trips_arr["success"]=true;
    $users_arr["data"]=array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $user_item=array(
            // set values to object properties
            "username" => $username,
            "id" => $id,
            "email_address" => $email_address,
            "access_level" => $access_level,
            "first_name" => $first_name,
            "last_name" =>$last_name,
            "dob" => $dob
        );
        array_push($users_arr["data"], $user_item);
    }
 
    echo json_encode($users_arr);
}
else{
    if(is_null($read_users_response[1])){
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
                    "message" => "No users found, ' . $read_trips_response[1] . '"
                )
            )
        );
        die();
    }
}
?>