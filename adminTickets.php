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

// Get all airlines for dropdown
$queryAirlines = "SELECT * FROM maskapai ORDER BY nama_maskapai";
$stmtAirlines = mysqli_prepare($conn, $queryAirlines);
mysqli_stmt_execute($stmtAirlines);
$resultAirlines = mysqli_stmt_get_result($stmtAirlines);
$airlines = mysqli_fetch_all($resultAirlines, MYSQLI_ASSOC);
mysqli_stmt_close($stmtAirlines);

// Handle Add Ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ticket'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $asal_kota = sanitizeInput($_POST['asal_kota']);
        $tujuan_kota = sanitizeInput($_POST['tujuan_kota']);
        $id_maskapai = intval($_POST['id_maskapai']);
        
        if (empty($asal_kota) || empty($tujuan_kota) || $id_maskapai <= 0) {
            $message = "Data tidak valid!";
            $messageType = "error";
        } elseif ($asal_kota === $tujuan_kota) {
            $message = "Kota asal dan tujuan tidak boleh sama!";
            $messageType = "error";
        } else {
            $insertQuery = "INSERT INTO tiket (asal_kota, tujuan_kota, id_maskapai) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($stmt, "ssi", $asal_kota, $tujuan_kota, $id_maskapai);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Tiket berhasil ditambahkan!";
                $messageType = "success";
            } else {
                $message = "Gagal menambahkan tiket!";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
        regenerateCsrfToken();
    }
}

// Handle Update Ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_tiket = intval($_POST['id_tiket']);
        $asal_kota = sanitizeInput($_POST['asal_kota']);
        $tujuan_kota = sanitizeInput($_POST['tujuan_kota']);
        $id_maskapai = intval($_POST['id_maskapai']);
        
        if (empty($asal_kota) || empty($tujuan_kota) || $id_maskapai <= 0) {
            $message = "Data tidak valid!";
            $messageType = "error";
        } elseif ($asal_kota === $tujuan_kota) {
            $message = "Kota asal dan tujuan tidak boleh sama!";
            $messageType = "error";
        } else {
            $updateQuery = "UPDATE tiket SET asal_kota = ?, tujuan_kota = ?, id_maskapai = ? WHERE id_tiket = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "ssii", $asal_kota, $tujuan_kota, $id_maskapai, $id_tiket);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Tiket berhasil diupdate!";
                $messageType = "success";
            } else {
                $message = "Gagal mengupdate tiket!";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
        regenerateCsrfToken();
    }
}

// Handle Delete Ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ticket'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_tiket = intval($_POST['id_tiket']);
        
        $deleteQuery = "DELETE FROM tiket WHERE id_tiket = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $id_tiket);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Tiket berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Gagal menghapus tiket! Mungkin masih ada pemesanan yang terkait.";
            $messageType = "error";
        }
        mysqli_stmt_close($stmt);
        regenerateCsrfToken();
    }
}

// Get all tickets with airline info
$query = "SELECT t.*, m.nama_maskapai, m.harga_satukursi 
          FROM tiket t 
          INNER JOIN maskapai m ON t.id_maskapai = m.id_maskapai 
          ORDER BY t.id_tiket DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tickets = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tiket - AIRtix.id Admin</title>
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
          <h1 class="page-title">🎫 Kelola Tiket</h1>
          <p class="page-subtitle">Manage tiket penerbangan</p>
        </div>
        <button class="btn-primary" onclick="document.getElementById('addModal').style.display='block'">
          ➕ Tambah Tiket
        </button>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <div class="data-table">
        <div class="table-header">
          <h2>📊 Daftar Tiket (Total: <?php echo count($tickets); ?>)</h2>
        </div>
        
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Rute</th>
                <th>Maskapai</th>
                <th>Harga per Kursi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tickets as $ticket): ?>
                <tr>
                  <td><?php echo $ticket['id_tiket']; ?></td>
                  <td>
                    <strong><?php echo htmlspecialchars($ticket['asal_kota'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    →
                    <strong><?php echo htmlspecialchars($ticket['tujuan_kota'], ENT_QUOTES, 'UTF-8'); ?></strong>
                  </td>
                  <td><?php echo htmlspecialchars($ticket['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>Rp <?php echo number_format($ticket['harga_satukursi'], 0, ',', '.'); ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-action btn-edit" onclick='editTicket(<?php echo json_encode($ticket); ?>)'>
                        ✏️ Edit
                      </button>
                      
                      <form method="POST" style="display: inline;">
                        <?php echo csrfTokenInput(); ?>
                        <input type="hidden" name="id_tiket" value="<?php echo $ticket['id_tiket']; ?>">
                        <button type="submit" name="delete_ticket" class="btn-action btn-delete" 
                                onclick="return confirm('Yakin ingin menghapus tiket ini?')">
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

  <!-- Add Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>➕ Tambah Tiket Baru</h2>
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
      </div>
      <form method="POST">
        <?php echo csrfTokenInput(); ?>
        <div class="form-group">
          <label>Kota Asal</label>
          <input type="text" name="asal_kota" placeholder="Contoh: Jakarta" required>
        </div>
        <div class="form-group">
          <label>Kota Tujuan</label>
          <input type="text" name="tujuan_kota" placeholder="Contoh: Surabaya" required>
        </div>
        <div class="form-group">
          <label>Maskapai</label>
          <select name="id_maskapai" required>
            <option value="">-- Pilih Maskapai --</option>
            <?php foreach ($airlines as $airline): ?>
              <option value="<?php echo $airline['id_maskapai']; ?>">
                <?php echo htmlspecialchars($airline['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?> 
                (Rp <?php echo number_format($airline['harga_satukursi'], 0, ',', '.'); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" name="add_ticket" class="btn-primary">💾 Simpan</button>
      </form>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>✏️ Edit Tiket</h2>
        <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
      </div>
      <form method="POST">
        <?php echo csrfTokenInput(); ?>
        <input type="hidden" name="id_tiket" id="edit_id">
        <div class="form-group">
          <label>Kota Asal</label>
          <input type="text" name="asal_kota" id="edit_asal" required>
        </div>
        <div class="form-group">
          <label>Kota Tujuan</label>
          <input type="text" name="tujuan_kota" id="edit_tujuan" required>
        </div>
        <div class="form-group">
          <label>Maskapai</label>
          <select name="id_maskapai" id="edit_maskapai" required>
            <option value="">-- Pilih Maskapai --</option>
            <?php foreach ($airlines as $airline): ?>
              <option value="<?php echo $airline['id_maskapai']; ?>">
                <?php echo htmlspecialchars($airline['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?> 
                (Rp <?php echo number_format($airline['harga_satukursi'], 0, ',', '.'); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" name="update_ticket" class="btn-primary">💾 Update</button>
      </form>
    </div>
  </div>

  <?php renderFooter(); ?>

  <script>
    function editTicket(ticket) {
      document.getElementById('edit_id').value = ticket.id_tiket;
      document.getElementById('edit_asal').value = ticket.asal_kota;
      document.getElementById('edit_tujuan').value = ticket.tujuan_kota;
      document.getElementById('edit_maskapai').value = ticket.id_maskapai;
      document.getElementById('editModal').style.display = 'block';
    }

    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }

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