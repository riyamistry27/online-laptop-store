<?php
$host     = "localhost";
$username = "root";
$password = "Mca@2026";
$dbname   = "laptop_store";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>