<?php
session_start();
include 'connector.php';
include 'csrf_helper.php';

// Redirect jika sudah login
if (isset($_SESSION['username'])) {
    safeRedirect("landingPage.php");
}

$message = "";
$showTimeout = isset($_GET['timeout']) && $_GET['timeout'] == '1';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token. Refresh halaman dan coba lagi.";
    } else {
        $email_user = sanitizeInput($_POST['email']);
        $password = $_POST['password']; // Jangan trim password
        
        // Validasi input
        if (empty($email_user) || empty($password)) {
            $message = "Email dan password harus diisi";
        } elseif (!validateEmail($email_user)) {
            $message = "Format email tidak valid";
        } else {
            // Rate limiting bisa ditambahkan di sini
            
            $query = "SELECT * FROM user WHERE email_user = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email_user);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                
                if (password_verify($password, $row['password'])) {
                    // Regenerate session ID untuk mencegah session fixation
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['id_user'] = $row['id_user'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['email_user'] = $row['email_user'];
                    $_SESSION['photo'] = $row['photo'];
                    $_SESSION['initialized'] = true;
                    $_SESSION['last_activity'] = time();
                    
                    // Generate new CSRF token
                    regenerateCsrfToken();

                    mysqli_stmt_close($stmt);
                    safeRedirect("landingPage.php");
                } else {
                    $message = "Email atau password tidak valid";
                }
            } else {
                $message = "Email atau password tidak valid";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Generate CSRF token untuk form
$csrfToken = generateCsrfToken();
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
                    <p class="error-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <?php echo csrfTokenInput(); ?>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL</label>
                        <input type="email" id="email" name="email" class="form-input" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
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
    
    <script>
        // Cek notifikasi
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.get('registered') === 'success') {
            showNotification('✅', 'Registrasi berhasil! Silakan login.', 'success');
        }
        
        if (urlParams.get('timeout') === '1') {
            showNotification('⏱️', 'Sesi Anda telah berakhir. Silakan login kembali.', 'warning');
        }
        
        function showNotification(icon, text, type) {
            const notification = document.createElement('div');
            notification.className = type + '-notification';
            notification.innerHTML = `
                <span class="icon">${icon}</span>
                <span>${text}</span>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('hide');
                setTimeout(() => notification.remove(), 500);
            }, 5000);
            
            // Bersihkan URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>
</html>