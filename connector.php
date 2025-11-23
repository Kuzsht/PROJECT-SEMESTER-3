<?php
// Konfigurasi database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$database = "airtix.id";

// Koneksi dengan error handling yang lebih baik
$conn = mysqli_connect($servername, $username_db, $password_db, $database);

if (!$conn) {
    // Jangan expose detail error di production
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Koneksi database gagal. Silakan hubungi administrator.");
}

// Set charset untuk mencegah encoding issues
mysqli_set_charset($conn, "utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>