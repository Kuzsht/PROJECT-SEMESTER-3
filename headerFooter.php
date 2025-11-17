<?php
if (!isset($_SESSION['username'])) {
    $username = 'Guest';
} else {
    $username = $_SESSION['username'];
}

function renderHeader($username) {
?>
  <header>
    <a href="landingPage.php" class="logo-link">
      <h1>✈️ AIRtix.id</h1>
    </a>
    <nav>
      <ul>
        <li><a href="profile.php" class="username-btn">👋 <?php echo htmlspecialchars($username); ?></a></li>
        <li><a href="history.php">📋 Riwayat</a></li>
        <li><a href="checkIn.php">✅ Check-in</a></li>
        <li><a class="logout-btn" href="logOut.php">Logout</a></li>
      </ul>
    </nav>
  </header>
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