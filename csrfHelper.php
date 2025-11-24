<?php
/**
 * CSRF Helper Functions
 * File ini berisi fungsi-fungsi untuk proteksi CSRF
 */

/**
 * Generate CSRF Token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate CSRF Token
 */
function regenerateCsrfToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Get CSRF Token HTML Input
 */
function csrfTokenInput() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate Password Strength
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 huruf besar";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 huruf kecil";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 angka";
    }
    
    return $errors;
}

/**
 * Sanitize Input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Validate Email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Safe Redirect
 */
function safeRedirect($url) {
    // Only allow internal redirects
    $allowedUrls = [
        'index.php', 'landingPage.php', 'search.php', 'profile.php',
        'history.php', 'checkIn.php', 'inputSearch.php', 'seat.php',
        'bookingPayment.php', 'paymentSuccess.php', 'signUp.php', 'logOut.php',
        'adminDashboard.php', 'adminUsers.php', 'adminAirlines.php', 
        'adminTickets.php', 'adminBookings.php'
    ];
    
    $basename = basename(parse_url($url, PHP_URL_PATH));
    
    if (in_array($basename, $allowedUrls)) {
        header("Location: $url");
        exit();
    }
    
    // Default fallback
    header("Location: index.php");
    exit();
}

/**
 * Check if user is logged in
 */
function requireLogin() {
    if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
        safeRedirect('index.php');
    }
}

/**
 * Secure session initialization
 */
function initSecureSession() {
    // Regenerate session ID if not done
    if (!isset($_SESSION['initialized'])) {
        session_regenerate_id(true);
        $_SESSION['initialized'] = true;
    }
    
    // Set session timeout (30 minutes)
    $timeout = 1800;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        safeRedirect('index.php?timeout=1');
    }
    $_SESSION['last_activity'] = time();
}
?>