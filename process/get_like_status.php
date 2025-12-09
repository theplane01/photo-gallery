<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['foto_id']) && isset($_SESSION['user_id'])) {
    $foto_id = intval($_GET['foto_id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if user liked this photo
    $like_sql = "SELECT * FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?";
    $like_stmt = $db->conn->prepare($like_sql);
    $like_stmt->bind_param("ii", $foto_id, $user_id);
    $like_stmt->execute();
    $user_liked = $like_stmt->get_result()->num_rows > 0;
    
    // Get total like count
    $count_sql = "SELECT COUNT(*) as like_count FROM gallery_likefoto WHERE FotoID = ?";
    $count_stmt = $db->conn->prepare($count_sql);
    $count_stmt->bind_param("i", $foto_id);
    $count_stmt->execute();
    $like_count = $count_stmt->get_result()->fetch_assoc()['like_count'];
    
    echo json_encode([
        'user_liked' => $user_liked,
        'like_count' => $like_count
    ]);
} else {
    echo json_encode([
        'user_liked' => false,
        'like_count' => 0
    ]);
}
?>