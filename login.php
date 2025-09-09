<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>

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
        }

        .container {
            display: flex; 
            width: 100%; 
            height: 100vh;
        }

        .signin-section {
            flex: 1; 
            background-color: #f8f9fa; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            padding: 40px;
        }

        .signin-form {
            width: 100%; 
            max-width: 400px;
        }

        .signin-title {
            font-size: 100px; 
            font-weight: 700; 
            color: #1a1a1a; 
            margin-bottom: 30px; 
            text-align: center;
        }

        .signin-title .highlight {
            color: rgb(75, 171, 255);
        }
        .signin-subtitle {
            font-size: 20px; 
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
        }

        .form-input:focus {
            outline: none; 
            background-color: #ddd;
        }

        .signin-button {
            width: 100%; 
            padding: 16px; 
            background-color: rgb(75, 171, 255); 
            color: white; 
            border: none; 
            border-radius: 25px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            margin-top: 20px; 
            text-transform: uppercase;
        }

        .signin-button:hover {
            background-color: #1976D2;
        }

        .forgot-password {
            text-align: center; 
            margin-top: 20px; 
            font-size: 14px; 
            color: #666; 
            cursor: pointer;
        }

        .forgot-password:hover {
            color: black;
        }

        .error-message {
            color: red; 
            text-align: center; 
            margin-top: 15px; 
            display: none;
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

        .signup-button {
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

        .signup-button:hover {
            background-color: white; 
            color: rgb(75, 171, 255);
        }

        @media (max-width: 768px) {.container {flex-direction: column;} .signin-section,.welcome-section {flex: none; height: auto; min-height: 50vh;} .signin-title {font-size: 36px;} .welcome-title {font-size: 48px;}}
    </style>
</head>
<body>
    <div class="container">
        <div class="signin-section">
            <div class="signin-form">
                <h1 class="signin-title">Sign <span class="highlight">in</span></h1>
                <p class="signin-subtitle">Jika anda telah memiliki akun,<br>gunakan akun anda</p>
                
                <form id="loginForm">
                    <div class="form-group">
                        <label class="form-label" for="email">EMAIL</label>
                        <input type="email" id="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">PASSWORD</label>
                        <input type="password" id="password" class="form-input" required>
                    </div>
                    
                    <button type="submit" class="signin-button">SIGN IN</button>
                    <p id="errorMessage" class="error-message">Email atau password salah!</p>
                </form>
                
                <p class="forgot-password">lupa kata sandi anda?</p>
            </div>
        </div>

        <div class="welcome-section">
            <h2 class="welcome-title">Halo, Kawan</h2>
            <p class="welcome-subtitle">Jika anda masih belum mempunyai akun,<br>daftarkan diri anda terlebih dahulu disini</p>
            <button class="signup-button">SIGN UP</button>
        </div>
    </div>

    <script>
        const validEmail = "admin@gmail.com";
        const validPassword = "12345";

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (email === validEmail && password === validPassword) {
                window.location.href = 'index.php';
            } else {
                document.getElementById('errorMessage').style.display = "block";
            }
        });

        document.querySelector('.signup-button').addEventListener('click', function() {
            window.location.href = 'signup.php';
        });
    </script>
</body>
</html>
