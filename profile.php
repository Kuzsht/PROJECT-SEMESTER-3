<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email_user'];

// prepared statement untuk query user
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
                    
                    // prepared statement untuk update
                    $updateQuery = "UPDATE user SET photo = ? WHERE email_user = ?";
                    $updateStmt = mysqli_prepare($conn, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "ss", $photoPathDB, $email);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);

                    $message = "Foto profil berhasil diunggah!";
                    $messageType = "success";
                    $photo = $photoPathDB;
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

if (isset($_POST['delete'])) {
    if (!empty($photo) && file_exists($photo)) {
        unlink($photo);
    }
    
    // prepared statement untuk update
    $updateQuery = "UPDATE user SET photo = NULL WHERE email_user = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "s", $email);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
    
    $photo = '';
    $message = "Foto profil berhasil dihapus.";
    $messageType = "success";
    $hasPhoto = false;
}

// URL foto profil (real atau default)
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
    <div class="profile-header">
      <h2 class="section-title" data-text="PROFIL">Profil Pengguna</h2>
      <p class="profile-subtitle">Kelola informasi akun dan foto profil Anda dengan mudah</p>
    </div>

    <?php if (!empty($message)): ?>
      <div class="message <?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
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
        <div class="info-card">
          <div class="info-icon">👤</div>
          <div class="info-content">
            <div class="info-label">Username</div>
            <div class="info-value"><?php echo htmlspecialchars($username); ?></div>
          </div>
        </div>

        <div class="info-card">
          <div class="info-icon">✏️</div>
          <div class="info-content">
            <div class="info-label">Nama Lengkap</div>
            <div class="info-value"><?php echo htmlspecialchars($name); ?></div>
          </div>
        </div>

        <div class="info-card">
          <div class="info-icon">📧</div>
          <div class="info-content">
            <div class="info-label">Email</div>
            <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
          </div>
        </div>

        <div class="info-card">
          <div class="info-icon">🔒</div>
          <div class="info-content">
            <div class="info-label">Password</div>
            <div class="info-value">••••••••</div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
</body>
</html>