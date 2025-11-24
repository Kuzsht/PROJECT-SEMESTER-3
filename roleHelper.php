<?php
/**
 * Role Helper Functions
 * Fungsi-fungsi untuk mengelola role dan authorization
 */

/**
 * Cek apakah user adalah admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Cek apakah user adalah user biasa
 */
function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

/**
 * Require admin access - redirect jika bukan admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: landingPage.php?error=access_denied");
        exit();
    }
}

/**
 * Get user role
 */
function getUserRole() {
    return $_SESSION['role'] ?? 'user';
}

/**
 * Check if user has permission
 */
function hasPermission($permission) {
    $role = getUserRole();
    
    $permissions = [
        'admin' => [
            'view_dashboard',
            'manage_users',
            'manage_airlines',
            'manage_tickets',
            'manage_bookings',
            'view_reports',
            'delete_data'
        ],
        'user' => [
            'view_dashboard',
            'book_ticket',
            'view_history',
            'checkin',
            'manage_profile'
        ]
    ];
    
    return in_array($permission, $permissions[$role] ?? []);
}

/**
 * Get role badge HTML
 */
function getRoleBadge($role) {
    if ($role === 'admin') {
        return '<span style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85em; font-weight: 600;">👑 ADMIN</span>';
    }
    return '<span style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85em; font-weight: 600;">👤 USER</span>';
}
?>