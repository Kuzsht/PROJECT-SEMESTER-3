<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrf_helper.php';

// Cek login
requireLogin();
initSecureSession();

$username = $_SESSION['username'];

// Validasi dan sanitasi input
$id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;
$seatsRaw = isset($_GET['seats']) ? sanitizeInput($_GET['seats']) : '';
$passengerCount = isset($_GET['passenger']) ? intval($_GET['passenger']) : 1;
$from = isset($_GET['from']) ? sanitizeInput($_GET['from']) : '';
$to = isset($_GET['to']) ? sanitizeInput($_GET['to']) : '';
$date = isset($_GET['date']) ? sanitizeInput($_GET['date']) : date('Y-m-d');
$airline = isset($_GET['airline']) ? sanitizeInput($_GET['airline']) : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;

// Parse seats
$seats = array_filter(array_map('trim', explode(",", $seatsRaw)));

// Validasi data
if ($id_tiket == 0 || empty($from) || empty($to) || empty($seats) || $passengerCount == 0) {
    safeRedirect("search.php");
}

// Validasi jumlah kursi sesuai dengan jumlah penumpang
if (count($seats) != $passengerCount) {
    safeRedirect("seat.php?id_tiket=$id_tiket&from=$from&to=$to&date=$date&airline=$airline&price=$price&penumpang=$passengerCount");
}

// Validasi tanggal
$dateObj = DateTime::createFromFormat('Y-m-d', $date);
if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
    safeRedirect("search.php");
}

// Hitung total harga
$totalPrice = $passengerCount * $price;

// Generate CSRF token
$csrfToken = generateCsrfToken();
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
          <span><?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="summary-item">
          <label>🛬 Ke:</label>
          <span><?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?></span>
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
                echo "<span class='seat-tag'>" . htmlspecialchars($seat, ENT_QUOTES, 'UTF-8') . "</span>";
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

        <form action="paymentSuccess.php" method="POST" id="paymentForm">
          <?php echo csrfTokenInput(); ?>
          
          <!-- Hidden fields untuk kirim data -->
          <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
          <input type="hidden" name="seats" value="<?php echo htmlspecialchars(implode(',', $seats), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="passenger" value="<?php echo $passengerCount; ?>">
          <input type="hidden" name="from" value="<?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="to" value="<?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="date" value="<?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="price" value="<?php echo $price; ?>">

          <div class="form-group">
            <label for="nama">👤 Nama Pemilik Kartu</label>
            <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso" required maxlength="100">
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
              <input type="password" id="cvv" name="cvv" maxlength="4" placeholder="123" required>
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
    // Format card number
    const cardInput = document.getElementById('card');
    cardInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\s/g, '');
      let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
      e.target.value = formattedValue;
    });

    // Validasi hanya angka untuk card
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
    
    // Set minimum bulan ke bulan ini
    const expInput = document.getElementById('exp');
    const today = new Date();
    const minMonth = today.toISOString().slice(0, 7);
    expInput.min = minMonth;
    
    // Validasi form
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
      const cardNumber = cardInput.value.replace(/\s/g, '');
      const cvv = cvvInput.value;
      const exp = expInput.value;
      
      if (cardNumber.length < 13 || cardNumber.length > 19) {
        e.preventDefault();
        alert('Nomor kartu tidak valid!');
        return false;
      }
      
      if (cvv.length < 3 || cvv.length > 4) {
        e.preventDefault();
        alert('CVV tidak valid!');
        return false;
      }
      
      if (!exp) {
        e.preventDefault();
        alert('Tanggal expired harus diisi!');
        return false;
      }
    });
  </script>
</body>
</html>