<?php
class Dbc {

 private $host; 
 private $username;
 private $password;
 private $dbname;
 public $conn;

 public function connect(){
 
    $this->host = "ablaikhan.kz";
    $this->username = "bahythan";
    $this->password = "3azr4eeq";
    $this->dbname = "www_portal";
 
	$conn = new mysqli($this->host, $this->username, $this->password, $this->dbname); 	
 	if(!$conn){
      echo "Failed connect to MySQL: " . mysqli_connect_error();
    }else{
    	//echo "Successfully connected";
    	return $conn;
    }
 
 }

}


?>