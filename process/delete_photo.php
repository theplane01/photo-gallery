<?php
// process/delete_photo.php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

// Check if photo ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../pages/explore.php');
    exit();
}

$photo_id = intval($_GET['id']);
$db = new Database();

try {
    // 1. Get photo details to check ownership
    $check_sql = "SELECT * FROM gallery_foto WHERE FotoID = ?";
    $check_stmt = $db->conn->prepare($check_sql);
    $check_stmt->bind_param("i", $photo_id);
    $check_stmt->execute();
    $photo = $check_stmt->get_result()->fetch_assoc();
    
    if (!$photo) {
        $_SESSION['error'] = 'Foto tidak ditemukan';
        header('Location: ../pages/explore.php');
        exit();
    }
    
    // 2. Check if user is the owner or admin
    $is_owner = ($_SESSION['user_id'] == $photo['UserID']);
    $is_admin = false;
    
    // Check if user is admin (adjust based on your role system)
    if (function_exists('isAdmin')) {
        $is_admin = isAdmin();
    } else {
        // Fallback: check session role if exists
        $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    if (!$is_owner && !$is_admin) {
        $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus foto ini';
        header('Location: ../pages/detail.php?id=' . $photo_id);
        exit();
    }
    
    // 3. Get file location before deleting
    $file_location = $photo['LokasiFile'];
    
    // 4. Delete associated likes
    $delete_likes_sql = "DELETE FROM gallery_likefoto WHERE FotoID = ?";
    $delete_likes_stmt = $db->conn->prepare($delete_likes_sql);
    $delete_likes_stmt->bind_param("i", $photo_id);
    $delete_likes_stmt->execute();
    
    // 5. Delete associated comments
    $delete_comments_sql = "DELETE FROM gallery_komentarfoto WHERE FotoID = ?";
    $delete_comments_stmt = $db->conn->prepare($delete_comments_sql);
    $delete_comments_stmt->bind_param("i", $photo_id);
    $delete_comments_stmt->execute();
    
    // 6. Delete photo from database
    $delete_sql = "DELETE FROM gallery_foto WHERE FotoID = ?";
    $delete_stmt = $db->conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $photo_id);
    $delete_result = $delete_stmt->execute();
    
    if ($delete_result) {
        // 7. Delete the actual file
        $file_path = '../uploads/' . $file_location;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // 8. If photo is in an album, update album count
        if (!empty($photo['AlbumID'])) {
            $album_sql = "SELECT COUNT(*) as photo_count FROM gallery_foto WHERE AlbumID = ?";
            $album_stmt = $db->conn->prepare($album_sql);
            $album_stmt->bind_param("i", $photo['AlbumID']);
            $album_stmt->execute();
            $album_result = $album_stmt->get_result()->fetch_assoc();
            
            if ($album_result['photo_count'] == 0) {
                // If album is empty after deletion, you might want to delete the album too
                // Uncomment if needed:
                // $delete_album_sql = "DELETE FROM gallery_album WHERE AlbumID = ?";
                // $delete_album_stmt = $db->conn->prepare($delete_album_sql);
                // $delete_album_stmt->bind_param("i", $photo['AlbumID']);
                // $delete_album_stmt->execute();
            }
        }
        
        $_SESSION['success'] = 'Foto berhasil dihapus';
        
        // Redirect based on where user came from
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            if (strpos($referer, 'profile.php') !== false || strpos($referer, 'dashboard.php') !== false) {
                header('Location: ../pages/dashboard.php');
                exit();
            }
        }
        
        header('Location: ../pages/explore.php');
        exit();
    } else {
        throw new Exception("Gagal menghapus foto dari database");
    }
    
} catch (Exception $e) {
    error_log("Delete photo error: " . $e->getMessage());
    $_SESSION['error'] = 'Terjadi kesalahan saat menghapus foto: ' . $e->getMessage();
    header('Location: ../pages/detail.php?id=' . $photo_id);
    exit();
}
?>