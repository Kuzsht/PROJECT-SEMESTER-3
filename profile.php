<?php
session_start();
include 'connector.php';
include 'headerFooter.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email_user'];

// Prepared statement untuk query user
$sql = "SELECT username, name, email_user, photo FROM user WHERE email_user = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $username = $user['username'];
    $name = $user['name'];
    $email = $user['email_user'];
    $photo = $user['photo'] ?? '';
} else {
    $username = $_SESSION['username'] ?? 'User';
    $name = $_SESSION['name'] ?? 'User Name';
    $photo = '';
}

mysqli_stmt_close($stmt);

function safe_name($str) {
    return preg_replace('/[^A-Za-z0-9]/', '_', $str);
}

function getProfilePhoto($photo) {
    if (!empty($photo) && file_exists($photo)) {
        return $photo;
    }
    return "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['name'] ?? 'User') . "&size=500&background=4BABFF&color=fff&bold=true&font-size=0.4";
}

$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$message = "";
$messageType = "";
$hasPhoto = !empty($photo) && file_exists($photo);

// Handle Update Profile Data
if (isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $new_name = trim($_POST['name']);
    $new_password = trim($_POST['password']);
    
    // Validasi
    $errors = [];
    
    if (empty($new_username)) {
        $errors[] = "Username tidak boleh kosong";
    } elseif (strlen($new_username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    }
    
    if (empty($new_name)) {
        $errors[] = "Nama lengkap tidak boleh kosong";
    }
    
    // Cek apakah username sudah digunakan user lain
    $checkQuery = "SELECT id_user FROM user WHERE username = ? AND email_user != ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "ss", $new_username, $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    
    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $errors[] = "Username sudah digunakan oleh user lain";
    }
    mysqli_stmt_close($checkStmt);
    
    if (!empty($errors)) {
        $message = implode("<br>", $errors);
        $messageType = "error";
    } else {
        // Update data
        if (!empty($new_password)) {
            // Update dengan password baru
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE user SET username = ?, name = ?, password = ? WHERE email_user = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, "ssss", $new_username, $new_name, $hashedPassword, $email);
        } else {
            // Update tanpa password
            $updateQuery = "UPDATE user SET username = ?, name = ? WHERE email_user = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, "sss", $new_username, $new_name, $email);
        }
        
        if (mysqli_stmt_execute($updateStmt)) {
            // Update session
            $_SESSION['username'] = $new_username;
            $_SESSION['name'] = $new_name;
            
            $username = $new_username;
            $name = $new_name;
            
            $message = "Profil berhasil diperbarui!";
            $messageType = "success";
        } else {
            $message = "Gagal memperbarui profil";
            $messageType = "error";
        }
        mysqli_stmt_close($updateStmt);
    }
}

// Handle Upload/Edit Photo
if (isset($_POST['upload']) || isset($_POST['edit'])) {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['photo']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $maxSize = 5 * 1024 * 1024;
            if ($_FILES['photo']['size'] > $maxSize) {
                $message = "Ukuran file terlalu besar. Maksimal 5MB.";
                $messageType = "error";
            } else {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $newFileName = safe_name($email) . "_" . time() . "." . $ext;
                $newFilePath = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $newFilePath)) {
                    if (!empty($photo) && file_exists($photo)) {
                        unlink($photo);
                    }

                    $photoPathDB = "uploads/" . $newFileName;
                    
                    $updateQuery = "UPDATE user SET photo = ? WHERE email_user = ?";
                    $updateStmt = mysqli_prepare($conn, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "ss", $photoPathDB, $email);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);

                    $message = "Foto profil berhasil diunggah!";
                    $messageType = "success";
                    $photo = $photoPathDB;
                    $_SESSION['photo'] = $photoPathDB;
                    $hasPhoto = true;
                } else {
                    $message = "Gagal memindahkan file upload.";
                    $messageType = "error";
                }
            }
        } else {
            $message = "Format file tidak didukung. Hanya JPG, PNG, dan GIF yang diizinkan.";
            $messageType = "error";
        }
    } else {
        $message = "Silakan pilih file terlebih dahulu.";
        $messageType = "error";
    }
}

// Handle Delete Photo
if (isset($_POST['delete'])) {
    if (!empty($photo) && file_exists($photo)) {
        unlink($photo);
    }
    
    $updateQuery = "UPDATE user SET photo = NULL WHERE email_user = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "s", $email);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
    
    $photo = '';
    $_SESSION['photo'] = '';
    $message = "Foto profil berhasil dihapus.";
    $messageType = "success";
    $hasPhoto = false;
}

