<?php
session_start();
include 'connector.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email_user'];

$sql = "SELECT username, name, email_user, photo FROM user WHERE email_user = '$email'";
$result = mysqli_query($conn, $sql);

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

function safe_name($str) {
    return preg_replace('/[^A-Za-z0-9]/', '_', $str);
}

// Fungsi untuk mendapatkan foto profil (default jika kosong)
function getProfilePhoto($photo) {
    if (!empty($photo) && file_exists($photo)) {
        return $photo;
    }
    // Gunakan avatar default dari UI Avatars
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
            // Validasi ukuran file (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($_FILES['photo']['size'] > $maxSize) {
                $message = "Ukuran file terlalu besar. Maksimal 5MB.";
                $messageType = "error";
            } else {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $newFileName = safe_name($email) . "_" . time() . "." . $ext;
                $newFilePath = $uploadDir . $newFileName;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $newFilePath)) {
                    // Hapus foto lama jika ada
                    if (!empty($photo) && file_exists($photo)) {
                        unlink($photo);
                    }

                    $photoPathDB = "uploads/" . $newFileName;
                    $updateQuery = "UPDATE user SET photo = '$photoPathDB' WHERE email_user = '$email'";
                    mysqli_query($conn, $updateQuery);

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
    $updateQuery = "UPDATE user SET photo = NULL WHERE email_user = '$email'";
    mysqli_query($conn, $updateQuery);
    
    $photo = '';
    $message = "Foto profil berhasil dihapus.";
    $messageType = "success";
    $hasPhoto = false;
}

// Dapatkan URL foto profil (real atau default)
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
    }

    /* Subtle Background */
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

    nav a, .username-btn {
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

    nav a:hover, .username-btn:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 255, 255, 1);
    }

    .username-btn {
      font-weight: 700;
      padding: 12px 26px;
      background: rgba(255,255,255,0.25);
      border: 2px solid rgba(255,255,255,0.3);
    }

    .logout-btn {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      padding: 12px 26px;
      border-radius: 25px;
      border: 2px solid rgba(255,255,255,0.2);
      box-shadow: 0 4px 20px rgba(231, 76, 60, 0.4);
    }

    .logout-btn:hover {
      box-shadow: 0 8px 30px rgba(255, 0, 0, 1);
      background: white;
      color: rgba(255, 0, 0, 0.5);
    }

    /* Main Content */
    main {
      padding: 80px 40px 140px;
      max-width: 1400px;
      margin: auto;
    }

    .profile-header {
      text-align: center;
      margin-bottom: 80px;
    }

    .section-title {
      font-size: 64px;
      font-weight: 800;
      color: rgb(75, 171, 255);
      position: relative;
      padding-bottom: 40px;
      letter-spacing: -2px;
      margin-bottom: 20px;
    }

    .section-title::before {
      content: attr(data-text);
      position: absolute;
      left: 50%;
      top: -20px;
      transform: translateX(-50%);
      font-size: 90px;
      color: transparent;
      -webkit-text-stroke: 2px rgba(75, 171, 255, 0.06);
      z-index: -1;
      font-weight: 900;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 180px;
      height: 6px;
      background: linear-gradient(90deg, transparent, rgb(75, 171, 255), #1976D2, transparent);
      border-radius: 3px;
      box-shadow: 0 4px 20px rgba(75, 171, 255, 0.5);
    }

    .profile-subtitle {
      font-size: 20px;
      color: #666;
      margin-top: 20px;
    }

    .profile-container {
      display: grid;
      grid-template-columns: 400px 1fr;
      gap: 50px;
      max-width: 1200px;
      margin: auto;
    }

    /* Photo Section Card */
    .photo-card {
      background: white;
      border-radius: 35px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      padding: 50px 40px;
      text-align: center;
      transition: all 0.5s ease;
      border: 3px solid rgba(75, 171, 255, 0.08);
      height: fit-content;
      position: relative;
    }

    .photo-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      border-radius: 35px 35px 0 0;
    }

    .photo-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 35px 80px rgba(75, 171, 255, 0.25);
      border-color: rgb(75, 171, 255);
    }

    .photo-wrapper {
      position: relative;
      display: inline-block;
      margin-bottom: 35px;
    }

    .photo-preview {
      width: 220px;
      height: 220px;
      border-radius: 50%;
      object-fit: cover;
      border: 6px solid rgba(75, 171, 255, 0.1);
      box-shadow: 0 15px 50px rgba(75, 171, 255, 0.3);
      transition: all 0.4s ease;
    }

    .photo-preview:hover {
      transform: scale(1.05);
      box-shadow: 0 20px 60px rgba(75, 171, 255, 0.5);
      border-color: rgba(75, 171, 255, 0.3);
    }

    .default-photo-badge {
      position: absolute;
      bottom: 10px;
      right: 10px;
      background: linear-gradient(135deg, #FFA726, #FB8C00);
      color: white;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 15px rgba(255, 167, 38, 0.4);
    }

    .photo-buttons {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 30px;
    }

    .btn {
      padding: 16px 35px;
      border: none;
      border-radius: 30px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      width: 100%;
    }

    .btn-upload {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      box-shadow: 0 8px 30px rgba(75, 171, 255, 0.4);
    }

    .btn-upload:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(75, 171, 255, 0.5);
    }

    .btn-edit {
      background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
      color: white;
      box-shadow: 0 8px 30px rgba(243, 156, 18, 0.4);
    }

    .btn-edit:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(243, 156, 18, 0.5);
    }

    .btn-delete {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      color: white;
      box-shadow: 0 8px 30px rgba(231, 76, 60, 0.4);
    }

    .btn-delete:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(231, 76, 60, 0.5);
    }

    .file-input-wrapper {
      position: relative;
      display: block;
    }

    input[type="file"] {
      display: none;
    }

    /* Info Section */
    .info-section {
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .info-card {
      background: white;
      border-radius: 25px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.08);
      padding: 35px 40px;
      display: flex;
      align-items: center;
      gap: 25px;
      transition: all 0.4s ease;
      border: 3px solid rgba(75, 171, 255, 0.08);
    }

    .info-card:hover {
      transform: translateX(10px);
      border-color: rgb(75, 171, 255);
      box-shadow: 0 20px 60px rgba(75, 171, 255, 0.2);
    }

    .info-icon {
      font-size: 48px;
      min-width: 60px;
      text-align: center;
      filter: drop-shadow(0 5px 15px rgba(75, 171, 255, 0.3));
    }

    .info-content {
      flex: 1;
    }

    .info-label {
      font-weight: 700;
      color: rgb(75, 171, 255);
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 8px;
    }

    .info-value {
      color: #333;
      font-size: 20px;
      font-weight: 600;
    }

    .message {
      text-align: center;
      padding: 20px 35px;
      border-radius: 20px;
      margin-bottom: 40px;
      font-weight: 600;
      animation: slideDown 0.6s ease;
      font-size: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .message.success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
      border: 3px solid #b1dfbb;
    }

    .message.error {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
      border: 3px solid #f1aeb5;
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
        padding: 40px 20px 70px;
      }

      .section-title {
        font-size: 38px;
      }

      .profile-container {
        grid-template-columns: 1fr;
        gap: 30px;
      }

      .photo-card {
        padding: 40px 30px;
      }

      .photo-preview {
        width: 180px;
        height: 180px;
      }

      .info-card {
        padding: 25px 20px;
        flex-direction: column;
        text-align: center;
      }

      .info-icon {
        font-size: 40px;
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
      <!-- Photo Section -->
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

      <!-- Info Section -->
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