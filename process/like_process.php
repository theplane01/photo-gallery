<?php
// like_process.php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to like photos'
    ]);
    exit();
}

require_once '../config/database.php';
$db = new Database();

$user_id = $_SESSION['user_id'];
$foto_id = $_POST['foto_id'] ?? 0;
$action = $_POST['action'] ?? 'like';

// Validate input
if (!$foto_id || !in_array($action, ['like', 'unlike'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit();
}

// Check if photo exists
$check_photo_sql = "SELECT FotoID FROM gallery_foto WHERE FotoID = ?";
$check_stmt = $db->conn->prepare($check_photo_sql);
$check_stmt->bind_param("i", $foto_id);
$check_stmt->execute();
$photo_exists = $check_stmt->get_result()->num_rows > 0;

if (!$photo_exists) {
    echo json_encode([
        'success' => false,
        'message' => 'Photo not found'
    ]);
    exit();
}

if ($action === 'like') {
    // Check if already liked
    $check_like_sql = "SELECT LikeID FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?";
    $check_like_stmt = $db->conn->prepare($check_like_sql);
    $check_like_stmt->bind_param("ii", $foto_id, $user_id);
    $check_like_stmt->execute();
    $already_liked = $check_like_stmt->get_result()->num_rows > 0;
    
    if ($already_liked) {
        echo json_encode([
            'success' => false,
            'message' => 'Already liked this photo'
        ]);
        exit();
    }
    
    // Insert like
    $insert_sql = "INSERT INTO gallery_likefoto (FotoID, UserID, TanggalLike) VALUES (?, ?, NOW())";
    $insert_stmt = $db->conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $foto_id, $user_id);
    
    if ($insert_stmt->execute()) {
        // Get updated like count
        $count_sql = "SELECT COUNT(*) as total FROM gallery_likefoto WHERE FotoID = ?";
        $count_stmt = $db->conn->prepare($count_sql);
        $count_stmt->bind_param("i", $foto_id);
        $count_stmt->execute();
        $result = $count_stmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'action' => 'liked',
            'total_likes' => $result['total'],
            'message' => 'Photo liked successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to like photo'
        ]);
    }
    
} elseif ($action === 'unlike') {
    // Delete like
    $delete_sql = "DELETE FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?";
    $delete_stmt = $db->conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $foto_id, $user_id);
    
    if ($delete_stmt->execute()) {
        // Get updated like count
        $count_sql = "SELECT COUNT(*) as total FROM gallery_likefoto WHERE FotoID = ?";
        $count_stmt = $db->conn->prepare($count_sql);
        $count_stmt->bind_param("i", $foto_id);
        $count_stmt->execute();
        $result = $count_stmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'action' => 'unliked',
            'total_likes' => $result['total'],
            'message' => 'Photo unliked successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to unlike photo'
        ]);
    }
}
?>