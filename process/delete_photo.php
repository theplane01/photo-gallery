<?php
require_once '../config/database.php';
include '../includes/auth_check.php';

if (!isAdmin()) {
    header("Location: ../pages/dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $foto_id = $db->escape_string($_GET['id']);

    // Get photo file location
    $sql = "SELECT LokasiFile FROM gallery_foto WHERE FotoID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $foto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $photo = $result->fetch_assoc();
        $file_path = '../uploads/' . $photo['LokasiFile'];

        // Delete from database
        $delete_sql = "DELETE FROM gallery_foto WHERE FotoID = ?";
        $delete_stmt = $db->conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $foto_id);

        if ($delete_stmt->execute()) {
            // Delete physical file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            header("Location: ../pages/dashboard.php?success=Photo deleted successfully");
            exit();
        }
    }
}

header("Location: ../pages/dashboard.php?error=Failed to delete photo");
exit();
?>