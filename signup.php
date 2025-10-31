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

    $checkUser = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' OR email_user='$email_user'");
    if (mysqli_num_rows($checkUser) > 0) {
        $message = "username sudah ada, gunakan username lainnya";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO user (email_user, username, name, password) 
                                       VALUES ('$email_user', '$username', '$name', '$password')");
        if ($insert) {
            $message = "Registrasi berhasil! Silakan login.";
            header("refresh:2; url=index.php");
            exit();
        } else {
            $message = "Terjadi kesalahan saat registrasi: " . mysqli_error($conn);
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Space Grotesk", sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
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
            font-size: 80px;
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

        .signin-button {
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

        .signin-button:hover {
            background-color: white;
            color: rgb(75, 171, 255);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,255,255,0.3);
        }

        .signup-section {
            flex: 1;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .signup-form {
            width: 100%;
            max-width: 480px;
            background: white;
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            animation: slideInRight 0.8s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .signup-title {
            font-size: 60px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
            text-align: center;
        }

        .signup-title .highlight {
            color: rgb(75, 171, 255);
        }

        .signup-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 35px;
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
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background-color: #f8f9fa;
            font-size: 15px;
            color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: rgb(75, 171, 255);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(75, 171, 255, 0.1);
            transform: translateY(-2px);
        }

        .signup-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(75, 171, 255, 0.4);
        }

        .signup-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(75, 171, 255, 0.5);
        }

        .signup-button:active {
            transform: translateY(0);
        }

        .message {
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

        .error-message {
            background: #fee;
            color: #c33;
            border: 2px solid #fcc;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            border: 2px solid #cfc;
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column-reverse;
            }
            .welcome-section, .signup-section {
                flex: none;
                min-height: 50vh;
            }
            .signup-title {
                font-size: 42px;
            }
            .welcome-title {
                font-size: 48px;
            }
            .signup-form {
                padding: 35px 25px;
            }
        }
    </style>
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
                    <p style="color:<?= strpos($message, 'berhasil') !== false ? 'green' : 'red'; ?>;">
                        <?= $message ?>
                    </p>
                    <br>
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