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

// Handle Add Airline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_airline'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $nama_maskapai = sanitizeInput($_POST['nama_maskapai']);
        $harga_satukursi = intval($_POST['harga_satukursi']);
        
        if (empty($nama_maskapai) || $harga_satukursi <= 0) {
            $message = "Data tidak valid!";
            $messageType = "error";
        } else {
            $insertQuery = "INSERT INTO maskapai (nama_maskapai, harga_satukursi) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($stmt, "si", $nama_maskapai, $harga_satukursi);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Maskapai berhasil ditambahkan!";
                $messageType = "success";
            } else {
                $message = "Gagal menambahkan maskapai!";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
        regenerateCsrfToken();
    }
}

// Handle Update Airline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_airline'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_maskapai = intval($_POST['id_maskapai']);
        $nama_maskapai = sanitizeInput($_POST['nama_maskapai']);
        $harga_satukursi = intval($_POST['harga_satukursi']);
        
        if (empty($nama_maskapai) || $harga_satukursi <= 0) {
            $message = "Data tidak valid!";
            $messageType = "error";
        } else {
            $updateQuery = "UPDATE maskapai SET nama_maskapai = ?, harga_satukursi = ? WHERE id_maskapai = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "sii", $nama_maskapai, $harga_satukursi, $id_maskapai);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Maskapai berhasil diupdate!";
                $messageType = "success";
            } else {
                $message = "Gagal mengupdate maskapai!";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
        regenerateCsrfToken();
    }
}

// Handle Delete Airline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_airline'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_maskapai = intval($_POST['id_maskapai']);
        
        $deleteQuery = "DELETE FROM maskapai WHERE id_maskapai = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $id_maskapai);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Maskapai berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Gagal menghapus maskapai! Mungkin masih ada tiket yang terkait.";
            $messageType = "error";
        }
        mysqli_stmt_close($stmt);
        regenerateCsrfToken();
    }
}

// Get all airlines
$query = "SELECT * FROM maskapai ORDER BY id_maskapai DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$airlines = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Maskapai - AIRtix.id Admin</title>
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
          <h1 class="page-title">✈️ Kelola Maskapai</h1>
          <p class="page-subtitle">Manage maskapai penerbangan</p>
        </div>
        <button class="btn-primary" onclick="document.getElementById('addModal').style.display='block'">
          ➕ Tambah Maskapai
        </button>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <div class="data-table">
        <div class="table-header">
          <h2>📊 Daftar Maskapai (Total: <?php echo count($airlines); ?>)</h2>
        </div>
        
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama Maskapai</th>
                <th>Harga per Kursi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($airlines as $airline): ?>
                <tr>
                  <td><?php echo $airline['id_maskapai']; ?></td>
                  <td><strong><?php echo htmlspecialchars($airline['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                  <td>Rp <?php echo number_format($airline['harga_satukursi'], 0, ',', '.'); ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn-action btn-edit" onclick="editAirline(<?php echo $airline['id_maskapai']; ?>, '<?php echo htmlspecialchars($airline['nama_maskapai'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo $airline['harga_satukursi']; ?>)">
                        ✏️ Edit
                      </button>
                      
                      <form method="POST" style="display: inline;">
                        <?php echo csrfTokenInput(); ?>
                        <input type="hidden" name="id_maskapai" value="<?php echo $airline['id_maskapai']; ?>">
                        <button type="submit" name="delete_airline" class="btn-action btn-delete" 
                                onclick="return confirm('Yakin ingin menghapus maskapai ini?')">
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
        <h2>➕ Tambah Maskapai Baru</h2>
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
      </div>
      <form method="POST">
        <?php echo csrfTokenInput(); ?>
        <div class="form-group">
          <label>Nama Maskapai</label>
          <input type="text" name="nama_maskapai" required>
        </div>
        <div class="form-group">
          <label>Harga per Kursi</label>
          <input type="number" name="harga_satukursi" min="1" required>
        </div>
        <button type="submit" name="add_airline" class="btn-primary">💾 Simpan</button>
      </form>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>✏️ Edit Maskapai</h2>
        <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
      </div>
      <form method="POST">
        <?php echo csrfTokenInput(); ?>
        <input type="hidden" name="id_maskapai" id="edit_id">
        <div class="form-group">
          <label>Nama Maskapai</label>
          <input type="text" name="nama_maskapai" id="edit_nama" required>
        </div>
        <div class="form-group">
          <label>Harga per Kursi</label>
          <input type="number" name="harga_satukursi" id="edit_harga" min="1" required>
        </div>
        <button type="submit" name="update_airline" class="btn-primary">💾 Update</button>
      </form>
    </div>
  </div>

  <?php renderFooter(); ?>

  <script>
    function editAirline(id, nama, harga) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_nama').value = nama;
      document.getElementById('edit_harga').value = harga;
      document.getElementById('editModal').style.display = 'block';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }

    // Auto hide alert
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