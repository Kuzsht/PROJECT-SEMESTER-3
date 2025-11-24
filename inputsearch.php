<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrfHelper.php';

// Cek login
requireLogin();
initSecureSession();

$username = $_SESSION['username'];

// Validasi dan sanitasi input GET
$id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;
$from = isset($_GET['from']) ? sanitizeInput($_GET['from']) : '';
$to = isset($_GET['to']) ? sanitizeInput($_GET['to']) : '';
$airline = isset($_GET['airline']) ? sanitizeInput($_GET['airline']) : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;

// Validasi data - pastikan ID tiket valid dan data lengkap
if ($id_tiket == 0 || empty($from) || empty($to)) {
    safeRedirect("search.php");
}

// Validasi bahwa tiket benar-benar ada di database
$validateQuery = "SELECT t.id_tiket FROM tiket t 
                  INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai 
                  WHERE t.id_tiket = ? AND t.asal_kota = ? AND t.tujuan_kota = ? AND m.nama_maskapai = ?";
$validateStmt = mysqli_prepare($conn, $validateQuery);
mysqli_stmt_bind_param($validateStmt, "isss", $id_tiket, $from, $to, $airline);
mysqli_stmt_execute($validateStmt);
$validateResult = mysqli_stmt_get_result($validateStmt);

if (mysqli_num_rows($validateResult) == 0) {
    mysqli_stmt_close($validateStmt);
    safeRedirect("search.php");
}
mysqli_stmt_close($validateStmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Pemesanan - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/inputSearch.css">
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php renderBackgroundDecorations(); ?>
  <?php renderHeader($username, $conn); ?>

  <main>
    <div class="container">
      <h1 class="page-title">Detail Pemesanan</h1>
      <p class="subtitle">Lengkapi informasi penerbangan Anda</p>

      <div class="flight-summary">
        <div class="summary-title">✈️ Penerbangan Dipilih</div>
        <div class="flight-route">
          <div class="route-city">
            <div class="route-city-name"><?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?></div>
          </div>
          <div class="route-arrow">→</div>
          <div class="route-city">
            <div class="route-city-name"><?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?></div>
          </div>
        </div>
        <div class="airline-info">
          🛫 <?php echo htmlspecialchars($airline, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      </div>

      <form method="get" action="seat.php" id="bookingForm">
        <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
        <input type="hidden" name="from" value="<?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="to" value="<?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="airline" value="<?php echo htmlspecialchars($airline, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="price" value="<?php echo $price; ?>">

        <div class="form-group">
          <label for="date">📅 Tanggal Keberangkatan</label>
          <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
          <label for="penumpang">👥 Jumlah Penumpang</label>
          <select id="penumpang" name="penumpang" required>
            <option value="">-- Pilih Jumlah Penumpang --</option>
            <option value="1">1 Penumpang</option>
            <option value="2">2 Penumpang</option>
            <option value="3">3 Penumpang</option>
            <option value="4">4 Penumpang</option>
            <option value="5">5 Penumpang</option>
            <option value="6">6 Penumpang</option>
          </select>
        </div>

        <div class="price-estimate">
          <div class="price-estimate-label">Estimasi Total Harga</div>
          <div class="price-estimate-amount" id="totalPrice">Rp 0</div>
        </div>

        <div class="btn-container">
          <a href="search.php" class="btn btn-back">← Kembali</a>
          <button type="submit" class="btn btn-submit">Pilih Kursi →</button>
        </div>
      </form>
    </div>
  </main>

  <?php renderFooter(); ?>

  <script>
    const pricePerSeat = <?php echo intval($price); ?>;
    const penumpangSelect = document.getElementById('penumpang');
    const totalPriceDisplay = document.getElementById('totalPrice');

    penumpangSelect.addEventListener('change', function() {
      const passengerCount = parseInt(this.value) || 0;
      const totalPrice = pricePerSeat * passengerCount;
      
      totalPriceDisplay.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    });

    // Set tanggal minimum hari ini
    document.getElementById('date').min = new Date().toISOString().split('T')[0];
    
    // Validasi form sebelum submit
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
      const date = document.getElementById('date').value;
      const penumpang = document.getElementById('penumpang').value;
      
      if (!date || !penumpang) {
        e.preventDefault();
        alert('Silakan lengkapi semua data!');
        return false;
      }
      
      // Validasi tanggal tidak boleh di masa lalu
      const selectedDate = new Date(date);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (selectedDate < today) {
        e.preventDefault();
        alert('Tanggal keberangkatan tidak boleh di masa lalu!');
        return false;
      }
    });
  </script>
</body>
</html>