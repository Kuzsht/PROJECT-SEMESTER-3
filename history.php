<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data booking dari session (dalam implementasi real, ambil dari database)
$bookings = isset($_SESSION['bookings']) ? $_SESSION['bookings'] : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pemesanan - AIRtix.id</title>
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
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(75, 171, 255, 0.6);
    }

    /* Booking Cards */
    .bookings-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      gap: 30px;
      animation: slideUp 0.6s ease-out;
    }

    .booking-card {
      background: white;
      border-radius: 25px;
      padding: 35px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      border: 3px solid rgba(75, 171, 255, 0.08);
      position: relative;
      transition: all 0.3s ease;
    }

    .booking-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
      border-color: rgb(75, 171, 255);
    }

    .booking-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      border-radius: 25px 25px 0 0;
    }

    .booking-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 2px solid rgba(75, 171, 255, 0.1);
    }

    .booking-code {
      font-size: 20px;
      font-weight: 800;
      color: #1976D2;
      font-family: monospace;
      letter-spacing: 1px;
    }

    .booking-status {
      padding: 8px 18px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-pending {
      background: linear-gradient(135deg, #FFA726, #FB8C00);
      color: white;
      box-shadow: 0 4px 15px rgba(255, 167, 38, 0.3);
    }

    .status-checkedin {
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      color: white;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .booking-route {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 25px;
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.05), rgba(25, 118, 210, 0.05));
      padding: 20px;
      border-radius: 15px;
    }

    .route-city {
      flex: 1;
      text-align: center;
    }

    .route-city-name {
      font-size: 24px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 5px;
    }

    .route-icon {
      font-size: 28px;
      color: rgb(75, 171, 255);
    }

    .booking-details {
      margin-bottom: 20px;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      font-weight: 700;
      color: #666;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .detail-value {
      font-weight: 600;
      color: #1a1a1a;
    }

    .seats-mini {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      justify-content: flex-end;
    }

    .seat-mini {
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      color: white;
      padding: 4px 10px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 12px;
    }

    .booking-total {
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      color: white;
      padding: 18px;
      border-radius: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }

    .total-label {
      font-size: 15px;
      font-weight: 700;
    }

    .total-amount {
      font-size: 24px;
      font-weight: 800;
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

      .bookings-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .booking-card {
        padding: 25px 20px;
      }

      .route-city-name {
        font-size: 20px;
      }

      .booking-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
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
        <li><a href="checkin.php">✅ Check-in</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h1 class="page-title">📋 Riwayat Pemesanan</h1>

    <?php if (empty($bookings)): ?>
      <!-- Empty State -->
      <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h2 class="empty-title">Belum Ada Riwayat Pemesanan</h2>
        <p class="empty-text">Anda belum melakukan pemesanan tiket. Mulai perjalanan Anda sekarang!</p>
        <a href="search.php" class="btn">🚀 Pesan Tiket Sekarang</a>
      </div>
    <?php else: ?>
      <!-- Booking Cards -->
      <div class="bookings-grid">
        <?php foreach (array_reverse($bookings) as $booking): ?>
          <div class="booking-card">
            <div class="booking-header">
              <div class="booking-code"><?php echo htmlspecialchars($booking['booking_code']); ?></div>
              <span class="booking-status <?php echo $booking['status'] === 'Sudah Check-in' ? 'status-checkedin' : 'status-pending'; ?>">
                <?php echo htmlspecialchars($booking['status']); ?>
              </span>
            </div>

            <div class="booking-route">
              <div class="route-city">
                <div class="route-city-name"><?php echo htmlspecialchars($booking['from']); ?></div>
                <small>🛫 Keberangkatan</small>
              </div>
              <div class="route-icon">✈️</div>
              <div class="route-city">
                <div class="route-city-name"><?php echo htmlspecialchars($booking['to']); ?></div>
                <small>🛬 Tujuan</small>
              </div>
            </div>

            <div class="booking-details">
              <div class="detail-row">
                <span class="detail-label">📅 Tanggal</span>
                <span class="detail-value"><?php echo date('d M Y', strtotime($booking['date'])); ?></span>
              </div>

              <div class="detail-row">
                <span class="detail-label">👥 Penumpang</span>
                <span class="detail-value"><?php echo $booking['passenger']; ?> Orang</span>
              </div>

              <div class="detail-row">
                <span class="detail-label">🪑 Kursi</span>
                <div class="seats-mini">
                  <?php foreach ($booking['seats'] as $seat): ?>
                    <span class="seat-mini"><?php echo htmlspecialchars($seat); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="detail-row">
                <span class="detail-label">🕒 Dibuat</span>
                <span class="detail-value"><?php echo date('d M Y H:i', strtotime($booking['created_at'])); ?></span>
              </div>
            </div>

            <div class="booking-total">
              <span class="total-label">Total Pembayaran:</span>
              <span class="total-amount">Rp <?php echo number_format($booking['total'], 0, ',', '.'); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>