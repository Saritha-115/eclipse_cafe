<?php
require_once 'includes/auth_check.php';
require_once '../includes/db.php';

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0 && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    try {
        // Get item details first
        $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if ($item) {
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            $stmt->execute([$item_id]);
            
            // Delete image file if not default
            if ($item['image'] !== 'default.jpg' && file_exists('../uploads/' . $item['image'])) {
                unlink('../uploads/' . $item['image']);
            }
            
            $_SESSION['success_message'] = "Item deleted successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Failed to delete item.";
        error_log("Delete error: " . $e->getMessage());
    }
}

header('Location: ../pages/menu.php');
exit;