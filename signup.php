<?php
session_start();
include 'connector.php';

if (isset($_SESSION['username'])) {
    header("Location: LandingPage.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email_user = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Cek apakah username atau email sudah ada menggunakan prepared statement
    $checkQuery = "SELECT id_user FROM user WHERE username = ? OR email_user = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ss", $username, $email_user);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    
    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $message = "Username atau email sudah digunakan, gunakan yang lain";
        mysqli_stmt_close($checkStmt);
    } else {
        mysqli_stmt_close($checkStmt);
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user baru dengan prepared statement
        $insertQuery = "INSERT INTO user (email_user, username, name, password) VALUES (?, ?, ?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssss", $email_user, $username, $name, $hashedPassword);
        
        if (mysqli_stmt_execute($insertStmt)) {
            $message = "Registrasi berhasil! Silakan login.";
            mysqli_stmt_close($insertStmt);
            header("refresh:2; url=index.php");
            exit();
        } else {
            $message = "Terjadi kesalahan saat registrasi";
            mysqli_stmt_close($insertStmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AIRtix.id</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/signup.css">
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <div class="welcome-content">
                <h2 class="welcome-title">Selamat Datang</h2>
                <p class="welcome-subtitle">
                    Sudah memiliki akun AIRtix.id?<br>
                    Silakan masuk untuk melanjutkan perjalanan Anda
                </p>
                <button class="signin-button" onclick="window.location.href='index.php'">SIGN IN</button>
            </div>
        </div>

        <div class="signup-section">
            <div class="signup-form">
                <h1 class="signup-title">Sign <span class="highlight">up</span></h1>
                <p class="signup-subtitle">Daftarkan diri Anda untuk memulai perjalanan bersama AIRtix.id</p>

                <?php if ($message): ?>
                    <p class="<?= strpos($message, 'berhasil') !== false ? 'success-message' : 'error-message' ?> message">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="name">NAMA LENGKAP</label>
                        <input type="text" id="name" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="username">USERNAME</label>
                        <input type="text" id="username" name="username" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                    <button type="submit" class="signup-button">DAFTAR</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>