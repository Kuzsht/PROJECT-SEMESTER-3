<?php
session_start();
include 'connector.php';

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

    main {
      flex: 1;
      padding: 60px 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: white;
      padding: 50px 45px;
      border-radius: 35px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      max-width: 700px;
      width: 100%;
      position: relative;
      border: 3px solid rgba(75, 171, 255, 0.08);
      transition: all 0.3s ease;
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

    .container:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
      border-color: rgb(75, 171, 255);
    }

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      border-radius: 35px 35px 0 0;
    }

    .page-title {
      text-align: center;
      font-size: 36px;
      font-weight: 800;
      margin-bottom: 15px;
      color: #1976D2;
      letter-spacing: -1px;
    }

    .subtitle {
      text-align: center;
      color: #666;
      margin-bottom: 35px;
      font-size: 15px;
    }

    .flight-summary {
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.08), rgba(25, 118, 210, 0.08));
      padding: 25px;
      border-radius: 20px;
      margin-bottom: 35px;
      border: 2px solid rgba(75, 171, 255, 0.15);
    }

    .summary-title {
      font-size: 16px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .flight-route {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .route-city {
      flex: 1;
      text-align: center;
    }

    .route-city-name {
      font-size: 22px;
      font-weight: 800;
      color: #1976D2;
    }

    .route-arrow {
      font-size: 28px;
      margin: 0 15px;
    }

    .airline-info {
      text-align: center;
      font-weight: 700;
      color: #666;
      margin-top: 10px;
    }

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

    select {
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%234BABFF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 20px;
      padding-right: 45px;
    }

    .price-estimate {
      background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(56, 142, 60, 0.1));
      padding: 20px;
      border-radius: 15px;
      margin: 25px 0;
      border: 2px solid rgba(76, 175, 80, 0.2);
      text-align: center;
    }

    .price-estimate-label {
      font-size: 14px;
      color: #388E3C;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .price-estimate-amount {
      font-size: 28px;
      font-weight: 800;
      color: #4CAF50;
      font-family: monospace;
    }

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
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      text-decoration: none;
      text-align: center;
      display: inline-block;
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

    .btn-submit {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.4);
    }

    .btn-submit:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(75, 171, 255, 0.6);
    }

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

      .container {
        padding: 40px 25px;
      }

      .page-title {
        font-size: 28px;
      }

      .route-city-name {
        font-size: 18px;
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