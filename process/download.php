<?php
require_once '../config/database.php';
include '../includes/auth_check.php';

if (isset($_GET['id'])) {
    $foto_id = $db->escape_string($_GET['id']);

    $sql = "SELECT JudulFoto, LokasiFile FROM gallery_foto WHERE FotoID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $foto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $photo = $result->fetch_assoc();
        $file_path = '../uploads/' . $photo['LokasiFile'];

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($photo['JudulFoto'] . '.' . pathinfo($file_path, PATHINFO_EXTENSION)) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
    }
}

header("Location: ../pages/dashboard.php?error=File not found");
exit();
?>