<?php 
// Database connection settings
$servername = "localhost";
$username = "root";
$pass = "";
$dbname = "ql_bakery";
$conn = new mysqli($servername, $username, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>