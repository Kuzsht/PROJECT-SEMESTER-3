<?php
session_start();
include 'connector.php';
include 'csrfHelper.php';

// Redirect jika sudah login
if (isset($_SESSION['username'])) {
    safeRedirect("landingPage.php");
}

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token. Refresh halaman dan coba lagi.";
    } else {
        $name = sanitizeInput($_POST['name']);
        $email_user = sanitizeInput($_POST['email']);
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password']; // Jangan sanitize password

        // Validasi server-side
        $errors = [];

        // Validasi nama
        if (empty($name)) {
            $errors[] = "Nama lengkap harus diisi";
        } elseif (strlen($name) < 3) {
            $errors[] = "Nama minimal 3 karakter";
        } elseif (strlen($name) > 100) {
            $errors[] = "Nama maksimal 100 karakter";
        }

        // Validasi username
        if (empty($username)) {
            $errors[] = "Username harus diisi";
        } elseif (strlen($username) < 3) {
            $errors[] = "Username minimal 3 karakter";
        } elseif (strlen($username) > 50) {
            $errors[] = "Username maksimal 50 karakter";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "Username hanya boleh huruf, angka, dan underscore";
        }

        // Validasi email
        if (empty($email_user)) {
            $errors[] = "Email harus diisi";
        } elseif (!validateEmail($email_user)) {
            $errors[] = "Format email tidak valid";
        }

        // Validasi password
        if (empty($password)) {
            $errors[] = "Password harus diisi";
        } else {
            $passwordErrors = validatePasswordStrength($password);
            $errors = array_merge($errors, $passwordErrors);
        }

        if (!empty($errors)) {
            $message = implode("<br>", $errors);
        } else {
            // Cek apakah username atau email sudah ada
            $checkQuery = "SELECT id_user FROM user WHERE username = ? OR email_user = ?";
            $checkStmt = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "ss", $username, $email_user);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_store_result($checkStmt);
            
            if (mysqli_stmt_num_rows($checkStmt) > 0) {
                $message = "Username atau email sudah digunakan";
                mysqli_stmt_close($checkStmt);
            } else {
                mysqli_stmt_close($checkStmt);
                
                // Hash password dengan Argon2ID (lebih aman)
                $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
                
                $insertQuery = "INSERT INTO user (email_user, username, name, password) VALUES (?, ?, ?, ?)";
                $insertStmt = mysqli_prepare($conn, $insertQuery);
                mysqli_stmt_bind_param($insertStmt, "ssss", $email_user, $username, $name, $hashedPassword);
                
                if (mysqli_stmt_execute($insertStmt)) {
                    mysqli_stmt_close($insertStmt);
                    
                    // Regenerate CSRF token setelah registrasi
                    regenerateCsrfToken();
                    
                    safeRedirect("index.php?registered=success");
                } else {
                    $message = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
                    error_log("Registration failed: " . mysqli_error($conn));
                    mysqli_stmt_close($insertStmt);
                }
            }
        }
    }
}

// Generate CSRF token
$csrfToken = generateCsrfToken();
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
    <link rel="stylesheet" href="styles/signUp.css">
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
                    <p class="error-message message">
                        <?= $message ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="" id="signupForm">
                    <?php echo csrfTokenInput(); ?>
                    
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
                            maxlength="100"
                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>"
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
                            maxlength="50"
                            pattern="[A-Za-z0-9_]+"
                            title="Hanya huruf, angka, dan underscore"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>"
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
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">PASSWORD <span style="color: #e74c3c;">*</span></label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Min 8 karakter (huruf besar, kecil, angka)"
                            required 
                            minlength="8"
                        >
                        <small style="color: #666; font-size: 0.85em; margin-top: 5px; display: block;">
                            Password harus minimal 8 karakter dengan huruf besar, kecil, dan angka
                        </small>
                    </div>
                    
                    <button type="submit" class="signup-button">DAFTAR</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Client-side validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password minimal 8 karakter!');
                return false;
            }
            
            if (!/[A-Z]/.test(password)) {
                e.preventDefault();
                alert('Password harus mengandung minimal 1 huruf besar!');
                return false;
            }
            
            if (!/[a-z]/.test(password)) {
                e.preventDefault();
                alert('Password harus mengandung minimal 1 huruf kecil!');
                return false;
            }
            
            if (!/[0-9]/.test(password)) {
                e.preventDefault();
                alert('Password harus mengandung minimal 1 angka!');
                return false;
            }
        });
    </script>
</body>
</html>