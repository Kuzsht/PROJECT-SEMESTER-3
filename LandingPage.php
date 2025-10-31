<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AIRtix.id - Pesan Tiket Pesawat Mudah</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Space Grotesk", sans-serif;
    }

    body {
      background: #f8f9fa;
      color: #1a1a1a;
      overflow-x: hidden;
    }

    /* Subtle Background */
    .bg-decorations {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
      pointer-events: none;
    }

    .decoration-circle {
      position: absolute;
      border-radius: 50%;
      opacity: 0.04;
    }

    .decoration-circle:nth-child(1) {
      width: 600px;
      height: 600px;
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      top: -200px;
      right: -200px;
    }

    .decoration-circle:nth-child(2) {
      width: 400px;
      height: 400px;
      background: linear-gradient(225deg, #1976D2, rgb(75, 171, 255));
      bottom: -150px;
      left: -150px;
    }

    /* Navbar Clean Premium */
    header {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 8px 32px rgba(255, 255, 255, 0.3);
    }

    .logo-link {
      text-decoration: none;
      color: white;
      transition: opacity 0.3s ease;
      cursor: pointer;
    }

    .logo-link:hover {
      opacity: 0.85;
    }

    .logo-link h1 {
      font-size: 36px;
      font-weight: 800;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.2);
      letter-spacing: -1px;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 12px;
      align-items: center;
    }

    nav a, .username-btn {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 12px 22px;
      border-radius: 15px;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      cursor: pointer;
      display: inline-block;
    }

    nav a:hover, .username-btn:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 255, 255, 1);
    }

    .username-btn {
      font-weight: 700;
      padding: 12px 26px;
      background: rgba(255,255,255,0.25);
      border: 2px solid rgba(255,255,255,0.3);
    }

    .logout-btn {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      padding: 12px 26px;
      border-radius: 25px;
      border: 2px solid rgba(255,255,255,0.2);
      box-shadow: 0 4px 20px rgba(231, 76, 60, 0.4);
    }

    .logout-btn:hover {
      box-shadow: 0 8px 30px rgba(255, 0, 0, 1);
      background: white;
      color:rgba(255, 0, 0, 0.5)
    }

    /* Hero Premium */
    .hero {
      min-height: 90vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: 
        linear-gradient(135deg, rgba(75, 171, 255, 0.96), rgba(25, 118, 210, 0.96)),
        url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1600') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 60px 20px;
      position: relative;
      overflow: hidden;
      margin-bottom: 60px;
    }

    .hero::after {
      content: '';
      position: absolute;
      bottom: -20px;
      left: 0;
      width: 100%;
      height: 70px;
      background: linear-gradient(to top, #f8f9fa, transparent);
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 900px;
    }

    .hero h2 {
      font-size: 82px;
      font-weight: 800;
      margin-bottom: 30px;
      text-shadow: 4px 4px 15px rgba(0,0,0,0.3);
      line-height: 1.1;
      letter-spacing: -3px;
    }

    .hero p {
      font-size: 28px;
      margin-bottom: 50px;
      opacity: 0.95;
      font-weight: 300;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.2);
      line-height: 1.5;
    }

    .hero-btn {
      padding: 24px 70px;
      background: white;
      border: none;
      border-radius: 50px;
      font-size: 20px;
      font-weight: 700;
      color: rgb(75, 171, 255);
      cursor: pointer;
      transition: all 0.4s ease;
      text-transform: uppercase;
      letter-spacing: 2px;
      box-shadow: 0 15px 50px rgba(255, 255, 255, 0.4);
    }

    .hero-btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 60px rgba(255, 255, 255, 0.7);
      background: #f8f9fa;
    }

    /* Stats Premium */
    .stats-wrapper {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      padding: 100px 40px;
      margin: -60px 0 120px;
      color: white;
      position: relative;
      box-shadow: 0 30px 80px rgba(75, 171, 255, 0.3);
    }

    .stats-wrapper::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0.02),
        rgba(255,255,255,0.02) 10px,
        transparent 10px,
        transparent 20px
      );
    }

    .stats-container {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 50px;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .stat-box {
      padding: 40px 30px;
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(10px);
      border-radius: 25px;
      border: 2px solid rgba(255,255,255,0.25);
      transition: all 0.4s ease;
    }

    .stat-box:hover {
      transform: translateY(-10px);
      background: rgba(255,255,255,0.18);
      box-shadow: 0 20px 50px rgba(255, 255, 255, 1);
    }

    .stat-number {
      font-size: 64px;
      font-weight: 800;
      margin-bottom: 15px;
      text-shadow: 3px 3px 12px rgba(0,0,0,0.2);
    }

    .stat-label {
      font-size: 18px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
      opacity: 0.95;
    }

    /* Section Title Premium */
    .section-title {
      text-align: center;
      font-size: 64px;
      font-weight: 800;
      margin: 0 0 90px;
      color: rgb(75, 171, 255);
      position: relative;
      padding-bottom: 40px;
      letter-spacing: -2px;
    }

    .section-title::before {
      content: attr(data-text);
      position: absolute;
      left: 50%;
      top: -20px;
      transform: translateX(-50%);
      font-size: 90px;
      color: transparent;
      -webkit-text-stroke: 2px rgba(75, 171, 255, 0.06);
      z-index: -1;
      font-weight: 900;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 180px;
      height: 6px;
      background: linear-gradient(90deg, transparent, rgb(75, 171, 255), #1976D2, transparent);
      border-radius: 3px;
      box-shadow: 0 4px 20px rgba(75, 171, 255, 0.5);
    }

    /* Features Ultra Premium */
    .features {
      padding: 0 40px 140px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
      gap: 20px;
      max-width: 1500px;
      margin: auto;
    }

    .feature-card {
      background: white;
      border-radius: 35px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      padding: 60px 45px;
      text-align: center;
      transition: all 0.5s ease;
      position: relative;
      border: 3px solid rgba(75, 171, 255, 0.08);
      width: 350px;
    }

    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      background: linear-gradient(90deg, rgb(75, 171, 255), #1976D2);
      border-radius: 35px 35px 0 0;
      opacity: 0;
      transition: opacity 0.4s;
    }

    .feature-card:hover::before {
      opacity: 0;
    }

    .feature-card:hover {
      transform: translateY(-20px);
      box-shadow: 0 35px 80px rgba(75, 171, 255, 0.25);
      border-color: rgb(75, 171, 255);
    }

    .feature-icon {
      font-size: 90px;
      margin-bottom: 30px;
      display: inline-block;
      filter: drop-shadow(0 15px 30px rgba(75, 171, 255, 0.3));
    }

    .feature-card h3 {
      font-size: 25px;
      margin-bottom: 25px;
      color: #1976D2;
      font-weight: 800;
      letter-spacing: -1px;
    }

    .feature-card p {
      font-size: 14px;
      color: #666;
      margin-bottom: 40px;
      line-height: 1.8;
    }

    .feature-btn {
      display: inline-block;
      padding: 18px 50px;
      background: linear-gradient(135deg, rgb(75,171,255) 0%, #1976D2 100%);
      color: white;
      border-radius: 40px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.4s ease;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      box-shadow: 0 10px 35px rgba(75, 171, 255, 0.4);
      font-size: 15px;
    }

    .feature-btn:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 45px rgba(75, 171, 255, 0.6);
    }

    /* Footer */
    footer {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: #ccc;
      text-align: center;
      padding: 45px;
      margin-top: 0;
      font-size: 16px;
      position: relative;
    }

    footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgb(75, 171, 255), transparent);
    }

    footer p {
      font-weight: 600;
    }

    @media (max-width: 968px) {
      header {
        flex-direction: column;
        gap: 18px;
        padding: 20px;
      }

      .logo-link h1 {
        font-size: 28px;
      }

      nav ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
      }

      .hero h2 {
        font-size: 44px;
      }

      .hero p {
        font-size: 20px;
      }

      .section-title {
        font-size: 38px;
        margin: 50px 0 50px;
      }

      .features {
        padding: 0 20px 70px;
        gap: 35px;
      }

      .feature-card {
        padding: 45px 35px;
      }

      .stats-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
      }
    }
  </style>
