<?php
/**
 * Common Utility Functions for SSCMS
 */

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect with a message
 */
function redirect($url, $msg = '', $type = 'success') {
    $param = !empty($msg) ? "?msg=" . urlencode($msg) . "&type=" . $type : "";
    header("Location: " . $url . $param);
    exit();
}

/**
 * Check if user has required role
 */
function check_role($roles) {
    if (!is_array($roles)) $roles = [$roles];
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        redirect('dashboard.php', 'Access denied. You do not have permission to view this page.', 'danger');
    }
}

/**
 * Format currency
 */
function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Get status badge class
 */
function get_status_badge($status) {
    switch (strtolower($status)) {
        case 'active':
        case 'approved':
        case 'completed':
        case 'received':
        case 'in stock':
            return 'success';
        case 'pending':
        case 'ordered':
        case 'in transit':
        case 'warning':
            return 'warning';
        case 'inactive':
        case 'rejected':
        case 'cancelled':
        case 'danger':
        case 'low stock':
            return 'danger';
        default:
            return 'secondary';
    }
}
/**
 * Helper to count rows in a table
 */
function get_count($table, $where = "1=1") {
    global $conn;
    if (!$conn) return 0;
    $res = $conn->query("SELECT COUNT(*) as count FROM $table WHERE $where");
    return $res ? $res->fetch_assoc()['count'] : 0;
}
?>
