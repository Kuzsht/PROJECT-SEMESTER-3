<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrfHelper.php';

// Cek login
requireLogin();
initSecureSession();

$username = $_SESSION["username"];

// Ambil semua tiket dari database dengan JOIN ke tabel maskapai
$query = "SELECT t.id_tiket, t.asal_kota, t.tujuan_kota, m.nama_maskapai, m.harga_satukursi 
          FROM tiket t 
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai 
          ORDER BY t.asal_kota, t.tujuan_kota";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$tickets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tickets[] = $row;
}
mysqli_stmt_close($stmt);
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
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php renderBackgroundDecorations(); ?>
  <?php renderHeader($username, $conn); ?>

  <main>
    <div class="back-wrapper">
      <a href="landingPage.php" class="back-btn">← Kembali ke Beranda</a>
    </div>

    <h1 class="page-title">Pilih Penerbangan</h1>

    <?php if (!empty($tickets)): ?>
      <div class="flights-grid">
        <?php foreach ($tickets as $ticket): ?>
          <div class="flight-card">
            <div class="airline-name">
              ✈️ <?php echo htmlspecialchars($ticket['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?>
            </div>

            <div class="route-display">
              <div class="city">
                <div class="city-name"><?php echo htmlspecialchars($ticket['asal_kota'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="city-label">🛫 Keberangkatan</div>
              </div>
              <div class="plane-icon">→</div>
              <div class="city">
                <div class="city-name"><?php echo htmlspecialchars($ticket['tujuan_kota'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="city-label">🛬 Tujuan</div>
              </div>
            </div>

            <div class="price-section">
              <div class="price-label">Harga per Kursi</div>
              <div class="price-amount">Rp <?php echo number_format($ticket['harga_satukursi'], 0, ',', '.'); ?></div>
            </div>

            <form action="inputSearch.php" method="get">
              <input type="hidden" name="id_tiket" value="<?php echo intval($ticket['id_tiket']); ?>">
              <input type="hidden" name="from" value="<?php echo htmlspecialchars($ticket['asal_kota'], ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="to" value="<?php echo htmlspecialchars($ticket['tujuan_kota'], ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="airline" value="<?php echo htmlspecialchars($ticket['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="price" value="<?php echo intval($ticket['harga_satukursi']); ?>">
              <button type="submit" class="select-btn">🎫 Pilih Penerbangan</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">✈️</div>
        <h2 class="empty-title">Tidak Ada Penerbangan Tersedia</h2>
        <p style="color: #999; margin-bottom: 25px;">Belum ada tiket penerbangan yang terdaftar di sistem.</p>
      </div>
    <?php endif; ?>
  </main>

  <?php renderFooter(); ?>
</body>
</html>