<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrf_helper.php';

// Cek login
requireLogin();
initSecureSession();

$username = $_SESSION['username'];
$id_user = $_SESSION['id_user'];

// VALIDASI: Hanya terima POST request dengan CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    safeRedirect("search.php");
}

// CSRF Protection
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    die("Invalid security token. Silakan refresh halaman dan coba lagi.");
}

// Ambil dan validasi data dari form pembayaran
$nama = isset($_POST['nama']) ? sanitizeInput($_POST['nama']) : '';
$card = isset($_POST['card']) ? sanitizeInput($_POST['card']) : '';

$id_tiket = isset($_POST['id_tiket']) ? intval($_POST['id_tiket']) : 0;
$from = isset($_POST['from']) ? sanitizeInput($_POST['from']) : '';
$to = isset($_POST['to']) ? sanitizeInput($_POST['to']) : '';
$date = isset($_POST['date']) ? sanitizeInput($_POST['date']) : date('Y-m-d');
$passengerCount = isset($_POST['passenger']) ? intval($_POST['passenger']) : 1;
$price = isset($_POST['price']) ? intval($_POST['price']) : 0;

$seatsRaw = isset($_POST['seats']) ? sanitizeInput($_POST['seats']) : '';

// Konversi seats ke array
$seats = [];
if (!empty($seatsRaw)) {
    $seats = array_filter(array_map('trim', explode(",", $seatsRaw)));
}

// Validasi data lengkap
if ($id_tiket == 0 || empty($seats) || $passengerCount == 0 || empty($nama) || empty($card)) {
    safeRedirect("search.php");
}

// Validasi jumlah kursi sesuai penumpang
if (count($seats) != $passengerCount) {
    die("Error: Jumlah kursi tidak sesuai dengan jumlah penumpang.");
}

// Validasi tanggal
$dateObj = DateTime::createFromFormat('Y-m-d', $date);
if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
    die("Error: Format tanggal tidak valid.");
}

// CEK KETERSEDIAAN KURSI SEKALI LAGI (PENTING!)
$kursi_dipilih = implode(',', $seats);
$checkSeatsQuery = "SELECT kursi_dipilih FROM pemesanan 
                    WHERE id_tiket = ? AND tanggal_keberangkatan = ?";
$checkStmt = mysqli_prepare($conn, $checkSeatsQuery);
mysqli_stmt_bind_param($checkStmt, "is", $id_tiket, $date);
mysqli_stmt_execute($checkStmt);
$resultCheck = mysqli_stmt_get_result($checkStmt);

$bookedSeats = [];
while ($row = mysqli_fetch_assoc($resultCheck)) {
    if (!empty($row['kursi_dipilih'])) {
        $existingSeats = explode(',', $row['kursi_dipilih']);
        foreach ($existingSeats as $seat) {
            $bookedSeats[] = trim($seat);
        }
    }
}
mysqli_stmt_close($checkStmt);

// Cek apakah ada kursi yang bentrok
$conflictSeats = array_intersect($seats, $bookedSeats);
if (!empty($conflictSeats)) {
    die("Error: Kursi " . implode(', ', $conflictSeats) . " sudah dipesan oleh orang lain. Silakan pilih kursi lain.");
}

// Generate kode booking unik
$bookingCode = 'ATX' . strtoupper(substr(md5(time() . rand() . $id_user), 0, 8));

// Hitung total harga
$totalPrice = $passengerCount * $price;

// Simpan ke database menggunakan prepared statement
$query = "INSERT INTO pemesanan 
          (kode_pemesanan, tanggal_keberangkatan, kursi_dipilih, jumlah_penumpang, harga_total, checkin, id_tiket, id_user) 
          VALUES (?, ?, ?, ?, ?, 0, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sssidii", $bookingCode, $date, $kursi_dipilih, $passengerCount, $totalPrice, $id_tiket, $id_user);

$success = mysqli_stmt_execute($stmt);
$id_pemesanan = mysqli_insert_id($conn);

mysqli_stmt_close($stmt);

if (!$success) {
    error_log("Payment failed: " . mysqli_error($conn));
    die("Error: Gagal menyimpan pemesanan. Silakan hubungi administrator.");
}

// Regenerate CSRF token setelah transaksi sukses
regenerateCsrfToken();
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
  <link rel="stylesheet" href="styles/paymentSuccess.css">
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php renderBackgroundDecorations(); ?>
  <?php renderHeader($username, $conn); ?>

  <main>
    <div class="success-container">
      <div class="success-icon">✅</div>
      <h1 class="success-title">Pembayaran Berhasil!</h1>
      <p class="success-subtitle">Terima kasih, pembayaran Anda telah diterima dan diproses.</p>

      <div class="booking-code">
        <div class="booking-code-label">Kode Booking</div>
        <div class="booking-code-value"><?php echo htmlspecialchars($bookingCode, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>

      <div class="summary-box">
        <div class="summary-title">📋 Detail Pemesanan</div>
        
        <div class="summary-row">
          <span class="summary-label">🛫 Dari:</span>
          <span class="summary-value"><?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="summary-row">
          <span class="summary-label">🛬 Ke:</span>
          <span class="summary-value"><?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?></span>
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
                  echo "<span class='seat-badge'>" . htmlspecialchars($seatClean, ENT_QUOTES, 'UTF-8') . "</span>";
                }
              }
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
        <a href="landingPage.php" class="btn btn-secondary">🏠 Kembali ke Beranda</a>
      </div>
    </div>
  </main>

  <?php renderFooter(); ?>
</body>
</html>