<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $penumpang = $_POST['penumpang'];
  header("Location: seat.php?penumpang=$penumpang");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cari Penerbangan - AIRtix.id</title>
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

    /* Navbar Clean Premium - SAMA DENGAN LANDING PAGE */
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
      padding: 80px 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .search-container {
      background: white;
      padding: 60px 50px;
      border-radius: 35px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      max-width: 600px;
      width: 100%;
      position: relative;
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

    .search-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      border-radius: 35px 35px 0 0;
    }

    .search-container h2 {
      margin-bottom: 40px;
      color: #1976D2;
      font-size: 42px;
      font-weight: 800;
      text-align: center;
      letter-spacing: -1px;
      position: relative;
      padding-bottom: 20px;
    }

    .search-container h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, transparent, rgb(75, 171, 255), #1976D2, transparent);
      border-radius: 2px;
    }

    .form-group {
      margin-bottom: 28px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 700;
      color: #1976D2;
      font-size: 16px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    input, select {
      width: 100%;
      padding: 18px 20px;
      border: 2px solid #e0e0e0;
      border-radius: 15px;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s ease;
      background: #f8f9fa;
      color: #1a1a1a;
    }

    input:focus, select:focus {
      outline: none;
      border-color: rgb(75, 171, 255);
      background: white;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.15);
      transform: translateY(-2px);
    }

    input::placeholder {
      color: #999;
      font-weight: 500;
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

    button {
      width: 100%;
      padding: 20px;
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      border: none;
      border-radius: 15px;
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.4s ease;
      text-transform: uppercase;
      letter-spacing: 2px;
      box-shadow: 0 10px 35px rgba(75, 171, 255, 0.4);
      margin-top: 15px;
    }

    button:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 45px rgba(75, 171, 255, 0.6);
    }

    button:active {
      transform: translateY(-2px);
    }

    .back-btn {
      display: inline-block;
      margin-top: 25px;
      padding: 16px 35px;
      background: rgba(75, 171, 255, 0.1);
      color: #1976D2;
      text-decoration: none;
      border-radius: 15px;
      font-weight: 700;
      transition: all 0.3s ease;
      border: 2px solid rgba(75, 171, 255, 0.2);
      text-align: center;
      display: block;
      letter-spacing: 1px;
    }

    .back-btn:hover {
      background: rgba(75, 171, 255, 0.15);
      border-color: rgb(75, 171, 255);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(75, 171, 255, 0.2);
    }

    /* Footer - SAMA DENGAN LANDING PAGE */
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

    /* Icon Enhancement */
    .form-group {
      position: relative;
    }

    .icon-wrapper {
      position: relative;
    }

    .icon-wrapper::before {
      content: attr(data-icon);
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 20px;
      pointer-events: none;
      z-index: 1;
    }

    .icon-wrapper input,
    .icon-wrapper select {
      padding-left: 55px;
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

      .search-container {
        padding: 40px 30px;
      }

      .search-container h2 {
        font-size: 32px;
        margin-bottom: 30px;
      }

      label {
        font-size: 14px;
      }

      input, select {
        padding: 16px 18px;
        font-size: 15px;
      }

      button {
        padding: 18px;
        font-size: 16px;
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
    <div class="search-container">
      <h2>🔍 Cari Penerbangan</h2>
      <form method="get" action="seat.php">
        <div class="form-group">
          <label for="from">🛫 Dari</label>
          <div class="icon-wrapper" data-icon="📍">
            <input type="text" id="from" name="from" placeholder="Masukkan Kota Asal" required>
          </div>
        </div>

        <div class="form-group">
          <label for="to">🛬 Ke</label>
          <div class="icon-wrapper" data-icon="📍">
            <input type="text" id="to" name="to" placeholder="Masukkan Kota Tujuan" required>
          </div>
        </div>

        <div class="form-group">
          <label for="date">📅 Tanggal Keberangkatan</label>
          <div class="icon-wrapper" data-icon="📆">
            <input type="date" id="date" name="date" required>
          </div>
        </div>

        <div class="form-group">
          <label for="penumpang">👥 Jumlah Penumpang</label>
          <div class="icon-wrapper" data-icon="👤">
            <select id="penumpang" name="penumpang" required>
              <option value="1">1 Penumpang</option>
              <option value="2">2 Penumpang</option>
              <option value="3">3 Penumpang</option>
              <option value="4">4 Penumpang</option>
            </select>
          </div>
        </div>

        <button type="submit">🚀 Cari Penerbangan</button>
      </form>
      <a href="LandingPage.php" class="back-btn">← Kembali ke Beranda</a>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>