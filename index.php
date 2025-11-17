<?php
session_start();
include 'connector.php';

if (isset($_SESSION['username'])) {
    header("Location: landingPage.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_user = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM user WHERE email_user = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email_user'] = $row['email_user'];

            // Ncegah fixation 
            session_regenerate_id(true);

            mysqli_stmt_close($stmt);
            header("Location: landingPage.php");
            exit();
        } else {
            $message = "Email atau password tidak valid";
        }
    } else {
        $message = "Email atau password tidak valid";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - AIRtix.id</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
    <div class="container">
        <div class="signin-section">
            <div class="signin-form">
                <h1 class="signin-title">Sign <span class="highlight">in</span></h1>
                <p class="signin-subtitle">Selamat datang kembali!<br>Masuk untuk melanjutkan perjalanan Anda</p>
                
                <?php if ($message): ?>
                    <p class="error-message"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                    
                    <button type="submit" class="signin-button">SIGN IN</button>
                </form>
                
                <p class="forgot-password">Lupa kata sandi Anda?</p>
            </div>
        </div>

        <div class="welcome-section">
            <div class="welcome-content">
                <h2 class="welcome-title">Halo, Kawan!</h2>
                <p class="welcome-subtitle">
                    Belum memiliki akun AIRtix.id?<br>
                    Daftarkan diri Anda untuk memulai petualangan
                </p>
                <button class="signup-button" onclick="window.location.href='signUp.php'">SIGN UP</button>
            </div>
        </div>
    </div>
</body>
</html>