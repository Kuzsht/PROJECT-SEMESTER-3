<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AIRtix.id - Landing Page</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: "Space Grotesk", sans-serif;
    }

    body {
      background: #f8f9fa;
      color: #1a1a1a;
    }

    /* Navbar */
    header {
      background: rgb(75, 171, 255);
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header h1 {
      font-size: 28px;
      font-weight: 700;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      align-items: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: opacity 0.3s ease;
    }

    nav a:hover {
      opacity: 0.8;
    }

    .username {
      font-weight: 600;
      margin-right: 20px;
    }

    .logout-btn {
      background: #e74c3c;
      padding: 8px 16px;
      border-radius: 20px;
      text-decoration: none;
      color: white;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .logout-btn:hover {
      background: #c0392b;
    }

    /* Hero Section */
    .hero {
      height: 90vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: url('hero-plane.jpg') no-repeat center center/cover;
      color: rgb(75, 171, 255);
      text-align: center;
      padding: 20px;
    }

    .hero h2 {
      font-size: 64px;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 20px;
      margin-bottom: 30px;
    }

    .hero button {
      padding: 16px 40px;
      background: rgb(75, 171, 255);
      border: none;
      border-radius: 50px;
      font-size: 16px;
      font-weight: 600;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .hero button:hover {
      background: #1976D2;
    }

    /* Features */
    .features {
      padding: 0px 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      max-width: 50%;
      margin: auto;
      margin-top: 50px;
    }

    .feature-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 30px;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .feature-card:hover {
      transform: translateY(-8px);
    }

    .feature-card h3 {
      font-size: 22px;
      margin-bottom: 15px;
      color: #1976D2;
    }

    .feature-card p {
      font-size: 15px;
      color: #555;
      margin-bottom: 20px;
    }

    .feature-card a {
      display: inline-block;
      padding: 10px 24px;
      background: rgb(75,171,255);
      color: white;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .feature-card a:hover {
      background: #1976D2;
    }

    /* Footer */
    footer {
      background: #1a1a1a;
      color: #ccc;
      text-align: center;
      padding: 20px;
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <header>
    <h1>AIRtix.id</h1>
    <nav>
      <ul>
        <li class="username">Halo, <?php echo $_SESSION["username"]; ?></li>
        <li><a href="search.php">Pesan Tiket</a></li>
        <li><a href="history.php">Riwayat</a></li>
        <li><a href="checkin.php">Check-in</a></li>
        <li><a href="profile.php">Profil</a></li>
        <li><a class="logout-btn" href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <h2>Pesan Tiket Pesawat dengan Mudah</h2>
    <p>AIRtix.id hadir untuk memudahkan perjalanan Anda</p>
    <button onclick="window.location.href='search.php'">Mulai Pesan</button>
  </section>

    <h2 style="text-align:center; font-size:36px; font-weight:700; margin:50px 0 30px; color:#1976D2;">Layanan AIRtix.id</h2>

  <!-- Features -->
  <section class="features">
    <div class="feature-card">
      <h3>Pesan Tiket</h3>
      <p>Cari dan pesan tiket pesawat sesuai tujuan, tanggal, dan harga terbaik.</p>
      <a href="search.php">Pergi</a>
    </div>
    <div class="feature-card">
      <h3>Riwayat</h3>
      <p>Lihat dan kelola riwayat pemesanan tiket Anda.</p>
      <a href="history.php">Pergi</a>
    </div>
    <div class="feature-card">
      <h3>Check-in Online</h3>
      <p>Hemat waktu dengan check-in secara online.</p>
      <a href="checkin.php">Pergi</a>
    </div>
    <div class="feature-card">
      <h3>Profil</h3>
      <p>Atur informasi akun dan preferensi perjalanan Anda.</p>
      <a href="profile.php">Pergi</a>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>
</body>
</html>
