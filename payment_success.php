<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Ambil data dari form pembayaran
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$card = isset($_POST['card']) ? $_POST['card'] : '';

// Ambil data penerbangan dari POST
$id_tiket = isset($_POST['id_tiket']) ? intval($_POST['id_tiket']) : 0;
$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$passengerCount = isset($_POST['passenger']) ? intval($_POST['passenger']) : 1;
$price = isset($_POST['price']) ? intval($_POST['price']) : 0;

// Ambil seats dari POST
$seatsRaw = isset($_POST['seats']) ? $_POST['seats'] : '';

// Konversi seats ke array
$seats = [];
if (!empty($seatsRaw)) {
    if (is_string($seatsRaw)) {
        $seats = array_filter(array_map('trim', explode(",", $seatsRaw)));
    } else if (is_array($seatsRaw)) {
        $seats = array_filter(array_map('trim', $seatsRaw));
    }
}

// Validasi data penting
if ($id_tiket == 0 || empty($seats) || $passengerCount == 0) {
    header("Location: search.php");
    exit();
}

// Generate booking code
$bookingCode = 'ATX' . strtoupper(substr(md5(time() . rand()), 0, 8));
$totalPrice = $passengerCount * $price;
$id_user = $_SESSION['id_user'];

// Gabungkan kursi menjadi string
$kursi_dipilih = implode(',', $seats);

// Insert ke database
$query = "INSERT INTO pemesanan 
          (kode_pemesanan, tanggal_keberangkatan, kursi_dipilih, jumlah_penumpang, harga_total, checkin, id_tiket, id_user) 
          VALUES (?, ?, ?, ?, ?, 0, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
// Tipe data: s=string, i=integer, d=double
// kode_pemesanan(s), tanggal(s), kursi(s), jumlah_penumpang(i), harga(d), id_tiket(i), id_user(i)
mysqli_stmt_bind_param($stmt, "sssidii", $bookingCode, $date, $kursi_dipilih, $passengerCount, $totalPrice, $id_tiket, $id_user);

$success = mysqli_stmt_execute($stmt);
$id_pemesanan = mysqli_insert_id($conn);

mysqli_stmt_close($stmt);

if (!$success) {
    die("Error: Gagal menyimpan pemesanan. " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran Berhasil - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/payment_success.css">
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
    <div class="success-container">
      <div class="success-icon">✅</div>
      <h1 class="success-title">Pembayaran Berhasil!</h1>
      <p class="success-subtitle">Terima kasih, pembayaran Anda telah diterima dan diproses.</p>

      <div class="booking-code">
        <div class="booking-code-label">Kode Booking</div>
        <div class="booking-code-value"><?php echo $bookingCode; ?></div>
      </div>

      <div class="summary-box">
        <div class="summary-title">📋 Detail Pemesanan</div>
        
        <div class="summary-row">
          <span class="summary-label">🛫 Dari:</span>
          <span class="summary-value"><?php echo htmlspecialchars($from); ?></span>
        </div>

        <div class="summary-row">
          <span class="summary-label">🛬 Ke:</span>
          <span class="summary-value"><?php echo htmlspecialchars($to); ?></span>
        </div>

        <div class="summary-row">
          <span class="summary-label">📅 Tanggal:</span>
          <span class="summary-value"><?php echo date('d M Y', strtotime($date)); ?></span>
        </div>

        <div class="summary-row">
          <span class="summary-label">👥 Penumpang:</span>
          <span class="summary-value"><?php echo $passengerCount; ?> Orang</span>
        </div>

        <div class="summary-row">
          <span class="summary-label">🪑 Kursi:</span>
          <div class="seats-list">
            <?php 
            if (!empty($seats) && is_array($seats) && count($seats) > 0) {
              foreach ($seats as $seat) {
                $seatClean = trim($seat);
                if (!empty($seatClean)) {
                  echo "<span class='seat-badge'>" . htmlspecialchars($seatClean) . "</span>";
                }
              }
            } else {
              echo "<span class='summary-value' style='color: #dc3545;'>⚠️ Data kursi tidak ditemukan</span>";
            }
            ?>
          </div>
        </div>

        <div class="total-row">
          <span class="total-label">Total Pembayaran:</span>
          <span class="total-amount">Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></span>
        </div>
      </div>

      <div class="action-buttons">
        <a href="history.php" class="btn btn-primary">📋 Lihat Riwayat</a>
        <a href="LandingPage.php" class="btn btn-secondary">🏠 Kembali ke Beranda</a>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>