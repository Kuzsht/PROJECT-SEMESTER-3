<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil semua tiket dari database dengan JOIN ke tabel maskapai
$query = "SELECT t.*, m.nama_maskapai, m.harga_satukursi 
          FROM tiket t 
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai 
          ORDER BY t.asal_kota, t.tujuan_kota";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Penerbangan - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/search.css">
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
        <li><a href="history.php">📋 Riwayat</a></li>
        <li><a href="checkin.php">✅ Check-in</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="back-wrapper">
      <a href="LandingPage.php" class="back-btn">← Kembali ke Beranda</a>
    </div>

    <h1 class="page-title">Pilih Penerbangan</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="flights-grid">
        <?php while ($ticket = mysqli_fetch_assoc($result)): ?>
          <div class="flight-card">
            <div class="airline-name">
              ✈️ <?php echo htmlspecialchars($ticket['nama_maskapai']); ?>
            </div>

            <div class="route-display">
              <div class="city">
                <div class="city-name"><?php echo htmlspecialchars($ticket['asal_kota']); ?></div>
                <div class="city-label">🛫 Keberangkatan</div>
              </div>
              <div class="plane-icon">→</div>
              <div class="city">
                <div class="city-name"><?php echo htmlspecialchars($ticket['tujuan_kota']); ?></div>
                <div class="city-label">🛬 Tujuan</div>
              </div>
            </div>

            <div class="price-section">
              <div class="price-label">Harga per Kursi</div>
              <div class="price-amount">Rp <?php echo number_format($ticket['harga_satukursi'], 0, ',', '.'); ?></div>
            </div>

            <form action="inputsearch.php" method="get">
              <input type="hidden" name="id_tiket" value="<?php echo $ticket['id_tiket']; ?>">
              <input type="hidden" name="from" value="<?php echo htmlspecialchars($ticket['asal_kota']); ?>">
              <input type="hidden" name="to" value="<?php echo htmlspecialchars($ticket['tujuan_kota']); ?>">
              <input type="hidden" name="airline" value="<?php echo htmlspecialchars($ticket['nama_maskapai']); ?>">
              <input type="hidden" name="price" value="<?php echo $ticket['harga_satukursi']; ?>">
              <button type="submit" class="select-btn">🎫 Pilih Penerbangan</button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">✈️</div>
        <h2 class="empty-title">Tidak Ada Penerbangan Tersedia</h2>
        <p style="color: #999; margin-bottom: 25px;">Belum ada tiket penerbangan yang terdaftar di sistem.</p>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>