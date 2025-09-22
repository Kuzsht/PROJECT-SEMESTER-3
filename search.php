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
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Space Grotesk', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f8fb;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background-color: rgb(75, 171, 255);
      padding: 15px 30px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 1.5rem;
    }

    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 600;
    }

    nav a:hover {
      text-decoration: underline;
    }

    main {
      flex: 1;
      padding: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .search-container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      max-width: 500px;
      width: 100%;
    }

    .search-container h2 {
      margin-bottom: 20px;
      color: #000000ff;
      font-size: 1.4rem;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #333;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: rgb(75, 171, 255);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
    }

    button:hover {
      background-color: #00509e;
    }

    .back-btn {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background-color: #ccc;
      color: #333;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
    }

    .back-btn:hover {
      background-color: #aaa;
    }

    footer {
      background-color: #1a1a1a;
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

  <main>
    <div class="search-container">
      <h2>Cari Penerbangan</h2>
      <form method="get" action="seat.php">
        <div class="form-group">
          <label for="from">Dari</label>
          <input type="text" id="from" name="from" placeholder="Kota Asal" required>
        </div>

        <div class="form-group">
          <label for="to">Ke</label>
          <input type="text" id="to" name="to" placeholder="Kota Tujuan" required>
        </div>

        <div class="form-group">
          <label for="date">Tanggal Keberangkatan</label>
          <input type="date" id="date" name="date" required>
        </div>

        <div class="form-group">
          <label for="penumpang">Jumlah Penumpang</label>
          <select id="penumpang" name="penumpang" required>
            <option value="1">1 Penumpang</option>
            <option value="2">2 Penumpang</option>
            <option value="3">3 Penumpang</option>
            <option value="4">4 Penumpang</option>
          </select>
        </div>

        <button type="submit">Cari Penerbangan</button>
      </form>
      <a href="LandingPage.php" class="back-btn">← Kembali</a>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id - All rights reserved.</p>
  </footer>
</body>
</html>