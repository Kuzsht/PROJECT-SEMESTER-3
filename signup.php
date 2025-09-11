<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>

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
        }

        .container {
            display: flex; 
            width: 100%; 
            min-height: 100vh;
        }

        .welcome-section {
            flex: 1;
            background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: white;
            text-align: center;
        }

        .welcome-title {
            font-size: 85px; 
            font-weight: 700; 
            margin-bottom: 30px;
        }

        .welcome-subtitle {
            font-size: 18px; 
            line-height: 1.6; 
            margin-bottom: 40px; 
            opacity: 0.9;
        }

        .signin-button {
            padding: 16px 40px;
            background-color: transparent;
            color: white;
            border: 2px solid white;
            border-radius: 70px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .signin-button:hover {
            background-color: white; 
            color: rgb(75, 171, 255);
        }

        .signup-section {
            flex: 1;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .signup-form {
            width: 100%; 
            max-width: 400px;
        }

        .signup-title {
            font-size: 80px; 
            font-weight: 700; 
            color: #1a1a1a; 
            margin-bottom: 30px; 
            text-align: center;
        }

        .signup-title .highlight {
            color: rgb(75, 171, 255);
        }

        .signup-subtitle {
            font-size: 18px; 
            color: #666; 
            margin-bottom: 40px; 
            line-height: 1.5; 
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block; 
            font-size: 14px; 
            font-weight: 600; 
            color: #1a1a1a; 
            margin-bottom: 8px; 
            text-transform: uppercase;
        }

        .form-input {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            background-color: #e5e5e5;
            font-size: 16px;
            color: #1a1a1a;
            transition: background-color 0.3s ease;
        }

        .form-input:focus {
            outline: none; 
            background-color: #ddd;
        }

        .signup-button {
            width: 100%;
            padding: 16px;
            background-color: rgb(75, 171, 255);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            text-transform: uppercase;
        }

        .signup-button:hover {
            background-color: #1976D2;
        }

        .error-message {
            color: red; 
            font-size: 14px; 
            text-align: center; 
            margin-top: 10px; 
            display: none;
        }

        @media (max-width: 768px) {
            .container {flex-direction: column-reverse;}
            .welcome-section, .signup-section {flex: none; height: auto; min-height: 50vh;}
            .signup-title {font-size: 36px;}
            .welcome-title {font-size: 48px;}
            .signup-section, .welcome-section {padding: 20px;}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <h2 class="welcome-title">Selamat Datang</h2>
            <p class="welcome-subtitle">
                Jika anda sudah memiliki akun,<br>
                silakan masuk melalui halaman login
            </p>
            <button class="signin-button" onclick="window.location.href='login.php'">SIGN IN</button>
        </div>

        <div class="signup-section">
            <div class="signup-form">
                <h1 class="signup-title">Sign <span class="highlight">up</span></h1>
                <p class="signup-subtitle">Silakan isi form berikut untuk membuat akun baru</p>

                <form id="signupForm" method="post" action="signup.php">
                    <div class="form-group">
                        <label class="form-label" for="name">NAMA LENGKAP</label>
                        <input type="text" id="name" name="name" class="form-input" required>
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
                    <p id="errorMessage" class="error-message">Semua field wajib diisi!</p>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const errorMessage = document.getElementById('errorMessage');

            if (!name || !email || !password) {
                errorMessage.style.display = "block";
            } else {
                errorMessage.style.display = "none";

                alert("Pendaftaran berhasil! Silakan login.");
                window.location.href = "login.php";
            }
        });
    </script>
</body>
</html>
