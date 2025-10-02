<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['email'])) {
    header("Location: LandingPage.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email !== '' && $password !== '') {
        $file = __DIR__ . "/users.txt";
        $found = false;
        $userData = [];
        
        if (file_exists($file)) {
            // Menggunakan fgets untuk membaca file per baris
            $fh = fopen($file, "r");
            if ($fh) {
                while (($line = fgets($fh)) !== false) {
                    $line = trim($line);
                    if ($line === '') continue;
                    
                    // Format: email|username|name|password
                    $parts = explode("|", $line);
                    if (count($parts) >= 4) {
                        $fEmail = trim($parts[0]);
                        $fUsername = trim($parts[1]);
                        $fName = trim($parts[2]);
                        $fPass = trim($parts[3]);
                        
                        if (strcasecmp($fEmail, $email) === 0 && $fPass === $password) {
                            $found = true;
                            $userData = [
                                'email' => $fEmail,
                                'username' => $fUsername,
                                'name' => $fName
                            ];
                            break;
                        }
                    }
                }
                fclose($fh);
            }
        }

        if ($found) {
            $_SESSION['email'] = $userData['email'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['name'] = $userData['name'];
            header("Location: LandingPage.php");
            exit();
        } else {
            $error = "Email atau password salah!";
        }
    } else {
        $error = "Email dan password wajib diisi!";
    }
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Space Grotesk", sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .signin-section {
            flex: 1;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
        }

        .signin-form {
            width: 100%;
            max-width: 480px;
            background: white;
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            animation: slideInLeft 0.8s ease;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .signin-title {
            font-size: 70px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
            text-align: center;
        }

        .signin-title .highlight {
            color: rgb(75, 171, 255);
        }

        .signin-subtitle {
            font-size: 17px;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background-color: #f8f9fa;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: rgb(75, 171, 255);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(75, 171, 255, 0.1);
            transform: translateY(-2px);
        }

        .signin-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(75, 171, 255, 0.4);
        }

        .signin-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(75, 171, 255, 0.5);
        }

        .signin-button:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: rgb(75, 171, 255);
        }

        .error-message {
            background: #fee;
            color: #c33;
            border: 2px solid #fcc;
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-section {
            flex: 1;
            background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-title {
            font-size: 75px;
            font-weight: 700;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            animation: fadeInDown 1s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-subtitle {
            font-size: 20px;
            line-height: 1.8;
            margin-bottom: 50px;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.3s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .signup-button {
            padding: 18px 50px;
            background-color: transparent;
            color: white;
            border: 3px solid white;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .signup-button:hover {
            background-color: white;
            color: rgb(75, 171, 255);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,255,255,0.3);
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column;
            }
            .signin-section,
            .welcome-section {
                flex: none;
                height: auto;
                min-height: 50vh;
            }
            .signin-title {
                font-size: 42px;
            }
            .welcome-title {
                font-size: 48px;
            }
            .signin-form {
                padding: 35px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signin-section">
            <div class="signin-form">
                <h1 class="signin-title">Sign <span class="highlight">in</span></h1>
                <p class="signin-subtitle">Selamat datang kembali!<br>Masuk untuk melanjutkan perjalanan Anda</p>
                
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
                    
                    <?php if ($error): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
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
                <button class="signup-button" onclick="window.location.href='signup.php'">SIGN UP</button>
            </div>
        </div>
    </div>
</body>
</html>