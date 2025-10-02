<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
$email = $_SESSION['email'];
$username = $_SESSION['username'] ?? 'User';
$name = $_SESSION['name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AIRtix.id - Pesan Tiket Pesawat Mudah</title>
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
    }

    /* Navbar dengan style index.php */
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
      box-shadow: 0 4px 20px rgba(75, 171, 255, 0.3);
    }

    header h1 {
      font-size: 32px;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 15px;
      align-items: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 10px 18px;
      border-radius: 12px;
    }

    nav a:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }

    .username {
      font-weight: 600;
      padding: 10px 20px;
      background: rgba(255,255,255,0.2);
      border-radius: 25px;
      animation: slideIn 0.8s ease;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .logout-btn {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      padding: 10px 20px;
      border-radius: 25px;
      text-decoration: none;
      color: white;
      font-weight: 700;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
    }

    .logout-btn:hover {
      transform: translateY(-2px);
      background: white;
      color: #e74c3c;
      box-shadow: 0px 10px 20px rgba(255, 255, 255, 0.5);
    }

    /* Hero Section - Style mirip index.php */
    .hero {
      min-height: 85vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.95), rgba(25, 118, 210, 0.95)),
                  url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1600') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 40px 20px;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at center, transparent 0%, rgba(75, 171, 255, 0.3) 100%);
      animation: pulse 8s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.5; }
      50% { opacity: 1; }
    }

    .hero-content {
      position: relative;
      z-index: 1;
      animation: fadeInUp 1s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .hero h2 {
      font-size: 70px;
      font-weight: 700;
      margin-bottom: 25px;
      text-shadow: 3px 3px 6px rgba(0,0,0,0.2);
      line-height: 1.2;
    }

    .hero p {
      font-size: 24px;
      margin-bottom: 40px;
      opacity: 0.95;
      font-weight: 300;
    }

    .hero button {
      padding: 18px 50px;
      background: white;
      border: none;
      border-radius: 50px;
      font-size: 18px;
      font-weight: 700;
      color: rgb(75, 171, 255);
      cursor: pointer;
      transition: all 0.4s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }

    .hero button:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 12px 35px rgba(255, 255, 255, 0.5);
      background: #f0f0f0;
    }

    /* Section Title - Style index.php */
    .section-title {
      text-align: center;
      font-size: 48px;
      font-weight: 700;
      margin: 80px 0 50px;
      color: rgb(75, 171, 255);
      position: relative;
      padding-bottom: 20px;
      animation: fadeIn 1s ease 0.3s both;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      border-radius: 2px;
    }

    /* Features Cards - Style index.php */
    .features {
      padding: 0 40px 80px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 35px;
      max-width: 1200px;
      margin: auto;
    }

    .feature-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      padding: 40px 30px;
      text-align: center;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.6s ease both;
    }

    .feature-card:nth-child(1) { animation-delay: 0.1s; }
    .feature-card:nth-child(2) { animation-delay: 0.2s; }
    .feature-card:nth-child(3) { animation-delay: 0.3s; }
    .feature-card:nth-child(4) { animation-delay: 0.4s; }

    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      transform: scaleX(0);
      transition: transform 0.4s ease;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(75, 171, 255, 0.2);
    }

    .feature-card:hover::before {
      transform: scaleX(1);
    }

    .feature-icon {
      font-size: 56px;
      margin-bottom: 20px;
      animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .feature-card h3 {
      font-size: 26px;
      margin-bottom: 18px;
      color: #1976D2;
      font-weight: 700;
    }

    .feature-card p {
      font-size: 16px;
      color: #666;
      margin-bottom: 25px;
      line-height: 1.6;
    }

    .feature-card a {
      display: inline-block;
      padding: 12px 32px;
      background: linear-gradient(135deg, rgb(75,171,255) 0%, #1976D2 100%);
      color: white;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 700;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 15px rgba(75, 171, 255, 0.3);
    }

    .feature-card a:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(75, 171, 255, 0.4);
    }

    /* Stats Section - New */
    .stats-section {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      padding: 60px 40px;
      margin: 60px 0;
      color: white;
    }

    .stats-container {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
      text-align: center;
    }

    .stat-item {
      animation: fadeInUp 0.8s ease both;
    }

    .stat-item:nth-child(1) { animation-delay: 0.1s; }
    .stat-item:nth-child(2) { animation-delay: 0.2s; }
    .stat-item:nth-child(3) { animation-delay: 0.3s; }
    .stat-item:nth-child(4) { animation-delay: 0.4s; }

    .stat-number {
      font-size: 48px;
      font-weight: 800;
      margin-bottom: 10px;
    }

    .stat-label {
      font-size: 16px;
      opacity: 0.9;
      font-weight: 500;
    }

    /* Footer - Style index.php */
    footer {
      background: #1a1a1a;
      color: #ccc;
      text-align: center;
      padding: 30px;
      margin-top: 60px;
      font-size: 15px;
    }

    @media (max-width: 968px) {
      header {
        flex-direction: column;
        gap: 15px;
        padding: 20px;
      }

      nav ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
      }

      .hero h2 {
        font-size: 42px;
      }

      .hero p {
        font-size: 18px;
      }

      .section-title {
        font-size: 36px;
        margin: 50px 0 30px;
      }

      .features {
        padding: 0 20px 50px;
        gap: 25px;
      }

      .feature-card {
        padding: 30px 20px;
      }

      .stats-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>✈️ AIRtix.id</h1>
    <nav>
      <ul>
        <li class="username">Halo, <?php echo htmlspecialchars($username); ?>!</li>
        <li><a href="history.php">📋 Riwayat</a></li>
        <li><a href="checkin.php">✅ Check-in</a></li>
        <li><a href="profile.php">👤 Profil</a></li>
        <li><a class="logout-btn" href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h2>Pesan Tiket Pesawat dengan Mudah</h2>
      <p>AIRtix.id hadir untuk memudahkan perjalanan Anda ke seluruh dunia</p>
      <button onclick="window.location.href='search.php'">🚀 Mulai Pesan Sekarang</button>
    </div>
  </section>

  <section class="stats-section">
    <div class="stats-container">
      <div class="stat-item">
        <div class="stat-number">1000+</div>
        <div class="stat-label">Penerbangan</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">50+</div>
        <div class="stat-label">Maskapai</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">100+</div>
        <div class="stat-label">Destinasi</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">10K+</div>
        <div class="stat-label">Pelanggan Puas</div>
      </div>
    </div>
  </section>

  <h2 class="section-title">Layanan AIRtix.id</h2>

  <section class="features">
    <div class="feature-card">
      <div class="feature-icon">🎫</div>
      <h3>Pesan Tiket</h3>
      <p>Cari dan pesan tiket pesawat sesuai tujuan, tanggal, dan dapatkan harga terbaik untuk perjalanan Anda.</p>
      <a href="search.php">Pesan Sekarang</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">📋</div>
      <h3>Riwayat Pemesanan</h3>
      <p>Lihat dan kelola semua riwayat pemesanan tiket Anda dengan mudah dalam satu tempat.</p>
      <a href="history.php">Lihat Riwayat</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">✅</div>
      <h3>Check-in Online</h3>
      <p>Hemat waktu Anda dengan melakukan check-in secara online sebelum keberangkatan.</p>
      <a href="checkin.php">Check-in Now</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">👤</div>
      <h3>Kelola Profil</h3>
      <p>Atur informasi akun dan preferensi perjalanan Anda untuk pengalaman yang lebih personal.</p>
      <a href="profile.php">Lihat Profil</a>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>