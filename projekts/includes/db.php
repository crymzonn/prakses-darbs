<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "instagram_clone";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Savienojuma kļūda: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
