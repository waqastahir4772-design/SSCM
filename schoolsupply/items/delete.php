<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

// Only Admin can delete
check_role('Admin');

if (isset($_GET['id'])) {
    $id = escape($_GET['id']);
    
    // Soft delete
    $sql = "UPDATE items SET deleted_at = NOW() WHERE id = $id";
    
    if ($conn->query($sql)) {
        redirect('index.php', 'Item deleted successfully.');
    } else {
        redirect('index.php', 'Error deleting item: ' . $conn->error, 'danger');
    }
} else {
    redirect('index.php', 'Invalid request.', 'danger');
}
?>
