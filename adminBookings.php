<?php
session_start();
include 'connector.php';
include 'headerFooter.php';
include 'csrfHelper.php';

requireLogin();
requireAdmin();
initSecureSession();

$username = $_SESSION['username'];
$message = "";
$messageType = "";

// Handle Delete Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_pemesanan = intval($_POST['id_pemesanan']);
        
        $deleteQuery = "DELETE FROM pemesanan WHERE id_pemesanan = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $id_pemesanan);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Pemesanan berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Gagal menghapus pemesanan!";
            $messageType = "error";
        }
        mysqli_stmt_close($stmt);
        regenerateCsrfToken();
    }
}

// Handle Update Check-in Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_checkin'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_pemesanan = intval($_POST['id_pemesanan']);
        $current_status = intval($_POST['current_status']);
        $new_status = $current_status == 1 ? 0 : 1;
        
        $updateQuery = "UPDATE pemesanan SET checkin = ? WHERE id_pemesanan = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ii", $new_status, $id_pemesanan);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Status check-in berhasil diubah!";
            $messageType = "success";
        } else {
            $message = "Gagal mengubah status check-in!";
            $messageType = "error";
        }
        mysqli_stmt_close($stmt);
        regenerateCsrfToken();
    }
}

// Get all bookings with user and ticket info
$query = "SELECT p.*, u.username, u.name, u.email_user, t.asal_kota, t.tujuan_kota, m.nama_maskapai 
          FROM pemesanan p
          INNER JOIN user u ON p.id_user = u.id_user
          INNER JOIN tiket t ON p.id_tiket = t.id_tiket
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai
          ORDER BY p.id_pemesanan DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Calculate statistics
$totalBookings = count($bookings);
$totalRevenue = array_sum(array_column($bookings, 'harga_total'));
$checkedIn = count(array_filter($bookings, function($b) { return $b['checkin'] == 1; }));
$pending = $totalBookings - $checkedIn;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Pemesanan - AIRtix.id Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/headerFooter.css">
  <link rel="stylesheet" href="styles/adminPages.css">
</head>
<body>
  <?php
  renderBackgroundDecorations();
  renderHeader($username, $conn);
  ?>

  <main>
    <div class="container">
      <div class="page-header">
        <div>
          <a href="adminDashboard.php" class="back-btn">← Kembali ke Dashboard</a>
          <h1 class="page-title">📋 Kelola Pemesanan</h1>
          <p class="page-subtitle">Manage semua pemesanan tiket</p>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <!-- Statistics Cards -->
      <div class="stats-cards">
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <div class="stat-label">Total Pemesanan</div>
            <div class="stat-value"><?php echo number_format($totalBookings); ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">💰</div>
          <div class="stat-info">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <div class="stat-label">Sudah Check-in</div>
            <div class="stat-value"><?php echo number_format($checkedIn); ?></div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⏳</div>
          <div class="stat-info">
            <div class="stat-label">Belum Check-in</div>
            <div class="stat-value"><?php echo number_format($pending); ?></div>
          </div>
        </div>
      </div>

      <div class="data-table">
        <div class="table-header">
          <h2>📊 Daftar Pemesanan</h2>
        </div>
        
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Kode Booking</th>
                <th>User</th>
                <th>Rute</th>
                <th>Maskapai</th>
                <th>Tanggal</th>
                <th>Penumpang</th>
                <th>Kursi</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $booking): 
                $seats = !empty($booking['kursi_dipilih']) ? explode(',', $booking['kursi_dipilih']) : [];
              ?>
                <tr>
                  <td><?php echo $booking['id_pemesanan']; ?></td>
                  <td><strong><?php echo htmlspecialchars($booking['kode_pemesanan'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                  <td>
                    <div><?php echo htmlspecialchars($booking['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <small style="color: #999;"><?php echo htmlspecialchars($booking['email_user'], ENT_QUOTES, 'UTF-8'); ?></small>
                  </td>
                  <td><?php echo htmlspecialchars($booking['asal_kota'], ENT_QUOTES, 'UTF-8'); ?> → <?php echo htmlspecialchars($booking['tujuan_kota'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($booking['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo date('d M Y', strtotime($booking['tanggal_keberangkatan'])); ?></td>
                  <td><?php echo $booking['jumlah_penumpang']; ?> orang</td>
                  <td>
                    <div class="seats-display">
                      <?php foreach ($seats as $seat): ?>
                        <span class="seat-badge"><?php echo htmlspecialchars(trim($seat), ENT_QUOTES, 'UTF-8'); ?></span>
                      <?php endforeach; ?>
                    </div>
                  </td>
                  <td><strong>Rp <?php echo number_format($booking['harga_total'], 0, ',', '.'); ?></strong></td>
                  <td>
                    <?php if ($booking['checkin'] == 1): ?>
                      <span class="badge badge-success">✅ Check-in</span>
                    <?php else: ?>
                      <span class="badge badge-warning">⏳ Pending</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <form method="POST" style="display: inline;">
                        <?php echo csrfTokenInput(); ?>
                        <input type="hidden" name="id_pemesanan" value="<?php echo $booking['id_pemesanan']; ?>">
                        <input type="hidden" name="current_status" value="<?php echo $booking['checkin']; ?>">
                        <button type="submit" name="toggle_checkin" class="btn-action btn-edit" 
                                onclick="return confirm('Ubah status check-in?')">
                          🔄 Toggle
                        </button>
                      </form>
                      
                      <form method="POST" style="display: inline;">
                        <?php echo csrfTokenInput(); ?>
                        <input type="hidden" name="id_pemesanan" value="<?php echo $booking['id_pemesanan']; ?>">
                        <button type="submit" name="delete_booking" class="btn-action btn-delete" 
                                onclick="return confirm('Yakin ingin menghapus pemesanan ini?')">
                          🗑️ Hapus
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <?php renderFooter(); ?>

  <script>
    const alert = document.querySelector('.alert');
    if (alert) {
      setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => alert.remove(), 500);
      }, 5000);
    }
  </script>
</body>
</html>