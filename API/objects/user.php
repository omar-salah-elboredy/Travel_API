<?php
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $username;
    public $password;
    public $salt;
    public $access_level;

    //Optionals
    public $email_address;
    public $first_name;
    public $last_name;
    public $dob;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function generate_salt(){
        $bytes = random_bytes(5);
        $random_salt = bin2hex($bytes);
        return $random_salt;
    }

    function read_all(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare( $query );
        if($stmt->execute()){
            return array($stmt, null);
        }else{
            $errorInfo = $stmt->errorInfo();
            return array(null, $errorInfo[2]);
        }
    }

    function create(){
        // Insert new user
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    username=:user_name, password=:pass, salt=:salt, access_level=:user_level, email_address=:email, first_name=:f_name, last_name=:l_name, dob=:date_of_birth";
        // prepare query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->salt=htmlspecialchars(strip_tags($this->salt));
        $this->access_level=htmlspecialchars(strip_tags($this->access_level));
        $this->email_address=htmlspecialchars(strip_tags($this->email_address));
        $this->first_name=htmlspecialchars(strip_tags($this->first_name));
        $this->last_name=htmlspecialchars(strip_tags($this->last_name));
        $this->dob=htmlspecialchars(strip_tags($this->dob));

        // bind values
        $stmt->bindParam(":user_name", $this->username);
        $stmt->bindParam(":pass", $this->password);
        $stmt->bindParam(":salt", $this->salt);
        $stmt->bindParam(":user_level", $this->access_level);
        $stmt->bindParam(":email", $this->email_address);
        $stmt->bindParam(":f_name", $this->first_name);
        $stmt->bindParam(":l_name", $this->last_name);
        $stmt->bindParam(":date_of_birth", $this->dob);
     
        if($stmt->execute()){//201
            return array(true, null);
        }else{//500
            $errorInfo = $stmt->errorInfo();
            return array(false, $errorInfo[2]);
        }
    }

    // Delete a user
    function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        if($stmt->execute()){
            return array(true, null);
        }else{
            $errorInfo = $stmt->errorInfo();
            return array(false, $errorInfo[2]);
        }
    }

    // Used when filling up the update trip details form
    function read_one(){
        // query to read all record
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        if($stmt->rowCount() <= 0){
            return false;//204
        }
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->username = $row['username'];
        $this->id = $row['id'];
        $this->email_address = $row['email_address'];
        $this->access_level = $row['access_level'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->dob = $row['dob'];
        return true;
    }

    // update the user
    function update($update_password){
        // update query
        $query = "";
        if($update_password){
            $query = "UPDATE
                    " . $this->table_name . "
                SET
                    username=:user_name,
                    password=:pass,
                    salt=:salt,
                    access_level=:user_level,
                    email_address=:email,
                    first_name=:f_name,
                    last_name=:l_name,
                    dob=:date_of_birth
                WHERE
                    id = :id";
        }
        else{
            $query = "UPDATE
                    " . $this->table_name . "
                SET
                    username=:user_name,
                    access_level=:user_level,
                    email_address=:email,
                    first_name=:f_name,
                    last_name=:l_name,
                    dob=:date_of_birth
                WHERE
                    id = :id";
        }
        
     
        // prepare query statement
        $stmt = $this->conn->prepare($query);
     
        // bind values
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_name", $this->username);
        $stmt->bindParam(":user_level", $this->access_level);
        $stmt->bindParam(":email", $this->email_address);
        $stmt->bindParam(":f_name", $this->first_name);
        $stmt->bindParam(":l_name", $this->last_name);
        $stmt->bindParam(":date_of_birth", $this->dob);
        if($update_password){
            $stmt->bindParam(":pass", $this->password);
            $stmt->bindParam(":salt", $this->salt);
        }
        
        // execute the query
        if($stmt->execute()){//200
            return array(true, null);
        }else{//500
            $errorInfo = $stmt->errorInfo();
            return array(false, $errorInfo[2]);
        }
    }

    function sign_in(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE username=:user_name AND password=:pass";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(":user_name", $this->username);
        $stmt->bindParam(":pass", $this->password);
        $stmt->execute();
        if($stmt->rowCount() <= 0){//204
            return false;
        }
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->username = $row['username'];
        $this->id = $row['id'];
        $this->email_address = $row['email_address'];
        $this->access_level = $row['access_level'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->dob = $row['dob'];
        return true;
    }

    function get_salt(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        if($stmt->rowCount() <= 0){//204
            return;
        }
        // get retrieved salt
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->salt = $row['salt'];
    }
}
?>