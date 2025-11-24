<?php
if (!isset($_SESSION['username'])) {
    $username = 'Guest';
} else {
    $username = $_SESSION['username'];
}

// PENTING: Load role helper jika belum
if (!function_exists('isAdmin')) {
    require_once __DIR__ . '/roleHelper.php';
}

// Fungsi untuk mendapatkan URL foto profil
function getProfilePhotoUrl($conn = null) {
    if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])) {
        $photoPath = $_SESSION['photo'];
        if (file_exists($photoPath)) {
            return htmlspecialchars($photoPath, ENT_QUOTES, 'UTF-8');
        }
    }
    
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
                $_SESSION['photo'] = $row['photo'];
                mysqli_stmt_close($stmt);
                return htmlspecialchars($row['photo'], ENT_QUOTES, 'UTF-8');
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
    return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&size=200&background=9b59b6&color=fff&bold=true&font-size=0.5";
}

function getUserFullName() {
    if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
        return htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8');
    }
    return isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : 'User';
}

function getUserEmail() {
    if (isset($_SESSION['email_user']) && !empty($_SESSION['email_user'])) {
        return htmlspecialchars($_SESSION['email_user'], ENT_QUOTES, 'UTF-8');
    }
    return '';
}

function renderHeader($username, $conn = null) {
    $profilePhotoUrl = getProfilePhotoUrl($conn);
    $fullName = getUserFullName();
    $userEmail = getUserEmail();
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $userRole = getUserRole();
    $roleBadge = getRoleBadge($userRole);
?>
  <header>
    <a href="<?php echo isAdmin() ? 'adminDashboard.php' : 'landingPage.php'; ?>" class="logo-link">
      <h1>✈️ AIRtix.id</h1>
    </a>
    <nav>
      <!-- Center Navigation Group -->
      <ul class="center-nav">
        <?php if (isAdmin()): ?>
          <!-- Menu untuk Admin -->
          <li><a href="adminDashboard.php" class="nav-btn">👑 Admin Panel</a></li>
          <li><a href="landingPage.php" class="nav-btn">✈️ Dashboard</a></li>
        <?php else: ?>
          <!-- Menu untuk User Biasa -->
          <li><a href="landingPage.php" class="nav-btn">✈️ Dashboard</a></li>
          <li><a href="history.php" class="nav-btn">📋 Riwayat</a></li>
          <li><a href="checkIn.php" class="nav-btn">✅ Check-in</a></li>
        <?php endif; ?>
      </ul>
      <!-- Right Navigation Group -->
      <ul class="right-nav">
        <li><span class="username-display"><?php echo $username; ?></span></li>
        <li class="profile-menu-container">
          <button type="button" class="profile-photo-btn" id="profileBtn" title="Profil Saya">
            <img src="<?php echo $profilePhotoUrl; ?>" alt="Foto Profil" class="profile-photo">
          </button>
          
          <!-- Profile Dropdown Menu -->
          <div class="profile-dropdown" id="profileDropdown">
            <!-- Header with user info -->
            <div class="dropdown-header">
              <img src="<?php echo $profilePhotoUrl; ?>" alt="Foto Profil" class="dropdown-header-photo">
              <div class="dropdown-header-info">
                <h3 class="dropdown-user-name"><?php echo $fullName; ?></h3>
                <?php if (!empty($userEmail)): ?>
                  <p class="dropdown-user-email"><?php echo $userEmail; ?></p>
                <?php endif; ?>
                <div style="margin-top: 8px;">
                  <?php echo $roleBadge; ?>
                </div>
              </div>
            </div>
            
            <!-- Menu items -->
            <div class="dropdown-menu">
              <?php if (isAdmin()): ?>
                <a href="adminDashboard.php" class="dropdown-item">
                  <span class="dropdown-item-icon">👑</span>
                  <span>Admin Dashboard</span>
                </a>
              <?php endif; ?>
              
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
      
      if (profileBtn && dropdown && overlay) {
        profileBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          dropdown.classList.toggle('active');
          overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
          dropdown.classList.remove('active');
          overlay.classList.remove('active');
        });
        
        document.addEventListener('click', function(e) {
          if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
            overlay.classList.remove('active');
          }
        });
      }
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