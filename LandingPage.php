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
  <link rel="stylesheet" href="styles/LandingPage.css">
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