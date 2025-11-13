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

    // Validasi server-side
    $errors = [];

    if (empty($name)) {
        $errors[] = "Nama lengkap harus diisi";
    }

    if (empty($username)) {
        $errors[] = "Username harus diisi";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    }

    if (empty($email_user)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (empty($password)) {
        $errors[] = "Password harus diisi";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }

    if (!empty($errors)) {
        $message = implode("<br>", $errors);
    } else {
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
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO user (email_user, username, name, password) VALUES (?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, "ssss", $email_user, $username, $name, $hashedPassword);
            
            if (mysqli_stmt_execute($insertStmt)) {
                $message = "✅ Registrasi berhasil! Silakan login.";
                mysqli_stmt_close($insertStmt);
                header("refresh:2; url=index.php");
                exit();
            } else {
                $message = "Terjadi kesalahan saat registrasi";
                mysqli_stmt_close($insertStmt);
            }
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
                    <p class="<?= strpos($message, 'berhasil') !== false || strpos($message, '✅') !== false ? 'success-message' : 'error-message' ?> message">
                        <?= $message ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="name">NAMA LENGKAP <span style="color: #e74c3c;">*</span></label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            placeholder="Contoh: John Doe"
                            required 
                            minlength="3"
                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="username">USERNAME <span style="color: #e74c3c;">*</span></label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            placeholder="Contoh: johndoe123"
                            required 
                            minlength="3"
                            pattern="[A-Za-z0-9_]+"
                            title="Hanya huruf, angka, dan underscore"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL <span style="color: #e74c3c;">*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="Contoh: john@example.com"
                            required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">PASSWORD <span style="color: #e74c3c;">*</span></label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Minimal 8 karakter"
                            required 
                            minlength="8"
                        >
                    </div>
                    <button type="submit" class="signup-button">DAFTAR</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>