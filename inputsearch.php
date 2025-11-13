<?php
session_start();
include 'connector.php';

$username = $_SESSION['username'];

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data tiket dari parameter GET
$id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';
$airline = isset($_GET['airline']) ? $_GET['airline'] : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;

// Validasi data
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
  <link rel="stylesheet" href="styles/inputsearch.css">
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

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>

  <script>
    const pricePerSeat = <?php echo $price; ?>;
    const penumpangSelect = document.getElementById('penumpang');
    const totalPriceDisplay = document.getElementById('totalPrice');

    // Update total price when passenger count changes
    penumpangSelect.addEventListener('change', function() {
      const passengerCount = parseInt(this.value) || 0;
      const totalPrice = pricePerSeat * passengerCount;
      
      totalPriceDisplay.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    });

    // Set minimum date to today
    document.getElementById('date').min = new Date().toISOString().split('T')[0];
  </script>
</body>
</html>