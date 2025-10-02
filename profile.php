<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$username = $_SESSION['username'] ?? 'User';
$name = $_SESSION['name'] ?? 'User Name';

function safe_name($str) {
    return preg_replace('/[^A-Za-z0-9]/', '_', $str);
}

$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$message = "";

if (isset($_POST['upload'])) {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['photo']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $newFile = $uploadDir . safe_name($email) . "." . $ext;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $newFile)) {
                foreach (glob($uploadDir . safe_name($email) . ".*") as $oldFile) {
                    if ($oldFile !== $newFile) {
                        unlink($oldFile);
                    }
                }
                $message = "success|Foto profil berhasil diunggah!";
            } else {
                $message = "error|Gagal mengunggah foto!";
            }
        } else {
            $message = "error|Format file tidak didukung!";
        }
        header("Location: profile.php");
        exit();
    }
}

if (isset($_POST['delete'])) {
    foreach (glob($uploadDir . safe_name($email) . ".*") as $file) {
        unlink($file);
    }
    $message = "success|Foto profil berhasil dihapus!";
    header("Location: profile.php");
    exit();
}

$photoFiles = glob($uploadDir . safe_name($email) . ".*");
if (!empty($photoFiles)) {
    $photo = "uploads/" . basename($photoFiles[0]) . "?t=" . time();
} else {
    $photo = "default.png";
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil - AIRtix.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Space Grotesk', sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      color: #333;
    }

    /* Header - Style index.php */
    header {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      padding: 20px 50px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 20px rgba(75, 171, 255, 0.3);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header h1 {
      font-size: 32px;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    nav {
      display: flex;
      gap: 15px;
      align-items: center;
      flex-wrap: wrap;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 10px 18px;
      border-radius: 12px;
    }

    nav a:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }

    .logout-btn {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      padding: 10px 20px;
      border-radius: 25px;
      box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
    }

    .logout-btn:hover {
      box-shadow: 0 6px 20px rgba(231, 76, 60, 0.5);
    }

    /* Main Content */
    main {
      flex: 1;
      padding: 80px 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .profile-container {
      background: white;
      padding: 60px 50px;
      border-radius: 25px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 900px;
      animation: fadeInUp 0.8s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .profile-header {
      text-align: center;
      margin-bottom: 50px;
    }

    .profile-header h2 {
      font-size: 48px;
      font-weight: 700;
      color: rgb(75, 171, 255);
      margin-bottom: 12px;
    }

    .profile-header p {
      color: #666;
      font-size: 16px;
    }

    /* Photo Section */
    .photo-section {
      text-align: center;
      margin-bottom: 50px;
      padding: 50px 40px;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      border-radius: 25px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .photo-wrapper {
      position: relative;
      display: inline-block;
      margin-bottom: 35px;
    }

    .photo-preview {
      width: 200px;
      height: 200px;
      border-radius: 50%;
      object-fit: cover;
      border: 6px solid white;
      box-shadow: 0 10px 40px rgba(75, 171, 255, 0.3);
      transition: all 0.4s ease;
    }

    .photo-preview:hover {
      transform: scale(1.05);
      box-shadow: 0 15px 50px rgba(75, 171, 255, 0.5);
    }

    .photo-buttons {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 16px 40px;
      border: none;
      border-radius: 30px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-upload {
      background: linear-gradient(135deg, rgb(75, 171, 255) 0%, #1976D2 100%);
      color: white;
      box-shadow: 0 6px 25px rgba(75, 171, 255, 0.4);
    }

    .btn-upload:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 35px rgba(75, 171, 255, 0.5);
    }

    .btn-delete {
      background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
      color: white;
      box-shadow: 0 6px 25px rgba(231, 76, 60, 0.4);
    }

    .btn-delete:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 35px rgba(231, 76, 60, 0.5);
    }

    .file-input-wrapper {
      position: relative;
      display: inline-block;
    }

    input[type="file"] {
      display: none;
    }

    /* Profile Info Cards */
    .profile-info {
      display: grid;
      gap: 25px;
    }

    .info-item {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      padding: 28px 35px;
      border-radius: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.4s ease;
      border: 2px solid transparent;
    }

    .info-item:hover {
      transform: translateX(8px);
      border-color: rgb(75, 171, 255);
      box-shadow: 0 8px 30px rgba(75, 171, 255, 0.2);
    }

    .info-label {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      color: #333;
      font-size: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .info-icon {
      font-size: 24px;
    }

    .info-value {
      color: #666;
      font-size: 17px;
      font-weight: 600;
    }

    /* Message Alert */
    .message {
      text-align: center;
      padding: 18px 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      font-weight: 600;
      animation: slideDown 0.6s ease;
      font-size: 15px;
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
      background: #d4edda;
      color: #155724;
      border: 2px solid #c3e6cb;
    }

    .message.error {
      background: #f8d7da;
      color: #721c24;
      border: 2px solid #f5c6cb;
    }

    /* Footer */
    footer {
      background: #1a1a1a;
      color: #ccc;
      text-align: center;
      padding: 30px;
      margin-top: auto;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        gap: 15px;
        padding: 20px;
      }

      nav {
        justify-content: center;
        gap: 8px;
      }

      main {
        padding: 40px 20px;
      }

      .profile-container {
        padding: 40px 25px;
      }

      .profile-header h2 {
        font-size: 36px;
      }

      .photo-section {
        padding: 35px 25px;
      }

      .photo-preview {
        width: 160px;
        height: 160px;
      }

      .photo-buttons {
        flex-direction: column;
      }

      .btn {
        width: 100%;
      }

      .info-item {
        flex-direction: column;
        gap: 12px;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>✈️ AIRtix.id</h1>
    <nav>
      <a href="LandingPage.php">🏠 Beranda</a>
      <a href="search.php">🎫 Pesan Tiket</a>
      <a href="history.php">📋 Riwayat</a>
      <a href="checkin.php">✅ Check-in</a>
      <a class="logout-btn" href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <div class="profile-container">
      <div class="profile-header">
        <h2>Profil Pengguna</h2>
        <p>Kelola informasi akun Anda dengan mudah</p>
      </div>

      <?php if (!empty($message)): 
        list($type, $text) = explode('|', $message);
      ?>
        <div class="message <?php echo $type; ?>">
          <?php echo htmlspecialchars($text); ?>
        </div>
      <?php endif; ?>

      <div class="photo-section">
        <div class="photo-wrapper">
          <img src="<?php echo htmlspecialchars($photo); ?>" alt="Foto Profil" class="photo-preview" id="photoPreview">
        </div>
        
        <div class="photo-buttons">
          <form method="POST" enctype="multipart/form-data" style="display: inline-block;">
            <div class="file-input-wrapper">
              <input type="file" name="photo" id="photoInput" accept="image/*" onchange="previewImage(this); this.form.submit();">
              <label for="photoInput" class="btn btn-upload">
                <?php echo !empty($photoFiles) ? '✏️ Edit Foto' : '📤 Upload Foto'; ?>
              </label>
            </div>
          </form>

          <?php if (!empty($photoFiles)): ?>
          <form method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus foto profil?');">
            <button type="submit" name="delete" class="btn btn-delete">
              🗑️ Hapus Foto
            </button>
          </form>
          <?php endif; ?>
        </div>
      </div>

      <div class="profile-info">
        <div class="info-item">
          <span class="info-label">
            <span class="info-icon">👤</span>
            Username
          </span>
          <span class="info-value"><?php echo htmlspecialchars($username); ?></span>
        </div>

        <div class="info-item">
          <span class="info-label">
            <span class="info-icon">✏️</span>
            Nama Lengkap
          </span>
          <span class="info-value"><?php echo htmlspecialchars($name); ?></span>
        </div>

        <div class="info-item">
          <span class="info-label">
            <span class="info-icon">📧</span>
            Email
          </span>
          <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
        </div>

        <div class="info-item">
          <span class="info-label">
            <span class="info-icon">🔒</span>
            Password
          </span>
          <span class="info-value">••••••••</span>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>

  <script>
    // Preview image before upload
    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>
</html>