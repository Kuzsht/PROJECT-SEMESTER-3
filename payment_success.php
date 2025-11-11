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

// Debug untuk cek data sebelum insert
// echo "Kursi: " . $kursi_dipilih . "<br>";
// echo "Penumpang: " . $passengerCount . "<br>";
// echo "Date: " . $date . "<br>";

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
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      top: -200px;
      right: -200px;
    }

    .decoration-circle:nth-child(2) {
      width: 400px;
      height: 400px;
      background: linear-gradient(225deg, #388E3C, #4CAF50);
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
      padding: 80px 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .success-container {
      background: white;
      padding: 60px 50px;
      border-radius: 35px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      max-width: 700px;
      width: 100%;
      text-align: center;
      border: 3px solid rgba(76, 175, 80, 0.15);
      position: relative;
      transition: all 0.3s ease;
    }

    .success-container:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
      border-color: #4CAF50;
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

    .success-icon {
      font-size: 100px;
      margin-bottom: 25px;
      animation: bounce 1s ease;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }

    .success-title {
      font-size: 42px;
      font-weight: 800;
      color: #4CAF50;
      margin-bottom: 15px;
      letter-spacing: -1px;
    }

    .success-subtitle {
      font-size: 18px;
      color: #666;
      margin-bottom: 40px;
      font-weight: 500;
    }

    .booking-code {
      background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(56, 142, 60, 0.1));
      padding: 25px;
      border-radius: 20px;
      margin: 30px 0;
      border: 2px solid rgba(76, 175, 80, 0.3);
    }

    .booking-code-label {
      font-size: 14px;
      color: #388E3C;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    .booking-code-value {
      font-size: 32px;
      font-weight: 800;
      color: #4CAF50;
      letter-spacing: 3px;
      font-family: monospace;
    }

    .summary-box {
      background: #f8f9fa;
      padding: 30px;
      border-radius: 20px;
      margin: 30px 0;
      text-align: left;
      border: 2px solid rgba(75, 171, 255, 0.1);
    }

    .summary-title {
      font-size: 20px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 20px;
      text-align: center;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid #e0e0e0;
      align-items: center;
    }

    .summary-row:last-child {
      border-bottom: none;
    }

    .summary-label {
      font-weight: 700;
      color: #666;
    }

    .summary-value {
      font-weight: 600;
      color: #1a1a1a;
    }

    .seats-list {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: flex-end;
    }

    .seat-badge {
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      color: white;
      padding: 6px 14px;
      border-radius: 15px;
      font-weight: 700;
      font-size: 13px;
    }

    .total-row {
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      color: white;
      padding: 20px;
      border-radius: 12px;
      margin-top: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .total-label {
      font-size: 18px;
      font-weight: 700;
    }

    .total-amount {
      font-size: 28px;
      font-weight: 800;
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 40px;
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
      display: inline-block;
      text-align: center;
    }

    .btn-primary {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(75, 171, 255, 0.6);
    }

    .btn-secondary {
      background: rgba(76, 175, 80, 0.1);
      color: #388E3C;
      border: 2px solid rgba(76, 175, 80, 0.3);
    }

    .btn-secondary:hover {
      background: rgba(76, 175, 80, 0.15);
      transform: translateY(-3px);
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

      .success-container {
        padding: 40px 25px;
      }

      .success-icon {
        font-size: 70px;
      }

      .success-title {
        font-size: 32px;
      }

      .booking-code-value {
        font-size: 24px;
      }

      .action-buttons {
        flex-direction: column;
      }

      .summary-row {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
      }

      .seats-list {
        justify-content: flex-start;
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