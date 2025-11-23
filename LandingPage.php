<?php
session_start();
include 'connector.php';
include 'csrf_helper.php';

// Cek login
requireLogin();
initSecureSession();

$username = $_SESSION['username'];

// Query untuk mendapatkan statistik dinamis dengan prepared statements
// Total Pemesanan
$queryPemesanan = "SELECT COUNT(*) as total FROM pemesanan";
$stmtPemesanan = mysqli_prepare($conn, $queryPemesanan);
mysqli_stmt_execute($stmtPemesanan);
$resultPemesanan = mysqli_stmt_get_result($stmtPemesanan);
$totalPemesanan = mysqli_fetch_assoc($resultPemesanan)['total'];
mysqli_stmt_close($stmtPemesanan);

// Total Maskapai
$queryMaskapai = "SELECT COUNT(*) as total FROM maskapai";
$stmtMaskapai = mysqli_prepare($conn, $queryMaskapai);
mysqli_stmt_execute($stmtMaskapai);
$resultMaskapai = mysqli_stmt_get_result($stmtMaskapai);
$totalMaskapai = mysqli_fetch_assoc($resultMaskapai)['total'];
mysqli_stmt_close($stmtMaskapai);

// Total Tiket/Penerbangan
$queryTiket = "SELECT COUNT(*) as total FROM tiket";
$stmtTiket = mysqli_prepare($conn, $queryTiket);
mysqli_stmt_execute($stmtTiket);
$resultTiket = mysqli_stmt_get_result($stmtTiket);
$totalTiket = mysqli_fetch_assoc($resultTiket)['total'];
mysqli_stmt_close($stmtTiket);

// Total User
$queryUser = "SELECT COUNT(*) as total FROM user";
$stmtUser = mysqli_prepare($conn, $queryUser);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$totalUser = mysqli_fetch_assoc($resultUser)['total'];
mysqli_stmt_close($stmtUser);
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
  <link rel="stylesheet" href="styles/landingPage.css">
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php 
  include 'headerFooter.php';
  renderBackgroundDecorations(); 
  renderHeader($username, $conn); 
  ?>

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
        <div class="stat-number"><?php echo number_format($totalTiket); ?></div>
        <div class="stat-label">Tiket</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?php echo number_format($totalMaskapai); ?></div>
        <div class="stat-label">Maskapai</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?php echo number_format($totalPemesanan); ?></div>
        <div class="stat-label">Pemesanan</div>
      </div>
      <div class="stat-box">
        <div class="stat-number"><?php echo number_format($totalUser); ?></div>
        <div class="stat-label">User Terdaftar</div>
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
      <a href="checkIn.php" class="feature-btn">Check-in Now</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">👤</div>
      <h3>Kelola Profil</h3>
      <p>Atur informasi akun dan preferensi perjalanan Anda untuk pengalaman yang lebih personal.</p>
      <a href="profile.php" class="feature-btn">Lihat Profil</a>
    </div>
  </section>

  <?php renderFooter(); ?>
</body>
</html>