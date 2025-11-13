<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

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
  <link rel="stylesheet" href="styles/seat.css">
  <link rel="stylesheet" href="styles/headerfooter.css">
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
        <li><a href="profile.php" class="username-btn">👋 <?php echo htmlspecialchars($username); ?></a></li>
        <li><a href="history.php">📋 Riwayat</a></li>
        <li><a href="checkin.php">✅ Check-in</a></li>
        <li><a class="logout-btn" href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="back-wrapper">
      <a href="javascript:history.back()" class="back-btn">← Kembali</a>
    </div>

    <div class="container">
      <h2>Pilih Kursi Penumpang</h2>
      
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