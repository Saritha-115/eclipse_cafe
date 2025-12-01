<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Include database connection
require_once '../includes/db.php';

// Get item ID from URL
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$confirm = isset($_GET['confirm']) ? $_GET['confirm'] : '';

// Check if confirmation is provided and ID is valid
if ($item_id > 0 && $confirm === 'yes') {
    try {
        // First, get the item details (especially the image filename)
        $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if ($item) {
            // Delete the item from database
            $delete_stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            $result = $delete_stmt->execute([$item_id]);
            
            if ($result) {
                // Delete the image file if it's not the default image
                if ($item['image'] !== 'default.jpg' && !empty($item['image'])) {
                    $image_path = '../uploads/' . $item['image'];
                    if (file_exists($image_path)) {
                        unlink($image_path); // Delete the file
                    }
                }
                
                // Set success message
                $_SESSION['success_message'] = "Menu item deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to delete item from database.";
            }
        } else {
            $_SESSION['error_message'] = "Item not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: Unable to delete item.";
        error_log("Delete error: " . $e->getMessage());
    }
} else {
    $_SESSION['error_message'] = "Invalid delete request.";
}

// Redirect back to menu page
header('Location: ../pages/menu.php');
exit;
?>