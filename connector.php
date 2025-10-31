<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$database = "airtix.id";

$conn = mysqli_connect($servername, $username_db, $password_db, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