$profilePhotoURL = getProfilePhoto($photo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil - AIRtix.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/profile.css">
  <link rel="stylesheet" href="styles/headerFooter.css">
</head>
<body>
  <?php renderBackgroundDecorations(); ?>
  <?php renderHeader($username, $conn); ?>

  <main>
    <div class="back-wrapper">
      <a href="landingPage.php" class="back-btn">← Kembali ke Beranda</a>
    </div>
    
    <div class="profile-header">
      <h2 class="section-title" data-text="PROFIL">Profil Pengguna</h2>
      <p class="profile-subtitle">Kelola informasi akun dan foto profil Anda dengan mudah</p>
    </div>

    <?php if (!empty($message)): ?>
      <div class="message <?php echo $messageType; ?>" id="message">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <div class="profile-container">
      <div class="photo-card">
        <div class="photo-wrapper">
          <img src="<?php echo htmlspecialchars($profilePhotoURL); ?>" alt="Foto Profil" class="photo-preview">
          <?php if (!$hasPhoto): ?>
            <span class="default-photo-badge">Default</span>
          <?php endif; ?>
        </div>
        
        <div class="photo-buttons">
          <?php if (!$hasPhoto): ?>
            <form method="POST" enctype="multipart/form-data">
              <div class="file-input-wrapper">
                <input type="file" name="photo" id="photoUpload" accept="image/*" onchange="this.form.submit();">
                <label for="photoUpload" class="btn btn-upload">
                  📤 Upload Foto
                </label>
                <input type="hidden" name="upload" value="1">
              </div>
            </form>
          <?php else: ?>
            <form method="POST" enctype="multipart/form-data">
              <div class="file-input-wrapper">
                <input type="file" name="photo" id="photoEdit" accept="image/*" onchange="this.form.submit();">
                <label for="photoEdit" class="btn btn-edit">
                  ✏️ Edit Foto
                </label>
                <input type="hidden" name="edit" value="1">
              </div>
            </form>

            <form method="POST" onsubmit="return confirm('Yakin ingin menghapus foto profil?');">
              <button type="submit" name="delete" class="btn btn-delete">
                🗑️ Hapus Foto
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <div class="info-section">
        <form method="POST" id="profileForm">
          <!-- Username Card -->
          <div class="info-card editable">
            <div class="info-icon">👤</div>
            <div class="info-content">
              <div class="info-label">Username</div>
              <div class="info-value-wrapper">
                <span class="info-value display-mode"><?php echo htmlspecialchars($username); ?></span>
                <input type="text" name="username" class="info-input edit-mode" value="<?php echo htmlspecialchars($username); ?>" required minlength="3" style="display: none;">
              </div>
            </div>
            <button type="button" class="edit-toggle-btn" onclick="toggleEdit(this)">✏️</button>
          </div>

          <!-- Name Card -->
          <div class="info-card editable">
            <div class="info-icon">✏️</div>
            <div class="info-content">
              <div class="info-label">Nama Lengkap</div>
              <div class="info-value-wrapper">
                <span class="info-value display-mode"><?php echo htmlspecialchars($name); ?></span>
                <input type="text" name="name" class="info-input edit-mode" value="<?php echo htmlspecialchars($name); ?>" required style="display: none;">
              </div>
            </div>
            <button type="button" class="edit-toggle-btn" onclick="toggleEdit(this)">✏️</button>
          </div>

          <!-- Email Card (Read Only) -->
          <div class="info-card">
            <div class="info-icon">📧</div>
            <div class="info-content">
              <div class="info-label">Email</div>
              <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
            </div>
            <span class="readonly-badge">🔒</span>
          </div>

          <!-- Password Card -->
          <div class="info-card editable">
            <div class="info-icon">🔒</div>
            <div class="info-content">
              <div class="info-label">Password</div>
              <div class="info-value-wrapper">
                <span class="info-value display-mode">••••••••</span>
                <input type="password" name="password" class="info-input edit-mode" placeholder="Kosongkan jika tidak ingin mengubah" minlength="8" style="display: none;">
              </div>
            </div>
            <button type="button" class="edit-toggle-btn" onclick="toggleEdit(this)">✏️</button>
          </div>

          <!-- Save Button (Hidden by default) -->
          <button type="submit" name="update_profile" class="btn-save" id="saveBtn" style="display: none;">
            💾 Simpan Perubahan
          </button>
        </form>
      </div>
    </div>
  </main>

  <?php renderFooter(); ?>
  
  <script>
    // Auto hide message after 5 seconds
    const message = document.getElementById('message');
    if (message) {
      setTimeout(() => {
        message.style.transition = 'all 0.5s ease';
        message.style.opacity = '0';
        message.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          message.remove();
        }, 500);
      }, 5000);
    }

    // Toggle Edit Mode
    function toggleEdit(btn) {
      const card = btn.closest('.info-card');
      const displayMode = card.querySelector('.display-mode');
      const editMode = card.querySelector('.edit-mode');
      const saveBtn = document.getElementById('saveBtn');
      
      // Toggle display
      if (displayMode.style.display !== 'none') {
        displayMode.style.display = 'none';
        editMode.style.display = 'block';
        editMode.focus();
        btn.textContent = '❌';
        btn.classList.add('cancel-mode');
        saveBtn.style.display = 'block';
      } else {
        displayMode.style.display = 'block';
        editMode.style.display = 'none';
        btn.textContent = '✏️';
        btn.classList.remove('cancel-mode');
        
        // Check if any field is still in edit mode
        const anyEditing = document.querySelectorAll('.edit-mode[style*="display: block"]').length > 0;
        if (!anyEditing) {
          saveBtn.style.display = 'none';
        }
      }
    }

    // Auto show save button if any input is changed
    document.querySelectorAll('.edit-mode').forEach(input => {
      input.addEventListener('input', () => {
        document.getElementById('saveBtn').style.display = 'block';
      });
    });
  </script>
</body>
</html>