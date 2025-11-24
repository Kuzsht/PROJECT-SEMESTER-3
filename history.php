<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrfHelper.php';

// Cek login
requireLogin();
initSecureSession();

$username = $_SESSION['username'];
$id_user = $_SESSION['id_user'];

// Query dengan prepared statement
$query = "SELECT p.*, t.asal_kota, t.tujuan_kota, m.nama_maskapai 
          FROM pemesanan p
          INNER JOIN tiket t ON p.id_tiket = t.id_tiket
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai
          WHERE p.id_user = ?
          ORDER BY p.id_pemesanan DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = $row;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pemesanan - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/history.css">
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php renderBackgroundDecorations(); ?>
  <?php renderHeader($username, $conn); ?>

  <main>
    <div class="back-wrapper">
      <a href="landingPage.php" class="back-btn">← Kembali ke Beranda</a>
    </div>

    <h1 class="page-title">📋 Riwayat Pemesanan</h1>

    <?php if (empty($bookings)): ?>
      <!-- Empty State -->
      <div class="empty-state">
        <div class="empty-icon">🔭</div>
        <h2 class="empty-title">Belum Ada Riwayat Pemesanan</h2>
        <p class="empty-text">Anda belum melakukan pemesanan tiket. Mulai perjalanan Anda sekarang!</p>
        <a href="search.php" class="btn">🚀 Pesan Tiket Sekarang</a>
      </div>
    <?php else: ?>
      <div class="bookings-grid">
        <?php foreach ($bookings as $booking): 
          // Konversi string ke array
          $seats = !empty($booking['kursi_dipilih']) ? explode(',', $booking['kursi_dipilih']) : [];
        ?>
          <div class="booking-card">
            <div class="booking-header">
              <div class="booking-code"><?php echo htmlspecialchars($booking['kode_pemesanan'], ENT_QUOTES, 'UTF-8'); ?></div>
              <span class="booking-status <?php echo $booking['checkin'] == 1 ? 'status-checkedin' : 'status-pending'; ?>">
                <?php echo $booking['checkin'] == 1 ? 'Sudah Check-in' : 'Belum Check-in'; ?>
              </span>
            </div>

            <div class="booking-route">
              <div class="route-city">
                <div class="route-city-name"><?php echo htmlspecialchars($booking['asal_kota'], ENT_QUOTES, 'UTF-8'); ?></div>
                <small>🛫 Keberangkatan</small>
              </div>
              <div class="route-icon">✈️</div>
              <div class="route-city">
                <div class="route-city-name"><?php echo htmlspecialchars($booking['tujuan_kota'], ENT_QUOTES, 'UTF-8'); ?></div>
                <small>🛬 Tujuan</small>
              </div>
            </div>

            <div class="booking-details">
              <div class="detail-row">
                <span class="detail-label">✈️ Maskapai</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?></span>
              </div>

              <div class="detail-row">
                <span class="detail-label">📅 Tanggal</span>
                <span class="detail-value"><?php echo date('d M Y', strtotime($booking['tanggal_keberangkatan'])); ?></span>
              </div>

              <div class="detail-row">
                <span class="detail-label">👥 Penumpang</span>
                <span class="detail-value"><?php echo $booking['jumlah_penumpang']; ?> Orang</span>
              </div>

              <div class="detail-row">
                <span class="detail-label">🪑 Kursi</span>
                <div class="seats-mini">
                  <?php foreach ($seats as $seat): ?>
                    <span class="seat-mini"><?php echo htmlspecialchars(trim($seat), ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="booking-total">
              <span class="total-label">Total Pembayaran:</span>
              <span class="total-amount">Rp <?php echo number_format($booking['harga_total'], 0, ',', '.'); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <?php renderFooter(); ?>
</body>
</html>