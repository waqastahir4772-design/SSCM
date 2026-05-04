<?php
/**
 * Database Connection Configuration
 * Project: School Supply Chain Management System (SSCMS)
 */

$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP/WAMP password is empty
$dbname = 'school_scm';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Utility function to sanitize input
 */
if (!function_exists('escape')) {
    function escape($data) {
        global $conn;
        return mysqli_real_escape_string($conn, trim($data));
    }
}

/**
 * Utility function to escape output for HTML
 */
if (!function_exists('h')) {
    function h($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
?>
