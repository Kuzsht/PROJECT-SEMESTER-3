<?php
if (!isset($_SESSION['username'])) {
    $username = 'Guest';
} else {
    $username = $_SESSION['username'];
}

// Fungsi untuk mendapatkan URL foto profil
function getProfilePhotoUrl($conn = null) {
    // Cek apakah ada foto di session
    if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])) {
        $photoPath = $_SESSION['photo'];
        if (file_exists($photoPath)) {
            return $photoPath;
        }
    }
    
    // Jika tidak ada di session atau file tidak ada, ambil dari database
    if ($conn && isset($_SESSION['email_user'])) {
        $email = $_SESSION['email_user'];
        $query = "SELECT photo FROM user WHERE email_user = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['photo']) && file_exists($row['photo'])) {
                $_SESSION['photo'] = $row['photo']; // Update session
                mysqli_stmt_close($stmt);
                return $row['photo'];
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Default avatar dari UI Avatars
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
    return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&size=200&background=9b59b6&color=fff&bold=true&font-size=0.5";
}

// Fungsi untuk mendapatkan nama lengkap user
function getUserFullName() {
    if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
        return $_SESSION['name'];
    }
    return isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
}

// Fungsi untuk mendapatkan email user
function getUserEmail() {
    if (isset($_SESSION['email_user']) && !empty($_SESSION['email_user'])) {
        return $_SESSION['email_user'];
    }
    return '';
}

function renderHeader($username, $conn = null) {
    $profilePhotoUrl = getProfilePhotoUrl($conn);
    $fullName = getUserFullName();
    $userEmail = getUserEmail();
?>
  <header>
    <a href="landingPage.php" class="logo-link">
      <h1>✈️ AIRtix.id</h1>
    </a>
    <nav>
      <!-- Center Navigation Group -->
      <ul class="center-nav">
        <li><a href="landingPage.php" class="nav-btn">✈️ Dashboard</a></li>
        <li><a href="history.php" class="nav-btn">📋 Riwayat</a></li>
        <li><a href="checkIn.php" class="nav-btn">✅ Check-in</a></li>
      </ul>
      <!-- Right Navigation Group -->
      <ul class="right-nav">
        <li><span class="username-display">👋 <?php echo htmlspecialchars($username); ?></span></li>
        <li class="profile-menu-container">
          <button type="button" class="profile-photo-btn" id="profileBtn" title="Profil Saya">
            <img src="<?php echo htmlspecialchars($profilePhotoUrl); ?>" alt="Foto Profil" class="profile-photo">
          </button>
          
          <!-- Profile Dropdown Menu -->
          <div class="profile-dropdown" id="profileDropdown">
            <!-- Header with user info -->
            <div class="dropdown-header">
              <img src="<?php echo htmlspecialchars($profilePhotoUrl); ?>" alt="Foto Profil" class="dropdown-header-photo">
              <div class="dropdown-header-info">
                <h3 class="dropdown-user-name"><?php echo htmlspecialchars($fullName); ?></h3>
                <?php if (!empty($userEmail)): ?>
                  <p class="dropdown-user-email"><?php echo htmlspecialchars($userEmail); ?></p>
                <?php endif; ?>
              </div>
            </div>
            
            <!-- Menu items -->
            <div class="dropdown-menu">
              <a href="profile.php" class="dropdown-item">
                <span class="dropdown-item-icon">👤</span>
                <span>Edit Profile</span>
              </a>
              <a href="logOut.php" class="dropdown-item logout">
                <span class="dropdown-item-icon">🚪</span>
                <span>Log Out</span>
              </a>
            </div>
          </div>
        </li>
      </ul>
    </nav>
  </header>
  
  <!-- Overlay untuk close dropdown -->
  <div class="dropdown-overlay" id="dropdownOverlay"></div>
  
  <!-- JavaScript untuk toggle dropdown -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const profileBtn = document.getElementById('profileBtn');
      const dropdown = document.getElementById('profileDropdown');
      const overlay = document.getElementById('dropdownOverlay');
      
      // Toggle dropdown saat button diklik
      profileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('active');
        overlay.classList.toggle('active');
      });
      
      // Close dropdown saat overlay diklik
      overlay.addEventListener('click', function() {
        dropdown.classList.remove('active');
        overlay.classList.remove('active');
      });
      
      // Close dropdown saat klik di luar
      document.addEventListener('click', function(e) {
        if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.classList.remove('active');
          overlay.classList.remove('active');
        }
      });
    });
  </script>
<?php
}

function renderFooter() {
?>
  <footer>
    <p>&copy; 2025 AIRtix.id | All Rights Reserved | Melayani Perjalanan Anda dengan Sepenuh Hati ❤️</p>
  </footer>
<?php
}

function renderBackgroundDecorations() {
?>
  <div class="bg-decorations">
    <div class="decoration-circle"></div>
    <div class="decoration-circle"></div>
  </div>
<?php
}
?>