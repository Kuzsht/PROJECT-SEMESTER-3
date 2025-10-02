<?php
session_start();

// harus login
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// fungsi untuk sanitize nama file dari email
function safe_filename_from_email($email) {
    // ganti karakter non-alphanumeric menjadi underscore
    return preg_replace('/[^A-Za-z0-9]/', '_', $email);
}

$uploadsDir = __DIR__ . "/uploads";
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

$san = safe_filename_from_email($email);
$profilePath = $uploadsDir . "/" . $san . ".png"; // pakai .png sebagai standar
$relativeProfile = "uploads/" . $san . ".png";
$default = "default.png";

// jika file user belum ada -> gunakan default
$displayPic = file_exists($profilePath) ? $relativeProfile : $default;
$message = "";

// upload / edit foto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (!isset($_FILES['photo'])) {
        $message = "Tidak ada file yang diupload.";
    } else {
        $f = $_FILES['photo'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $message = "Upload error (Kode: {$f['error']}).";
        } else {
            // validasi sederhana: mime image dan ukuran < 2MB (adjust jika perlu)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            if (!in_array($mime, $allowed)) {
                $message = "Tipe file tidak diperbolehkan. Gunakan PNG/JPG/GIF.";
            } elseif ($f['size'] > 2 * 1024 * 1024) {
                $message = "File terlalu besar (maks 2MB).";
            } else {
                // simpan sebagai PNG (lebih aman untuk mengganti extension sesuai mime)
                $target = $profilePath;
                // jika jpeg/gif, kita bisa move file langsung dan biarkan extension .png
                if (!move_uploaded_file($f['tmp_name'], $target)) {
                    $message = "Gagal menyimpan file upload.";
                } else {
                    $message = "Foto profil berhasil diupload.";
                    header("Location: profile.php");
                    exit();
                }
            }
        }
    }
}

// hapus foto profil
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (file_exists($profilePath)) {
        unlink($profilePath);
        $message = "Foto profil dihapus.";
    } else {
        $message = "Foto profil tidak ditemukan.";
    }
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil - AIRtix.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet">
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
      background: #f5f9ff;
      color: #333;
    }

    header {
      background: rgb(75, 171, 255);
      padding: 15px 40px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      font-size: 1.5rem;
    }

    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }

    nav a:hover {
      text-decoration: underline;
      color: rgb(0, 119, 182);
    }

    main {
      flex: 1;
      padding: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .profile-card {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 500px;
    }

    .profile-card h2 {
      margin-bottom: 20px;
      color: rgb(75, 171, 255);
      text-align: center;
    }

    .profile-item {
      margin-bottom: 15px;
    }

    .profile-item label {
      display: block;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .profile-item p {
      background: #f1f7ff;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #dce9f9;
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
  <header>
    <h1>AIRtix.id</h1>
    <nav>
      <a href="LandingPage.php">Beranda</a>
      <a href="search.php">Pesan Tiket</a>
      <a href="history.php">Riwayat</a>
      <a href="checkin.php">Check-in</a>
    </nav>
  </header>

  <main>
    <div class="profile-card">
      <h2>Profil Pengguna</h2>

      <div class="profile-item">
        <label>Username:</label>
        <p>Admin</p>
      </div>

      <div class="profile-item">
        <label>Nama Lengkap:</label>
        <p>Admin FILKOM</p>
      </div>

      <div class="profile-item">
        <label>Email:</label>
        <p>admin@gmail.com</p>
      </div>

      <div class="profile-item">
        <label>Password:</label>
        <p>*******</p>
      </div>
    </div>
  </main>

  <?php if (!empty($message)): ?>
      <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>

  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved</p>
  </footer>
</body>
</html>
