<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data kursi & jumlah penumpang dari seat.php
$seats = isset($_GET['seats']) ? explode(",", $_GET['seats']) : [];
$passengerCount = isset($_GET['passenger']) ? intval($_GET['passenger']) : 1;

// Data penerbangan dari session atau GET
$from = isset($_GET['from']) ? $_GET['from'] : 'Jakarta';
$to = isset($_GET['to']) ? $_GET['to'] : 'Bali';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
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
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Space Grotesk", sans-serif;
    }

    body {
      background: #f8f9fa;
      color: #1a1a1a;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Subtle Background */
    .bg-decorations {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
      pointer-events: none;
    }

    .decoration-circle {
      position: absolute;
      border-radius: 50%;
      opacity: 0.04;
    }

    .decoration-circle:nth-child(1) {
      width: 600px;
      height: 600px;
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      top: -200px;
      right: -200px;
    }

    .decoration-circle:nth-child(2) {
      width: 400px;
      height: 400px;
      background: linear-gradient(225deg, #1976D2, rgb(75, 171, 255));
      bottom: -150px;
      left: -150px;
    }

    /* Header Premium */
    header {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 8px 32px rgba(255, 255, 255, 0.3);
    }

    .logo-link {
      text-decoration: none;
      color: white;
      transition: opacity 0.3s ease;
      cursor: pointer;
    }

    .logo-link:hover {
      opacity: 0.85;
    }

    .logo-link h1 {
      font-size: 36px;
      font-weight: 800;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.2);
      letter-spacing: -1px;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 12px;
      align-items: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 12px 22px;
      border-radius: 15px;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      cursor: pointer;
      display: inline-block;
    }

    nav a:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 255, 255, 1);
    }

    /* Main Content */
    main {
      flex: 1;
      padding: 60px 40px;
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
    }

    .page-title {
      text-align: center;
      font-size: 48px;
      font-weight: 800;
      margin-bottom: 50px;
      color: #1976D2;
      letter-spacing: -2px;
      position: relative;
      padding-bottom: 20px;
    }

    .page-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 150px;
      height: 5px;
      background: linear-gradient(90deg, transparent, rgb(75, 171, 255), #1976D2, transparent);
      border-radius: 3px;
    }

    .payment-wrapper {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card {
      background: white;
      padding: 40px;
      border-radius: 25px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      border: 3px solid rgba(75, 171, 255, 0.08);
      position: relative;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
      border-color: rgb(75, 171, 255);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      border-radius: 25px 25px 0 0;
    }

    .card-title {
      font-size: 26px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 25px;
      letter-spacing: -1px;
    }

    /* Summary Card */
    .summary-item {
      display: flex;
      justify-content: space-between;
      padding: 15px;
      background: rgba(75, 171, 255, 0.05);
      border-radius: 12px;
      margin-bottom: 12px;
      border-left: 4px solid rgb(75, 171, 255);
    }

    .summary-item label {
      font-weight: 700;
      color: #1976D2;
    }

    .summary-item span {
      font-weight: 600;
      color: #333;
    }

    .seats-display {
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.1), rgba(25, 118, 210, 0.1));
      padding: 20px;
      border-radius: 15px;
      margin: 20px 0;
      border: 2px solid rgba(75, 171, 255, 0.2);
    }

    .seats-display strong {
      display: block;
      color: #1976D2;
      font-size: 16px;
      margin-bottom: 10px;
      font-weight: 800;
    }

    .seat-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
    }

    .seat-tag {
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 14px;
      box-shadow: 0 4px 12px rgba(75, 171, 255, 0.3);
    }

    .price-total {
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      color: white;
      padding: 25px;
      border-radius: 15px;
      text-align: center;
      margin-top: 25px;
      box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
    }

    .price-total .label {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 10px;
      opacity: 0.95;
    }

    .price-total .amount {
      font-size: 42px;
      font-weight: 800;
      letter-spacing: -1px;
    }

    /* Form Card */
    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      margin-bottom: 10px;
      font-weight: 700;
      color: #1976D2;
      font-size: 15px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 16px 20px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s ease;
      background: #f8f9fa;
      font-family: "Space Grotesk", sans-serif;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: rgb(75, 171, 255);
      background: white;
      box-shadow: 0 6px 20px rgba(75, 171, 255, 0.15);
    }

    .form-group input::placeholder {
      color: #999;
      font-weight: 500;
    }

    .form-row {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 15px;
    }

    .card-visual {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 25px;
      border-radius: 15px;
      color: white;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
      position: relative;
      overflow: hidden;
    }

    .card-visual::before {
      content: '💳';
      position: absolute;
      font-size: 120px;
      right: -20px;
      top: -20px;
      opacity: 0.2;
    }

    .card-chip {
      width: 50px;
      height: 40px;
      background: linear-gradient(135deg, #f4d03f, #f39c12);
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .card-number {
      font-size: 22px;
      letter-spacing: 4px;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .card-info {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      opacity: 0.9;
    }

    /* Buttons */
    .btn-container {
      display: flex;
      gap: 15px;
      margin-top: 30px;
    }

    .btn {
      flex: 1;
      padding: 18px;
      border: none;
      border-radius: 12px;
      font-size: 17px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      text-decoration: none;
      text-align: center;
      display: inline-block;
    }

    .btn-pay {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.4);
    }

    .btn-pay:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(75, 171, 255, 0.6);
    }

    .btn-back {
      background: rgba(75, 171, 255, 0.1);
      color: #1976D2;
      border: 2px solid rgba(75, 171, 255, 0.3);
    }

    .btn-back:hover {
      background: rgba(75, 171, 255, 0.15);
      transform: translateY(-3px);
    }

    /* Footer */
    footer {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: #ccc;
      text-align: center;
      padding: 45px;
      margin-top: 0;
      font-size: 16px;
      position: relative;
    }

    footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgb(75, 171, 255), transparent);
    }

    footer p {
      font-weight: 600;
    }

    @media (max-width: 968px) {
      header {
        flex-direction: column;
        gap: 18px;
        padding: 20px;
      }

      .logo-link h1 {
        font-size: 28px;
      }

      nav ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
      }

      main {
        padding: 40px 20px;
      }

      .page-title {
        font-size: 32px;
        margin-bottom: 30px;
      }

      .payment-wrapper {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .card {
        padding: 30px 20px;
      }

      .form-row {
        grid-template-columns: 1fr;
      }

      .btn-container {
        flex-direction: column;
      }
    }
  </style>
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
          <div class="amount">Rp <?php echo number_format($passengerCount * 1500000, 0, ',', '.'); ?></div>
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

        <form action="payment_success.php" method="POST">
          <input type="hidden" name="seats" value="<?php echo htmlspecialchars(implode(',', $seats)); ?>">
          <input type="hidden" name="passenger" value="<?php echo $passengerCount; ?>">
          <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
          <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
          <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">

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

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>

  <script>
    // Auto-format card number
    const cardInput = document.getElementById('card');
    cardInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\s/g, '');
      let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
      e.target.value = formattedValue;
    });

    // Card number validation
    cardInput.addEventListener('keypress', (e) => {
      if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
        e.preventDefault();
      }
    });

    // CVV validation
    const cvvInput = document.getElementById('cvv');
    cvvInput.addEventListener('keypress', (e) => {
      if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>