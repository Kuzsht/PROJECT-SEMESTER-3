<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrfHelper.php';

// Cek login dan role admin
requireLogin();
requireAdmin();
initSecureSession();

$username = $_SESSION['username'];
$userRole = getUserRole();

// Statistik untuk admin
$stats = [];

// Total User
$queryUsers = "SELECT COUNT(*) as total FROM user";
$stmtUsers = mysqli_prepare($conn, $queryUsers);
mysqli_stmt_execute($stmtUsers);
$stats['users'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUsers))['total'];
mysqli_stmt_close($stmtUsers);

// Total Pemesanan
$queryBookings = "SELECT COUNT(*) as total, SUM(harga_total) as revenue FROM pemesanan";
$stmtBookings = mysqli_prepare($conn, $queryBookings);
mysqli_stmt_execute($stmtBookings);
$bookingData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtBookings));
$stats['bookings'] = $bookingData['total'];
$stats['revenue'] = $bookingData['revenue'] ?? 0;
mysqli_stmt_close($stmtBookings);

// Total Maskapai
$queryAirlines = "SELECT COUNT(*) as total FROM maskapai";
$stmtAirlines = mysqli_prepare($conn, $queryAirlines);
mysqli_stmt_execute($stmtAirlines);
$stats['airlines'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtAirlines))['total'];
mysqli_stmt_close($stmtAirlines);

// Total Tiket
$queryTickets = "SELECT COUNT(*) as total FROM tiket";
$stmtTickets = mysqli_prepare($conn, $queryTickets);
mysqli_stmt_execute($stmtTickets);
$stats['tickets'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTickets))['total'];
mysqli_stmt_close($stmtTickets);

// Recent bookings
$queryRecent = "SELECT p.*, u.username, u.email_user, t.asal_kota, t.tujuan_kota, m.nama_maskapai
                FROM pemesanan p
                INNER JOIN user u ON p.id_user = u.id_user
                INNER JOIN tiket t ON p.id_tiket = t.id_tiket
                INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai
                ORDER BY p.id_pemesanan DESC
                LIMIT 10";
$stmtRecent = mysqli_prepare($conn, $queryRecent);
mysqli_stmt_execute($stmtRecent);
$recentBookings = mysqli_fetch_all(mysqli_stmt_get_result($stmtRecent), MYSQLI_ASSOC);
mysqli_stmt_close($stmtRecent);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/headerFooter.css">
  <link rel="stylesheet" href="styles/adminDashboard.css">
</head>
<body>
  <?php
  renderBackgroundDecorations();
  renderHeader($username, $conn);
  ?>

  <main>
    <section class="hero">
      <div class="hero-content">
        <div class="admin-badge">👑 ADMIN DASHBOARD</div>
        <h2>Selamat Datang, Administrator!</h2>
        <p>Kelola sistem AIRtix.id dengan mudah dan efisien</p>
      </div>
    </section>

    <div class="container">
      <h2 class="section-title">Statistik Sistem</h2>
      
      <div class="stats-grid">
        <div class="stat-card">
          <span class="icon">👥</span>
          <h3>Total Pengguna</h3>
          <div class="number"><?php echo number_format($stats['users']); ?></div>
        </div>
        
        <div class="stat-card">
          <span class="icon">📋</span>
          <h3>Total Pemesanan</h3>
          <div class="number"><?php echo number_format($stats['bookings']); ?></div>
        </div>
        
        <div class="stat-card">
          <span class="icon">💰</span>
          <h3>Total Revenue</h3>
          <div class="number" style="font-size: 32px;">Rp <?php echo number_format($stats['revenue']/1000000, 1); ?>JT</div>
        </div>
        
        <div class="stat-card">
          <span class="icon">✈️</span>
          <h3>Total Maskapai</h3>
          <div class="number"><?php echo number_format($stats['airlines']); ?></div>
        </div>
      </div>

      <h2 class="section-title">Menu Admin</h2>
      
      <div class="admin-menu">
        <a href="adminUsers.php" class="admin-menu-item">
          <span class="icon">👥</span>
          <h4>Kelola User</h4>
          <p>Manage pengguna sistem</p>
        </a>
        
        <a href="adminAirlines.php" class="admin-menu-item">
          <span class="icon">✈️</span>
          <h4>Kelola Maskapai</h4>
          <p>Tambah/edit maskapai</p>
        </a>
        
        <a href="adminTickets.php" class="admin-menu-item">
          <span class="icon">🎫</span>
          <h4>Kelola Tiket</h4>
          <p>Manage tiket penerbangan</p>
        </a>
        
        <a href="adminBookings.php" class="admin-menu-item">
          <span class="icon">📋</span>
          <h4>Kelola Pemesanan</h4>
          <p>Lihat semua pemesanan</p>
        </a>
      </div>

      <div class="recent-bookings">
        <h3>📊 Pemesanan Terbaru</h3>
        <?php if (!empty($recentBookings)): ?>
          <?php foreach ($recentBookings as $booking): ?>
            <div class="booking-item">
              <div>
                <strong><?php echo htmlspecialchars($booking['kode_pemesanan'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                <small>
                  <?php echo htmlspecialchars($booking['username'], ENT_QUOTES, 'UTF-8'); ?> • 
                  <?php echo htmlspecialchars($booking['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?> • 
                  <?php echo htmlspecialchars($booking['asal_kota'], ENT_QUOTES, 'UTF-8'); ?> → 
                  <?php echo htmlspecialchars($booking['tujuan_kota'], ENT_QUOTES, 'UTF-8'); ?>
                </small>
              </div>
              <div style="text-align: right;">
                <strong>Rp <?php echo number_format($booking['harga_total'], 0, ',', '.'); ?></strong><br>
                <small><?php echo date('d M Y', strtotime($booking['tanggal_keberangkatan'])); ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; color: #999; padding: 40px 0;">Belum ada pemesanan</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php renderFooter(); ?>
</body>
</html>