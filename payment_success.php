<?php
// Ambil data dari form pembayaran
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$card = isset($_POST['card']) ? $_POST['card'] : '';
$exp  = isset($_POST['exp']) ? $_POST['exp'] : '';
$cvv  = isset($_POST['cvv']) ? $_POST['cvv'] : '';

// Ambil data kursi & penumpang dari GET (jika ada)
$seats = isset($_GET['seats']) ? explode(",", $_GET['seats']) : [];
$passengerCount = isset($_GET['passenger']) ? intval($_GET['passenger']) : 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran Berhasil - AIRtix.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: "Space Grotesk", sans-serif;
      background: #f9f9f9;
      color: #333;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      overflow-x: hidden;
    }
    header {
      background: rgb(75, 171, 255);
      color: white;
      padding: 15px;
      text-align: center;
      width: 100%;
    }
    h1 {
      font-size: 24px;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      flex: 1;
    }
    .success-icon {
      font-size: 60px;
      color: #28a745;
      margin-bottom: 20px;
    }
    h2 {
      font-size: 22px;
      margin-bottom: 15px;
      color: #28a745;
    }
    .summary {
      background: #f1f7ff;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
      text-align: left;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background: linear-gradient(135deg, #4babff, #1e90ff);
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      text-decoration: none;
      transition: 0.3s;
    }
    .btn:hover {
      background: #0077e6;
    }
    footer {
      background: #1a1a1a;
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: auto;
    }
  </style>
</head>
<body>
  <header>
    <h1>AIRtix.id</h1>
  </header>

  <div class="container">
    <div class="success-icon">✅</div>
    <h2>Pembayaran Berhasil!</h2>
    <p>Terima kasih, pembayaran Anda telah diterima.</p>

    <div class="summary">
      <p><strong>Jumlah Penumpang:</strong> <?php echo $passengerCount; ?></p>
      <p><strong>Kursi:</strong> <?php echo $seats ? implode(", ", $seats) : "Tidak tersedia"; ?></p>
    </div>

    <a href="history.php" class="btn">Lihat Riwayat Pemesanan</a>
  </div>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>
</body>
</html>
