<?php
// Ambil data kursi & jumlah penumpang dari seat.php
$seats = isset($_GET['seats']) ? explode(",", $_GET['seats']) : [];
$passengerCount = isset($_GET['passenger']) ? intval($_GET['passenger']) : 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran - AIRtix.id</title>
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
      overflow-x: hidden; /* cegah geser kanan-kiri */
    }

    header {
      background: rgb(75, 171, 255);
      color: white;
      padding: 15px;
      text-align: center;
      width: 100%;
    }

    h1 {
      margin: 0;
      font-size: 24px;
    }

    .container {
      max-width: 600px;
      margin: 30px auto;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .section {
      margin-bottom: 25px;
    }

    .section h2 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #1e90ff;
    }

    .seats {
      background: #f1f7ff;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      word-wrap: break-word; /* biar kursi panjang tidak keluar layar */
    }

    .form-group {
      margin-bottom: 18px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #444;
    }

    input, select {
      width: 100%;
      max-width: 100%; /* cegah overflow */
      padding: 11px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-family: "Space Grotesk", sans-serif;
      font-size: 15px;
      transition: border 0.2s ease;
    }

    input:focus, select:focus {
      border-color: #1e90ff;
      outline: none;
    }

    .btn-container {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .btn-pay, .btn-back {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
      min-width: 120px;
      text-align: center;
    }

    .btn-pay {
      background: linear-gradient(135deg, #4babff, #1e90ff);
      color: white;
    }

    .btn-pay:hover {
      background: #0077e6;
    }

    .btn-back {
      background: #ccc;
      color: #333;
    }

    .btn-back:hover {
      background: #aaa;
    }

    footer {
      background: #1a1a1a;
      color: white;
      text-align: center;
      padding: 15px;
      width: 100%;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <header>
    <h1>Pembayaran</h1>
  </header>

  <div class="container">
    <!-- Ringkasan Pemesanan -->
    <div class="section">
      <h2>Ringkasan Pemesanan</h2>
      <p><strong>Jumlah Penumpang:</strong> <?php echo $passengerCount; ?></p>
      <div class="seats">
        <strong>Kursi Dipilih:</strong> 
        <?php echo $seats ? implode(", ", $seats) : "Belum ada"; ?>
      </div>
    </div>

    <!-- Form Pembayaran -->
    <div class="section">
      <h2>Informasi Pembayaran</h2>
      <form action="payment_success.php" method="POST">
        <div class="form-group">
          <label for="nama">Nama Pemilik Kartu</label>
          <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso" required>
        </div>
        <div class="form-group">
          <label for="card">Nomor Kartu</label>
          <input type="text" id="card" name="card" maxlength="16" placeholder="1234 5678 9012 3456" required>
        </div>
        <div class="form-group">
          <label for="exp">Tanggal Expired</label>
          <input type="month" id="exp" name="exp" required>
        </div>
        <div class="form-group">
          <label for="cvv">CVV</label>
          <input type="password" id="cvv" name="cvv" maxlength="3" placeholder="123" required>
        </div>

        <div class="btn-container">
          <button type="button" class="btn-back" onclick="history.back()">Kembali</button>
          <button type="submit" class="btn-pay">Bayar Sekarang</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>
</body>
</html>
