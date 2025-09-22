<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil - AIRtix.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Space Grotesk', sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: #f5f9ff;
      color: #333;
    }

    header {
      background: rgb(75, 171, 255);
      padding: 15px 40px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      font-size: 1.5rem;
    }

    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }

    nav a:hover {
      text-decoration: underline;
      color: rgb(0, 119, 182);
    }

    main {
      flex: 1;
      padding: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .profile-card {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 500px;
    }

    .profile-card h2 {
      margin-bottom: 20px;
      color: rgb(75, 171, 255);
      text-align: center;
    }

    .profile-item {
      margin-bottom: 15px;
    }

    .profile-item label {
      display: block;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .profile-item p {
      background: #f1f7ff;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #dce9f9;
    }

    footer {
      background: #1a1a1a;
      color: #ccc;
      text-align: center;
      padding: 20px;
      margin-top: auto;
    }
  </style>
</head>
<body>
  <header>
    <h1>AIRtix.id</h1>
    <nav>
      <a href="LandingPage.php">Beranda</a>
      <a href="search.php">Pesan Tiket</a>
      <a href="history.php">Riwayat</a>
      <a href="checkin.php">Check-in</a>
    </nav>
  </header>

  <main>
    <div class="profile-card">
      <h2>Profil Pengguna</h2>

      <div class="profile-item">
        <label>Username:</label>
        <p>Admin</p>
      </div>

      <div class="profile-item">
        <label>Nama Lengkap:</label>
        <p>Admin FILKOM</p>
      </div>

      <div class="profile-item">
        <label>Email:</label>
        <p>admin@gmail.com</p>
      </div>

      <div class="profile-item">
        <label>Password:</label>
        <p>*******</p>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>
</body>
</html>
