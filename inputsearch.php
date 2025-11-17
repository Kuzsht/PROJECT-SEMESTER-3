<?php
session_start();
include 'connector.php';
include 'headerFooter.php';

$username = $_SESSION['username'];

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';
$airline = isset($_GET['airline']) ? $_GET['airline'] : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;

if ($id_tiket == 0 || empty($from) || empty($to)) {
    header("Location: search.php");
    exit();
}
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
  <?php renderHeader($username); ?>

  <main>
    <div class="container">
      <h1 class="page-title">Detail Pemesanan</h1>
      <p class="subtitle">Lengkapi informasi penerbangan Anda</p>

      <div class="flight-summary">
        <div class="summary-title">✈️ Penerbangan Dipilih</div>
        <div class="flight-route">
          <div class="route-city">
            <div class="route-city-name"><?php echo htmlspecialchars($from); ?></div>
          </div>
          <div class="route-arrow">→</div>
          <div class="route-city">
            <div class="route-city-name"><?php echo htmlspecialchars($to); ?></div>
          </div>
        </div>
        <div class="airline-info">
          🛫 <?php echo htmlspecialchars($airline); ?>
        </div>
      </div>

      <form method="get" action="seat.php" id="bookingForm">
        <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
        <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
        <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
        <input type="hidden" name="airline" value="<?php echo htmlspecialchars($airline); ?>">
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
    const pricePerSeat = <?php echo $price; ?>;
    const penumpangSelect = document.getElementById('penumpang');
    const totalPriceDisplay = document.getElementById('totalPrice');

    penumpangSelect.addEventListener('change', function() {
      const passengerCount = parseInt(this.value) || 0;
      const totalPrice = pricePerSeat * passengerCount;
      
      totalPriceDisplay.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    });

    // Set tgl minim hari ini
    document.getElementById('date').min = new Date().toISOString().split('T')[0];
  </script>
</body>
</html>