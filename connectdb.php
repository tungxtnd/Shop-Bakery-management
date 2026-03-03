<?php 
// Database connection settings
$servername = "localhost";
$username = "root";
$pass = "03012004";
$dbname = "ql_flower";
$conn = new mysqli($servername, $username, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>