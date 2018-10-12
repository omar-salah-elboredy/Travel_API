<?php
class Trip{
 
    // Database connection and table name
    private $conn;
    private $table_name = "trips";

    // Trip object attributes
    public $id;
    public $trip_source;
    public $trip_destination;
    public $trip_start_date;
    public $trip_end_date;
    public $trip_user_id;
    public $trip_username;

    // Constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read all trips
    function read($forUser){
        $query="";
        if($forUser){
            $query = "SELECT
                    u.username as trip_username, u.id as trip_user_id, t.trip_source, t.trip_destination, t.trip_start_date, t.trip_end_date, t.id
                FROM
                    " . $this->table_name . " t
                    LEFT JOIN
                        users u
                            ON t.trip_user_id = u.id
                WHERE
                    u.id = ?
                ORDER BY
                    t.trip_end_date DESC";
        }
        else{
            $query = "SELECT
                    u.username as trip_username, u.id as trip_user_id, t.trip_source, t.trip_destination, t.trip_start_date, t.trip_end_date, t.id
                FROM
                    " . $this->table_name . " t
                    LEFT JOIN
                        users u
                            ON t.trip_user_id = u.id
                ORDER BY
                    t.trip_end_date DESC";
        }
        // select all trips
        
        $stmt = $this->conn->prepare($query);
        
        if($forUser){
            $stmt->bindParam(1,$this->trip_user_id);
        }

        if($stmt->execute()){
            return array($stmt, null);
        }else{
            $errorInfo = $stmt->errorInfo();
            return array(null, $errorInfo[2]);
        }
    }

    // create trip
    function create(){
        // Insert new trip
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    trip_source=:source, trip_destination=:destination, trip_start_date=:start, trip_end_date=:end, trip_user_id=:user_id";
        // prepare query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->trip_source=htmlspecialchars(strip_tags($this->trip_source));
        $this->trip_destination=htmlspecialchars(strip_tags($this->trip_destination));
        $this->trip_start_date=htmlspecialchars(strip_tags($this->trip_start_date));
        $this->trip_end_date=htmlspecialchars(strip_tags($this->trip_end_date));
        $this->trip_user_id=htmlspecialchars(strip_tags($this->trip_user_id));

        // bind values
        $stmt->bindParam(":source", $this->trip_source);
        $stmt->bindParam(":destination", $this->trip_destination);
        $stmt->bindParam(":start", $this->trip_start_date);
        $stmt->bindParam(":end", $this->trip_end_date);
        $stmt->bindParam(":user_id", $this->trip_user_id);
     
        if($stmt->execute()){
            return array(true, null);
        }else{
            $errorInfo = $stmt->errorInfo();
            return array(false, $errorInfo[2]);
        }
    }

    // Used when filling up the update trip details form
    function readOne(){
        // query to read single record
        $query = "SELECT
                    u.username as trip_username, t.trip_source, t.trip_destination, t.trip_start_date, t.trip_end_date, t.id
                FROM
                    " . $this->table_name . " t
                    LEFT JOIN
                        users u
                            ON t.trip_user_id = u.id
                WHERE
                    t.id = ?
                LIMIT
                    0,1";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        if($stmt->rowCount() <= 0){
            return false;
        }
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->trip_source = $row['trip_source'];
        $this->trip_destination = $row['trip_destination'];
        $this->trip_start_date = $row['trip_start_date'];
        $this->trip_end_date = $row['trip_end_date'];
        $this->trip_username = $row['trip_username'];
        $this->id = $row['id'];
        return true;
    }

    // Delete a trip
    function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = $this->id;
        $stmt->bindParam(1, $this->id);
        if($stmt->execute()){
            return array(true, null);
        }else{
            $errorInfo = $stmt->errorInfo();
            return array(false, $errorInfo[2]);
        }
    }

    // update the trip
    function update(){
        // update query
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    trip_start_date = :start_date,
                    trip_end_date = :end_date,
                    trip_source = :source,
                    trip_destination = :destination
                WHERE
                    id = :id";
     
        // prepare query statement
        $stmt = $this->conn->prepare($query);
     
        // bind new values
        $stmt->bindParam(':start_date', $this->trip_start_date);
        $stmt->bindParam(':end_date', $this->trip_end_date);
        $stmt->bindParam(':source', $this->trip_source);
        $stmt->bindParam(':destination', $this->trip_destination);
        $stmt->bindParam(':id', $this->id);
        
        // execute the query
        if($stmt->execute()){
            return array(true, null);
        }else{
            $errorInfo = $stmt->errorInfo();
            return array(false, $errorInfo[2]);
        }
    }
}
?>