</head>
<body>
  <div class="bg-decorations">
    <div class="decoration-circle"></div>
    <div class="decoration-circle"></div>
  </div>

  <header>
    <a href="LandingPage.php" class="logo-link">
      <h1>✈️ AIRtix.id</h1>
    </a>
    <nav>
      <ul>
        <li><a href="profile.php" class="username-btn">👋 <?php echo htmlspecialchars($username); ?></a></li>
        <li><a href="history.php">📋 Riwayat</a></li>
        <li><a href="checkin.php">✅ Check-in</a></li>
        <li><a class="logout-btn" href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h2>Terbang Lebih Mudah, Hidup Lebih Indah</h2>
      <p>Pesan tiket pesawat ke seluruh dunia dengan harga terbaik dan layanan terpercaya</p>
      <button class="hero-btn" onclick="window.location.href='search.php'">🚀 Mulai Pesan Sekarang</button>
    </div>
  </section>

  <section class="stats-wrapper">
    <div class="stats-container">
      <div class="stat-box">
        <div class="stat-number">1000+</div>
        <div class="stat-label">Penerbangan</div>
      </div>
      <div class="stat-box">
        <div class="stat-number">50+</div>
        <div class="stat-label">Maskapai</div>
      </div>
      <div class="stat-box">
        <div class="stat-number">100+</div>
        <div class="stat-label">Destinasi</div>
      </div>
      <div class="stat-box">
        <div class="stat-number">10K+</div>
        <div class="stat-label">Pelanggan Puas</div>
      </div>
    </div>
  </section>

  <h2 class="section-title" data-text="LAYANAN">Layanan AIRtix.id</h2>

  <section class="features">
    <div class="feature-card">
      <div class="feature-icon">🎫</div>
      <h3>Pesan Tiket</h3>
      <p>Cari dan pesan tiket pesawat sesuai tujuan, tanggal, dan dapatkan harga terbaik untuk perjalanan Anda.</p>
      <a href="search.php" class="feature-btn">Pesan Sekarang</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">📋</div>
      <h3>Riwayat Pemesanan</h3>
      <p>Lihat dan kelola semua riwayat pemesanan tiket Anda dengan mudah dalam satu tempat.</p>
      <a href="history.php" class="feature-btn">Lihat Riwayat</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">✅</div>
      <h3>Check-in Online</h3>
      <p>Hemat waktu Anda dengan melakukan check-in secara online sebelum keberangkatan.</p>
      <a href="checkin.php" class="feature-btn">Check-in Now</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">👤</div>
      <h3>Kelola Profil</h3>
      <p>Atur informasi akun dan preferensi perjalanan Anda untuk pengalaman yang lebih personal.</p>
      <a href="profile.php" class="feature-btn">Lihat Profil</a>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>