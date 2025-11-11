<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data dari inputsearch.php
$id_tiket = isset($_GET['id_tiket']) ? intval($_GET['id_tiket']) : 0;
$penumpang = isset($_GET['penumpang']) ? (int)$_GET['penumpang'] : 1;
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$airline = isset($_GET['airline']) ? $_GET['airline'] : '';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;

// Validasi data
if ($id_tiket == 0 || empty($from) || empty($to)) {
    header("Location: search.php");
    exit();
}

// Ambil kursi yang sudah dipesan untuk tiket dan tanggal ini
$bookedSeats = [];
$queryBooked = "SELECT kursi_dipilih FROM pemesanan 
                WHERE id_tiket = ? AND tanggal_keberangkatan = ?";
$stmt = mysqli_prepare($conn, $queryBooked);
mysqli_stmt_bind_param($stmt, "is", $id_tiket, $date);
mysqli_stmt_execute($stmt);
$resultBooked = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($resultBooked)) {
    if (!empty($row['kursi_dipilih'])) {
        $seats = explode(',', $row['kursi_dipilih']);
        $bookedSeats = array_merge($bookedSeats, $seats);
    }
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Kursi - AIRtix.id</title>
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
      flex-direction: column;
      align-items: center;
    }

    .back-wrapper {
      max-width: 900px;
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

    .container {
      background: white;
      padding: 60px 50px;
      border-radius: 35px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      max-width: 900px;
      width: 100%;
      position: relative;
      border: 3px solid rgba(75, 171, 255, 0.08);
      transition: all 0.3s ease;
    }

    .container:hover {
          transform: translateY(-8px);
          box-shadow: 0 25px 60px rgba(75, 171, 255, 0.2);
          border-color: rgb(75, 171, 255);
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

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      border-radius: 35px 35px 0 0;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #1976D2;
      font-size: 38px;
      font-weight: 800;
      letter-spacing: -1px;
      position: relative;
      padding-bottom: 15px;
    }

    h2::after {
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

    .passenger-info {
      text-align: center;
      background: linear-gradient(135deg, rgba(75, 171, 255, 0.08), rgba(25, 118, 210, 0.08));
      padding: 20px;
      border-radius: 20px;
      margin-bottom: 40px;
      border: 2px solid rgba(75, 171, 255, 0.15);
    }

    .passenger-info p {
      font-size: 18px;
      color: #1976D2;
      font-weight: 700;
      margin: 0;
    }

    .passenger-info span {
      font-size: 32px;
      font-weight: 800;
      color: rgb(75, 171, 255);
    }

    .airplane-container {
      background: linear-gradient(180deg, #e3f2fd 0%, #ffffff 100%);
      padding: 40px 30px;
      border-radius: 25px;
      border: 3px solid rgba(75, 171, 255, 0.15);
      margin-bottom: 35px;
      position: relative;
      overflow: hidden;
    }

    .airplane-container::before {
      content: '✈️';
      position: absolute;
      top: 15px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 40px;
      opacity: 0.3;
    }

    .cockpit {
      text-align: center;
      margin-bottom: 30px;
      padding-top: 20px;
    }

    .cockpit span {
      display: inline-block;
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      color: white;
      padding: 12px 40px;
      border-radius: 20px 20px 5px 5px;
      font-weight: 700;
      font-size: 16px;
      letter-spacing: 1px;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.3);
    }

    .seats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      justify-items: center;
      max-width: 450px;
      margin: 0 auto;
    }

    .seat {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
      border-radius: 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 16px;
      color: #666;
      transition: all 0.3s ease;
      border: 3px solid #ddd;
      position: relative;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .seat:hover:not(.booked) {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 8px 20px rgba(75, 171, 255, 0.3);
      border-color: rgb(75, 171, 255);
    }

    .seat.selected {
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      color: white;
      border-color: #1976D2;
      box-shadow: 0 8px 25px rgba(75, 171, 255, 0.5);
      transform: translateY(-5px) scale(1.05);
    }

    .seat.selected::after {
      content: '✓';
      position: absolute;
      top: -8px;
      right: -8px;
      background: #4CAF50;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 800;
      box-shadow: 0 2px 8px rgba(76, 175, 80, 0.5);
    }

    .seat.booked {
      background: linear-gradient(135deg, #e57373, #d32f2f);
      color: white;
      border-color: #c62828;
      cursor: not-allowed;
      opacity: 0.6;
    }

    .seat.booked::before {
      content: '✗';
      position: absolute;
      font-size: 24px;
      font-weight: 800;
    }

    .legend {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-top: 35px;
      flex-wrap: wrap;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      font-size: 15px;
      color: #666;
    }

    .legend-box {
      width: 35px;
      height: 35px;
      border-radius: 8px;
      border: 3px solid #ddd;
    }

    .legend-box.available {
      background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
    }

    .legend-box.selected-demo {
      background: linear-gradient(135deg, rgb(75, 171, 255), #1976D2);
      border-color: #1976D2;
    }

    .legend-box.booked-demo {
      background: linear-gradient(135deg, #e57373, #d32f2f);
      border-color: #c62828;
    }

    .submit-btn {
      width: 100%;
      padding: 22px;
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

    .submit-btn:hover:not(:disabled) {
      transform: translateY(-4px);
      box-shadow: 0 15px 45px rgba(75, 171, 255, 0.6);
    }

    .submit-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
      box-shadow: none;
      opacity: 0.5;
    }

    .selection-counter {
      text-align: center;
      margin-top: 25px;
      padding: 15px;
      background: linear-gradient(135deg, rgba(76, 175, 80, 0.08), rgba(56, 142, 60, 0.08));
      border-radius: 15px;
      border: 2px solid rgba(76, 175, 80, 0.2);
    }

    .selection-counter p {
      font-size: 16px;
      font-weight: 700;
      color: #388E3C;
      margin: 0;
    }

    .selection-counter span {
      font-size: 24px;
      color: #4CAF50;
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

      h2 {
        font-size: 28px;
      }

      .seats {
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
      }

      .seat {
        width: 60px;
        height: 60px;
        font-size: 14px;
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
      <a href="javascript:history.back()" class="back-btn">← Kembali</a>
    </div>

    <div class="container">
      <h2>🪑 Pilih Kursi Penumpang</h2>
      
      <div class="passenger-info">
        <p>Jumlah Penumpang: <span><?php echo $penumpang; ?></span> Orang</p>
      </div>

      <form id="seatForm" method="get" action="BookingPayment.php">
        <!-- Hidden fields -->
        <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
        <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
        <input type="hidden" name="to" value="<?php echo htmlspecialchars($to); ?>">
        <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="airline" value="<?php echo htmlspecialchars($airline); ?>">
        <input type="hidden" name="price" value="<?php echo $price; ?>">
        <input type="hidden" name="passenger" value="<?php echo $penumpang; ?>">
        <input type="hidden" name="seats" id="selectedSeats" value="">
        
        <div class="airplane-container">
          <div class="cockpit">
            <span>🧑‍✈️ KOKPIT</span>
          </div>
          
          <div class="seats">
            <?php 
            for ($i = 1; $i <= 20; $i++): 
              $seatNumber = "K" . $i;
              $isBooked = in_array($seatNumber, $bookedSeats);
            ?>
              <div class="seat <?php echo $isBooked ? 'booked' : ''; ?>" 
                   data-seat="<?php echo $seatNumber; ?>"
                   <?php echo $isBooked ? 'data-booked="true"' : ''; ?>>
                <?php echo $seatNumber; ?>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="legend">
          <div class="legend-item">
            <div class="legend-box available"></div>
            <span>Tersedia</span>
          </div>
          <div class="legend-item">
            <div class="legend-box selected-demo"></div>
            <span>Dipilih</span>
          </div>
          <div class="legend-item">
            <div class="legend-box booked-demo"></div>
            <span>Terpesan</span>
          </div>
        </div>

        <div class="selection-counter">
          <p>Kursi Terpilih: <span id="counterDisplay">0</span> / <?php echo $penumpang; ?></p>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn" disabled>🚀 Lanjutkan ke Pembayaran</button>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>

  <script>
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsInput = document.getElementById('selectedSeats');
    const seatForm = document.getElementById('seatForm');
    const submitBtn = document.getElementById('submitBtn');
    const counterDisplay = document.getElementById('counterDisplay');
    const maxSeats = <?php echo $penumpang; ?>;

    let selected = [];

    function updateCounter() {
      counterDisplay.textContent = selected.length;
      
      if (selected.length === maxSeats) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
      } else {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
      }
    }

    seats.forEach(seat => {
      // Skip kursi yang sudah dipesan
      if (seat.dataset.booked === 'true') {
        seat.addEventListener('click', () => {
          alert('⚠️ Kursi ini sudah dipesan oleh penumpang lain!');
        });
        return;
      }

      seat.addEventListener('click', () => {
        const seatNumber = seat.dataset.seat;

        if (seat.classList.contains('selected')) {
          seat.classList.remove('selected');
          selected = selected.filter(s => s !== seatNumber);
        } else {
          if (selected.length < maxSeats) {
            seat.classList.add('selected');
            selected.push(seatNumber);
          } else {
            seat.style.animation = 'shake 0.5s';
            setTimeout(() => {
              seat.style.animation = '';
            }, 500);
            
            alert(`⚠️ Anda hanya bisa memilih ${maxSeats} kursi!`);
          }
        }

        selectedSeatsInput.value = selected.join(',');
        updateCounter();
      });
    });

    seatForm.addEventListener('submit', (e) => {
      if (selected.length !== maxSeats) {
        e.preventDefault();
        alert(`⚠️ Silakan pilih tepat ${maxSeats} kursi sebelum melanjutkan!`);
      }
    });

    // Initial state
    updateCounter();
  </script>

  <style>
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }
  </style>
</body>
</html>