<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$success = false;

// Handle check-in submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pemesanan'])) {
    $id_pemesanan = intval($_POST['id_pemesanan']);
    
    // Update checkin status di database
    $updateQuery = "UPDATE pemesanan SET checkin = 1 WHERE id_pemesanan = ? AND id_user = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ii", $id_pemesanan, $id_user);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    
    mysqli_stmt_close($stmt);
}

// Ambil data booking yang belum check-in dari database
$query = "SELECT p.*, t.asal_kota, t.tujuan_kota, m.nama_maskapai 
          FROM pemesanan p
          INNER JOIN tiket t ON p.id_tiket = t.id_tiket
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai
          WHERE p.id_user = ? AND p.checkin = 0
          ORDER BY p.tanggal_keberangkatan ASC, p.id_pemesanan DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pendingBookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pendingBookings[] = $row;
}

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check-in Online - AIRtix.id</title>
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

    .success-alert {
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      color: white;
      padding: 25px 35px;
      border-radius: 20px;
      margin-bottom: 40px;
      display: flex;
      align-items: center;
      gap: 20px;
      box-shadow: 0 10px 35px rgba(76, 175, 80, 0.3);
      animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .success-alert .icon {
      font-size: 42px;
    }

    .success-alert .text {
      flex: 1;
    }

    .success-alert .title {
      font-size: 20px;
      font-weight: 800;
      margin-bottom: 5px;
    }

    .success-alert .desc {
      font-size: 15px;
      opacity: 0.95;
    }

    .empty-state {
      text-align: center;
      padding: 80px 40px;
      background: white;
      border-radius: 25px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      border: 3px solid rgba(75, 171, 255, 0.08);
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

    .empty-icon {
      font-size: 100px;
      margin-bottom: 25px;
      opacity: 0.3;
    }

    .empty-title {
      font-size: 28px;
      font-weight: 800;
      color: #666;
      margin-bottom: 15px;
    }

    .empty-text {
      font-size: 16px;
      color: #999;
      margin-bottom: 35px;
    }

    .btn {
      display: inline-block;
      padding: 18px 40px;
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 700;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.4);
      border: none;
      cursor: pointer;
      font-size: 16px;
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(75, 171, 255, 0.6);
    }

    .checkin-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
      gap: 30px;
      animation: slideUp 0.6s ease-out;
    }

    .checkin-card {
      background: white;
      border-radius: 25px;
      padding: 40px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      border: 3px solid rgba(75, 171, 255, 0.08);
      position: relative;
      transition: all 0.3s ease;
    }

    .checkin-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
      border-color: rgb(75, 171, 255);
    }

    .checkin-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      border-radius: 25px 25px 0 0;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 2px solid rgba(75, 171, 255, 0.1);
    }

    .booking-code-display {
      font-size: 22px;
      font-weight: 800;
      color: #1976D2;
      font-family: monospace;
      letter-spacing: 1px;
    }

    .route-display {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.08), rgba(25, 118, 210, 0.08));
      padding: 25px;
      border-radius: 15px;
    }

    .city-display {
      flex: 1;
      text-align: center;
    }

    .city-name {
      font-size: 26px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 8px;
    }

    .city-label {
      font-size: 13px;
      color: #666;
      font-weight: 600;
    }

    .plane-icon {
      font-size: 32px;
      margin: 0 20px;
    }

    .flight-details {
      margin-bottom: 30px;
    }

    .detail-item {
      display: flex;
      justify-content: space-between;
      padding: 14px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .detail-item:last-child {
      border-bottom: none;
    }

    .detail-label {
      font-weight: 700;
      color: #666;
      font-size: 15px;
    }

    .detail-value {
      font-weight: 600;
      color: #1a1a1a;
      font-size: 15px;
    }

    .seats-display-mini {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      justify-content: flex-end;
    }

    .seat-badge-mini {
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      color: white;
      padding: 5px 12px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 12px;
    }

    .checkin-form {
      margin-top: 25px;
    }

    .checkin-btn {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 17px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
    }

    .checkin-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(76, 175, 80, 0.6);
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

      .page-title {
        font-size: 32px;
        margin-bottom: 30px;
      }

      .checkin-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .checkin-card {
        padding: 30px 20px;
      }

      .city-name {
        font-size: 22px;
      }

      .plane-icon {
        font-size: 24px;
        margin: 0 10px;
      }

      .success-alert {
        flex-direction: column;
        text-align: center;
        padding: 25px 20px;
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
      </ul>
    </nav>
  </header>

  <main>
    <h1 class="page-title">✅ Check-in Online</h1>

    <?php if ($success): ?>
      <div class="success-alert">
        <div class="icon">✅</div>
        <div class="text">
          <div class="title">Check-in Berhasil!</div>
          <div class="desc">Anda telah berhasil melakukan check-in. Selamat menikmati penerbangan!</div>
        </div>
      </div>
    <?php endif; ?>

    <?php if (empty($pendingBookings)): ?>
      <!-- Empty State -->
      <div class="empty-state">
        <div class="empty-icon">✈️</div>
        <h2 class="empty-title">Tidak Ada Booking untuk Check-in</h2>
        <p class="empty-text">Anda belum memiliki booking yang perlu di-check-in. Pesan tiket terlebih dahulu!</p>
        <a href="search.php" class="btn">🚀 Pesan Tiket Sekarang</a>
      </div>
    <?php else: ?>
      <!-- Check-in Cards -->
      <div class="checkin-grid">
        <?php foreach ($pendingBookings as $booking): 
          // Konversi kursi dari string ke array
          $seats = !empty($booking['kursi_dipilih']) ? explode(',', $booking['kursi_dipilih']) : [];
        ?>
          <div class="checkin-card">
            <div class="card-header">
              <div class="booking-code-display"><?php echo htmlspecialchars($booking['kode_pemesanan']); ?></div>
            </div>

            <div class="route-display">
              <div class="city-display">
                <div class="city-name"><?php echo htmlspecialchars($booking['asal_kota']); ?></div>
                <div class="city-label">🛫 Keberangkatan</div>
              </div>
              <div class="plane-icon">✈️</div>
              <div class="city-display">
                <div class="city-name"><?php echo htmlspecialchars($booking['tujuan_kota']); ?></div>
                <div class="city-label">🛬 Tujuan</div>
              </div>
            </div>

            <div class="flight-details">
              <div class="detail-item">
                <span class="detail-label">✈️ Maskapai</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['nama_maskapai']); ?></span>
              </div>

              <div class="detail-item">
                <span class="detail-label">📅 Tanggal Keberangkatan</span>
                <span class="detail-value"><?php echo date('d M Y', strtotime($booking['tanggal_keberangkatan'])); ?></span>
              </div>

              <div class="detail-item">
                <span class="detail-label">👥 Jumlah Penumpang</span>
                <span class="detail-value"><?php echo $booking['jumlah_penumpang']; ?> Orang</span>
              </div>

              <div class="detail-item">
                <span class="detail-label">🪑 Kursi</span>
                <div class="seats-display-mini">
                  <?php foreach ($seats as $seat): ?>
                    <span class="seat-badge-mini"><?php echo htmlspecialchars(trim($seat)); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="detail-item">
                <span class="detail-label">💰 Total Pembayaran</span>
                <span class="detail-value">Rp <?php echo number_format($booking['harga_total'], 0, ',', '.'); ?></span>
              </div>
            </div>

            <form method="POST" class="checkin-form">
              <input type="hidden" name="id_pemesanan" value="<?php echo $booking['id_pemesanan']; ?>">
              <button type="submit" class="checkin-btn">✅ Check-in Sekarang</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>

  <script>
    // Auto-hide success alert after 5 seconds
    const successAlert = document.querySelector('.success-alert');
    if (successAlert) {
      setTimeout(() => {
        successAlert.style.transition = 'all 0.5s ease';
        successAlert.style.opacity = '0';
        successAlert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          successAlert.remove();
        }, 500);
      }, 5000);
    }

    // Confirm before check-in
    document.querySelectorAll('.checkin-form').forEach(form => {
      form.addEventListener('submit', (e) => {
        if (!confirm('Apakah Anda yakin ingin melakukan check-in?')) {
          e.preventDefault();
        }
      });
    });
  </script>
</body>
</html>