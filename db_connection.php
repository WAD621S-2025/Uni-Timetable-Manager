<?php

$host = "localhost";
$dbname = "campus_connect";
$username = "root";
$password = ""; 
$conn = new mysqli($host ,$dbname ,$username ,$password);

if($conn->connect_error) {
    die("Connection failed: ".$conn->connect_error);
}

?>
