<?php
session_start();
include 'connector.php';

$username = $_SESSION['username'];

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$success = false;

// Handle check-in submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemesanan'])) {
    $id_pemesanan = intval($_POST['id_pemesanan']);
    
    // Update checkin status di database
    $updateQuery = "UPDATE pemesanan SET checkin = 1 WHERE id_pemesanan = ? AND id_user = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ii", $id_pemesanan, $id_user);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    
    mysqli_stmt_close($stmt);
}

// Ambil data booking yang belum check-in dari database
$query = "SELECT p.*, t.asal_kota, t.tujuan_kota, m.nama_maskapai 
          FROM pemesanan p
          INNER JOIN tiket t ON p.id_tiket = t.id_tiket
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai
          WHERE p.id_user = ? AND p.checkin = 0
          ORDER BY p.tanggal_keberangkatan ASC, p.id_pemesanan DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pendingBookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pendingBookings[] = $row;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check-in Online - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/checkin.css">
  <link rel="stylesheet" href="styles/headerfooter.css">
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

  <main>
    <h1 class="page-title">✅ Check-in Online</h1>

    <?php if ($success): ?>
      <div class="success-alert">
        <div class="icon">✅</div>
        <div class="text">
          <div class="title">Check-in Berhasil!</div>
          <div class="desc">Anda telah berhasil melakukan check-in. Selamat menikmati penerbangan!</div>
        </div>
      </div>
    <?php endif; ?>

    <?php if (empty($pendingBookings)): ?>
      <!-- Empty State -->
      <div class="empty-state">
        <div class="empty-icon">✈️</div>
        <h2 class="empty-title">Tidak Ada Booking untuk Check-in</h2>
        <p class="empty-text">Anda belum memiliki booking yang perlu di-check-in. Pesan tiket terlebih dahulu!</p>
        <a href="search.php" class="btn">🚀 Pesan Tiket Sekarang</a>
      </div>
    <?php else: ?>
      <!-- Check-in Cards -->
      <div class="checkin-grid">
        <?php foreach ($pendingBookings as $booking): 
          // Konversi kursi dari string ke array
          $seats = !empty($booking['kursi_dipilih']) ? explode(',', $booking['kursi_dipilih']) : [];
        ?>
          <div class="checkin-card">
            <div class="card-header">
              <div class="booking-code-display"><?php echo htmlspecialchars($booking['kode_pemesanan']); ?></div>
            </div>

            <div class="route-display">
              <div class="city-display">
                <div class="city-name"><?php echo htmlspecialchars($booking['asal_kota']); ?></div>
                <div class="city-label">🛫 Keberangkatan</div>
              </div>
              <div class="plane-icon">✈️</div>
              <div class="city-display">
                <div class="city-name"><?php echo htmlspecialchars($booking['tujuan_kota']); ?></div>
                <div class="city-label">🛬 Tujuan</div>
              </div>
            </div>

            <div class="flight-details">
              <div class="detail-item">
                <span class="detail-label">✈️ Maskapai</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['nama_maskapai']); ?></span>
              </div>

              <div class="detail-item">
                <span class="detail-label">📅 Tanggal Keberangkatan</span>
                <span class="detail-value"><?php echo date('d M Y', strtotime($booking['tanggal_keberangkatan'])); ?></span>
              </div>

              <div class="detail-item">
                <span class="detail-label">👥 Jumlah Penumpang</span>
                <span class="detail-value"><?php echo $booking['jumlah_penumpang']; ?> Orang</span>
              </div>

              <div class="detail-item">
                <span class="detail-label">🪑 Kursi</span>
                <div class="seats-display-mini">
                  <?php foreach ($seats as $seat): ?>
                    <span class="seat-badge-mini"><?php echo htmlspecialchars(trim($seat)); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="detail-item">
                <span class="detail-label">💰 Total Pembayaran</span>
                <span class="detail-value">Rp <?php echo number_format($booking['harga_total'], 0, ',', '.'); ?></span>
              </div>
            </div>

            <form method="POST" class="checkin-form">
              <input type="hidden" name="id_pemesanan" value="<?php echo $booking['id_pemesanan']; ?>">
              <button type="submit" class="checkin-btn">✅ Check-in Sekarang</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>

  <script>
    // Auto-hide success alert after 5 seconds
    const successAlert = document.querySelector('.success-alert');
    if (successAlert) {
      setTimeout(() => {
        successAlert.style.transition = 'all 0.5s ease';
        successAlert.style.opacity = '0';
        successAlert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          successAlert.remove();
        }, 500);
      }, 5000);
    }

    // Confirm before check-in
    document.querySelectorAll('.checkin-form').forEach(form => {
      form.addEventListener('submit', (e) => {
        if (!confirm('Apakah Anda yakin ingin melakukan check-in?')) {
          e.preventDefault();
        }
      });
    });
  </script>
</body>
</html>