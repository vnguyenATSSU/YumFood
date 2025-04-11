<?php
/*$host = "weblab.salemstate.edu";
$user = "yummy";
$pass = "yummy2025";
$db = "yummy"; */

$host = "localhost";
$user = "root";
$pass = "123456";
$db = "yfood";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Failed to connect to database: " . $conn->connect_error);
} 
?> 

