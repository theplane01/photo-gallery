<?php
require_once '../config/database.php';

function getAllPhotos($search = '') {
    global $db;
    $sql = "SELECT f.*, u.Username, a.NamaAlbum 
            FROM gallery_foto f 
            LEFT JOIN gallery_user u ON f.UserID = u.UserID 
            LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID 
            WHERE f.JudulFoto LIKE ? 
            ORDER BY f.TanggalUnggah DESC";
    $searchTerm = "%$search%";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    return $stmt->get_result();
}

function getPhotoById($id) {
    global $db;
    $sql = "SELECT f.*, u.Username, a.NamaAlbum 
            FROM gallery_foto f 
            LEFT JOIN gallery_user u ON f.UserID = u.UserID 
            LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID 
            WHERE f.FotoID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserPhotos($user_id) {
    global $db;
    $sql = "SELECT f.*, a.NamaAlbum 
            FROM gallery_foto f 
            LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID 
            WHERE f.UserID = ? 
            ORDER BY f.TanggalUnggah DESC";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getAllUsers() {
    global $db;
    $result = $db->conn->query("SELECT * FROM gallery_user ORDER BY UserID DESC");
    return $result;
}

function getAllAlbums() {
    global $db;
    $result = $db->conn->query("SELECT * FROM gallery_album ORDER BY NamaAlbum");
    return $result;
}

function getUserLikeStatus($foto_id, $user_id) {
    global $db;
    $sql = "SELECT * FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("ii", $foto_id, $user_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function getLikeCount($foto_id) {
    global $db;
    $sql = "SELECT COUNT(*) as count FROM gallery_likefoto WHERE FotoID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $foto_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}

function getCommentCount($foto_id) {
    global $db;
    $sql = "SELECT COUNT(*) as count FROM gallery_komentarfoto WHERE FotoID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $foto_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}

function getUserAlbums($user_id) {
    global $db;
    $sql = "SELECT * FROM gallery_album WHERE UserID = ? ORDER BY NamaAlbum";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getAlbumById($album_id, $user_id = null) {
    global $db;
    $sql = "SELECT * FROM gallery_album WHERE AlbumID = ?";
    if ($user_id) {
        $sql .= " AND UserID = ?";
    }
    $stmt = $db->conn->prepare($sql);
    if ($user_id) {
        $stmt->bind_param("ii", $album_id, $user_id);
    } else {
        $stmt->bind_param("i", $album_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getPopularAlbums($limit = 6) {
    global $db;
    $sql = "SELECT a.*, COUNT(f.FotoID) as photo_count 
            FROM gallery_album a 
            LEFT JOIN gallery_foto f ON a.AlbumID = f.AlbumID 
            GROUP BY a.AlbumID 
            ORDER BY photo_count DESC 
            LIMIT ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

function getAlbumCoverPhoto($album_id) {
    global $db;
    $sql = "SELECT LokasiFile FROM gallery_foto 
            WHERE AlbumID = ? 
            ORDER BY TanggalUnggah DESC 
            LIMIT 1";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $album_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

?>
