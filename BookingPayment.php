<?php
session_start();
include 'connector.php';
include 'headerFooter.php';

$username = $_SESSION['username'];

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Jupuk data seat.php
$id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;
$seats = isset($_GET['seats']) ? explode(",", $_GET['seats']) : [];
$passengerCount = isset($_GET['passenger']) ? intval($_GET['passenger']) : 1;
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$airline = isset($_GET['airline']) ? $_GET['airline'] : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;

// Validasi data
if ($id_tiket == 0 || empty($from) || empty($to) || empty($seats)) {
    header("Location: search.php");
    exit();
}

// Itung total rego
$totalPrice = $passengerCount * $price;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/bookingPayment.css">
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php renderBackgroundDecorations(); ?>
  <?php renderHeader($username, $conn); ?>

  <main>
    <h1 class="page-title">💳 Pembayaran</h1>

    <div class="payment-wrapper">
      <!-- Summary Card -->
      <div class="card">
        <h2 class="card-title">📋 Ringkasan Pemesanan</h2>
        
        <div class="summary-item">
          <label>🛫 Dari:</label>
          <span><?php echo htmlspecialchars($from); ?></span>
        </div>

        <div class="summary-item">
          <label>🛬 Ke:</label>
          <span><?php echo htmlspecialchars($to); ?></span>
        </div>

        <div class="summary-item">
          <label>📅 Tanggal:</label>
          <span><?php echo date('d M Y', strtotime($date)); ?></span>
        </div>

        <div class="summary-item">
          <label>👥 Penumpang:</label>
          <span><?php echo $passengerCount; ?> Orang</span>
        </div>

        <div class="seats-display">
          <strong>Kursi Dipilih:</strong>
          <div class="seat-tags">
            <?php 
            if (!empty($seats)) {
              foreach ($seats as $seat) {
                echo "<span class='seat-tag'>" . htmlspecialchars($seat) . "</span>";
              }
            } else {
              echo "<span style='color: #999;'>Belum ada kursi dipilih</span>";
            }
            ?>
          </div>
        </div>

        <div class="price-total">
          <div class="label">Total Pembayaran</div>
          <div class="amount">Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></div>
        </div>
      </div>

      <!-- Payment Form Card -->
      <div class="card">
        <h2 class="card-title">💳 Informasi Pembayaran</h2>

        <div class="card-visual">
          <div class="card-chip"></div>
          <div class="card-number">•••• •••• •••• ••••</div>
          <div class="card-info">
            <span>CARD HOLDER</span>
            <span>MM/YY</span>
          </div>
        </div>

        <form action="paymentSuccess.php" method="POST">
          <!-- Hidden fields untuk kirim data -->
          <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
          <input type="hidden" name="seats" value="<?php echo htmlspecialchars(implode(',', $seats)); ?>">
          <input type="hidden" name="passenger" value="<?php echo $passengerCount; ?>">
          <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
          <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
          <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
          <input type="hidden" name="price" value="<?php echo $price; ?>">

          <div class="form-group">
            <label for="nama">👤 Nama Pemilik Kartu</label>
            <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso" required>
          </div>

          <div class="form-group">
            <label for="card">💳 Nomor Kartu</label>
            <input type="text" id="card" name="card" maxlength="19" placeholder="1234 5678 9012 3456" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="exp">📅 Expired</label>
              <input type="month" id="exp" name="exp" required>
            </div>

            <div class="form-group">
              <label for="cvv">🔒 CVV</label>
              <input type="password" id="cvv" name="cvv" maxlength="3" placeholder="123" required>
            </div>
          </div>

          <div class="btn-container">
            <a href="javascript:history.back()" class="btn btn-back">← Kembali</a>
            <button type="submit" class="btn btn-pay">💰 Bayar Sekarang</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php renderFooter(); ?>

  <script>
    // Gawe format card number
    const cardInput = document.getElementById('card');
    cardInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\s/g, '');
      let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
      e.target.value = formattedValue;
    });

    // Validasi anu
    cardInput.addEventListener('keypress', (e) => {
      if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
        e.preventDefault();
      }
    });

    // Validasi cvv
    const cvvInput = document.getElementById('cvv');
    cvvInput.addEventListener('keypress', (e) => {
      if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>