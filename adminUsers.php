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

// Handle Delete User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_user = intval($_POST['id_user']);
        
        // Tidak boleh hapus diri sendiri
        if ($id_user == $_SESSION['id_user']) {
            $message = "Tidak dapat menghapus akun sendiri!";
            $messageType = "error";
        } else {
            $deleteQuery = "DELETE FROM user WHERE id_user = ?";
            $stmt = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($stmt, "i", $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "User berhasil dihapus!";
                $messageType = "success";
            } else {
                $message = "Gagal menghapus user!";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
        regenerateCsrfToken();
    }
}

// Handle Update Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid security token";
        $messageType = "error";
    } else {
        $id_user = intval($_POST['id_user']);
        $new_role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        
        // Tidak boleh ubah role diri sendiri
        if ($id_user == $_SESSION['id_user']) {
            $message = "Tidak dapat mengubah role sendiri!";
            $messageType = "error";
        } else {
            $updateQuery = "UPDATE user SET role = ? WHERE id_user = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $new_role, $id_user);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Role berhasil diubah!";
                $messageType = "success";
            } else {
                $message = "Gagal mengubah role!";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
        regenerateCsrfToken();
    }
}

// Get all users
$query = "SELECT id_user, username, name, email_user, role, photo FROM user ORDER BY id_user DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola User - AIRtix.id Admin</title>
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
          <h1 class="page-title">👥 Kelola User</h1>
          <p class="page-subtitle">Manage semua pengguna sistem AIRtix.id</p>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <div class="data-table">
        <div class="table-header">
          <h2>📊 Daftar User (Total: <?php echo count($users); ?>)</h2>
        </div>
        
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): 
                $photoUrl = !empty($user['photo']) && file_exists($user['photo']) 
                  ? htmlspecialchars($user['photo'], ENT_QUOTES, 'UTF-8')
                  : "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&size=100&background=4BABFF&color=fff";
              ?>
                <tr>
                  <td><?php echo $user['id_user']; ?></td>
                  <td>
                    <img src="<?php echo $photoUrl; ?>" alt="Profile" class="table-avatar">
                  </td>
                  <td><strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                  <td><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($user['email_user'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <?php if ($user['role'] === 'admin'): ?>
                      <span class="badge badge-admin">👑 ADMIN</span>
                    <?php else: ?>
                      <span class="badge badge-user">👤 USER</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <?php if ($user['id_user'] != $_SESSION['id_user']): ?>
                        <!-- Change Role Button -->
                        <form method="POST" style="display: inline;">
                          <?php echo csrfTokenInput(); ?>
                          <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                          <input type="hidden" name="role" value="<?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>">
                          <button type="submit" name="update_role" class="btn-action btn-edit" 
                                  onclick="return confirm('Ubah role user ini?')">
                            🔄 Ubah Role
                          </button>
                        </form>
                        
                        <!-- Delete Button -->
                        <form method="POST" style="display: inline;">
                          <?php echo csrfTokenInput(); ?>
                          <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                          <button type="submit" name="delete_user" class="btn-action btn-delete" 
                                  onclick="return confirm('Yakin ingin menghapus user ini? Semua data terkait akan hilang!')">
                            🗑️ Hapus
                          </button>
                        </form>
                      <?php else: ?>
                        <span class="text-muted">⚠️ Akun Anda</span>
                      <?php endif; ?>
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