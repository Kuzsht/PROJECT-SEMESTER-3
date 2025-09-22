<?php
session_start();

// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect kembali ke halaman login
header("Location: index.php");
exit();
