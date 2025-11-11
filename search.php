<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil semua tiket dari database dengan JOIN ke tabel maskapai
$query = "SELECT t.*, m.nama_maskapai, m.harga_satukursi 
          FROM tiket t 
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai 
          ORDER BY t.asal_kota, t.tujuan_kota";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Penerbangan - AIRtix.id</title>
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
      max-width: 1400px;
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

    .flights-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
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

    .flight-card {
      background: white;
      border-radius: 25px;
      padding: 30px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      border: 3px solid rgba(75, 171, 255, 0.08);
      transition: all 0.3s ease;
      position: relative;
    }

    .flight-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
      border-color: rgb(75, 171, 255);
    }

    .flight-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      border-radius: 25px 25px 0 0;
    }

    .airline-name {
      font-size: 24px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .route-display {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 25px 0;
      padding: 20px;
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.05), rgba(25, 118, 210, 0.05));
      border-radius: 15px;
    }

    .city {
      flex: 1;
      text-align: center;
    }

    .city-name {
      font-size: 20px;
      font-weight: 800;
      color: #1976D2;
      margin-bottom: 5px;
    }

    .city-label {
      font-size: 12px;
      color: #666;
      font-weight: 600;
    }

    .plane-icon {
      font-size: 28px;
      margin: 0 15px;
    }

    .price-section {
      background: linear-gradient(135deg, #4CAF50, #388E3C);
      color: white;
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      margin: 20px 0;
    }

    .price-label {
      font-size: 14px;
      opacity: 0.9;
      margin-bottom: 5px;
    }

    .price-amount {
      font-size: 28px;
      font-weight: 800;
      letter-spacing: -1px;
    }

    .select-btn {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.4);
    }

    .select-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(75, 171, 255, 0.6);
    }

    .back-wrapper {
      max-width: 1400px;
      width: 100%;
      margin-bottom: 25px;
    }

    .back-btn {
      display: inline-block;
      padding: 14px 30px;
      background: rgba(75, 171, 255, 0.1);
      color: #1976D2;
      text-decoration: none;
      border-radius: 15px;
      font-weight: 700;
      transition: all 0.3s ease;
      border: 2px solid rgba(75, 171, 255, 0.2);
      letter-spacing: 1px;
      font-size: 15px;
    }

    .back-btn:hover {
      background: rgba(75, 171, 255, 0.15);
      border-color: rgb(75, 171, 255);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(75, 171, 255, 0.2);
    }

    .empty-state {
      text-align: center;
      padding: 80px 40px;
      background: white;
      border-radius: 25px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      border: 3px solid rgba(75, 171, 255, 0.08);
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

      .flights-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .flight-card {
        padding: 25px 20px;
      }

      .airline-name {
        font-size: 20px;
      }

      .city-name {
        font-size: 18px;
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
    <div class="back-wrapper">
      <a href="LandingPage.php" class="back-btn">← Kembali ke Beranda</a>
    </div>

    <h1 class="page-title">Pilih Penerbangan</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="flights-grid">
        <?php while ($ticket = mysqli_fetch_assoc($result)): ?>
          <div class="flight-card">
            <div class="airline-name">
              ✈️ <?php echo htmlspecialchars($ticket['nama_maskapai']); ?>
            </div>

            <div class="route-display">
              <div class="city">
                <div class="city-name"><?php echo htmlspecialchars($ticket['asal_kota']); ?></div>
                <div class="city-label">🛫 Keberangkatan</div>
              </div>
              <div class="plane-icon">→</div>
              <div class="city">
                <div class="city-name"><?php echo htmlspecialchars($ticket['tujuan_kota']); ?></div>
                <div class="city-label">🛬 Tujuan</div>
              </div>
            </div>

            <div class="price-section">
              <div class="price-label">Harga per Kursi</div>
              <div class="price-amount">Rp <?php echo number_format($ticket['harga_satukursi'], 0, ',', '.'); ?></div>
            </div>

            <form action="inputsearch.php" method="get">
              <input type="hidden" name="id_tiket" value="<?php echo $ticket['id_tiket']; ?>">
              <input type="hidden" name="from" value="<?php echo htmlspecialchars($ticket['asal_kota']); ?>">
              <input type="hidden" name="to" value="<?php echo htmlspecialchars($ticket['tujuan_kota']); ?>">
              <input type="hidden" name="airline" value="<?php echo htmlspecialchars($ticket['nama_maskapai']); ?>">
              <input type="hidden" name="price" value="<?php echo $ticket['harga_satukursi']; ?>">
              <button type="submit" class="select-btn">🎫 Pilih Penerbangan</button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">✈️</div>
        <h2 class="empty-title">Tidak Ada Penerbangan Tersedia</h2>
        <p style="color: #999; margin-bottom: 25px;">Belum ada tiket penerbangan yang terdaftar di sistem.</p>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>