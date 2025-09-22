<?php
$penumpang = isset($_GET['penumpang']) ? (int)$_GET['penumpang'] : 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AIRtix.id - Pilih Kursi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: "Space Grotesk", sans-serif;
    }
    body {
      background: #f8f9fa;
      color: #1a1a1a;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    header {
      background: rgb(75, 171, 255);
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    header h1 {
      font-size: 28px;
      font-weight: 700;
    }
    .container {
      flex: 1;
      max-width: 600px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #1976D2;
    }
    .seats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      justify-items: center;
      margin-bottom: 20px;
    }
    .seat {
      width: 50px;
      height: 50px;
      background: #ddd;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }
    .seat.selected {
      background: rgb(75,171,255);
      color: white;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      background: rgb(75,171,255);
      border: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 600;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
      text-decoration: none;
    }
    .btn:hover {
      background: #1976D2;
    }
    footer {
      background: #1a1a1a;
      color: #ccc;
      text-align: center;
      padding: 20px;
      margin-top: auto;
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <h1>AIRtix.id</h1>
  </header>

  <!-- BACK BUTTON -->
  <div style="max-width: 600px; margin: 20px auto; text-align: left;">
    <a href="search.php" class="btn">← Kembali</a>
  </div>

  <!-- CONTENT -->
  <div class="container">
    <h2>Pilih Kursi (<?php echo $penumpang; ?> Penumpang)</h2>
    <form id="seatForm" method="get" action="BookingPayment.php">
        <div class="seats">
            <?php for ($i = 1; $i <= 20; $i++): ?>
                <div class="seat" data-seat="K<?php echo $i; ?>">K<?php echo $i; ?></div>
            <?php endfor; ?>
        </div>
        <input type="hidden" name="seats" id="selectedSeats">
        <input type="hidden" name="passenger" value="<?php echo $penumpang; ?>">
        <button type="submit" class="btn">Lanjutkan</button>
    </form>
  </div>

  <!-- FOOTER -->
  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>

  <script>
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsInput = document.getElementById('selectedSeats');
    const seatForm = document.getElementById('seatForm');
    const maxSeats = <?php echo $penumpang; ?>;

    let selected = [];

    seats.forEach(seat => {
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
            alert(`Anda hanya bisa memilih ${maxSeats} kursi.`);
          }
        }

        selectedSeatsInput.value = selected.join(',');
      });
    });

    seatForm.addEventListener('submit', (e) => {
      if (selected.length !== maxSeats) {
        e.preventDefault();
        alert(`Silakan pilih tepat ${maxSeats} kursi.`);
      }
    });
  </script>
</body>
</html>
