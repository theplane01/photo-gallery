<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Add Album
    if (isset($_POST['add_album'])) {
        $nama_album = trim($db->escape_string($_POST['nama_album']));
        $deskripsi = trim($db->escape_string($_POST['deskripsi']));

        if (empty($nama_album)) {
            header("Location: ../pages/my_albums.php?error=Album name is required");
            exit();
        }

        // Check if album name already exists for this user
        $check_sql = "SELECT AlbumID FROM gallery_album WHERE NamaAlbum = ? AND UserID = ?";
        $check_stmt = $db->conn->prepare($check_sql);
        $check_stmt->bind_param("si", $nama_album, $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            header("Location: ../pages/my_albums.php?error=You already have an album with this name");
            exit();
        }

        // Insert new album
        $insert_sql = "INSERT INTO gallery_album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) VALUES (?, ?, CURDATE(), ?)";
        $insert_stmt = $db->conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssi", $nama_album, $deskripsi, $user_id);

        if ($insert_stmt->execute()) {
            header("Location: ../pages/my_albums.php?success=Album created successfully");
            exit();
        } else {
            header("Location: ../pages/my_albums.php?error=Failed to create album");
            exit();
        }
    }

    // Edit Album
    if (isset($_POST['edit_album'])) {
        $album_id = intval($_POST['album_id']);
        $nama_album = trim($db->escape_string($_POST['nama_album']));
        $deskripsi = trim($db->escape_string($_POST['deskripsi']));

        // Verify album ownership
        $verify_sql = "SELECT AlbumID FROM gallery_album WHERE AlbumID = ? AND UserID = ?";
        $verify_stmt = $db->conn->prepare($verify_sql);
        $verify_stmt->bind_param("ii", $album_id, $user_id);
        $verify_stmt->execute();
        
        if ($verify_stmt->get_result()->num_rows === 0) {
            header("Location: ../pages/my_albums.php?error=Album not found or access denied");
            exit();
        }

        // Update album
        $update_sql = "UPDATE gallery_album SET NamaAlbum = ?, Deskripsi = ? WHERE AlbumID = ?";
        $update_stmt = $db->conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $nama_album, $deskripsi, $album_id);

        if ($update_stmt->execute()) {
            header("Location: ../pages/my_albums.php?success=Album updated successfully");
            exit();
        } else {
            header("Location: ../pages/my_albums.php?error=Failed to update album");
            exit();
        }
    }
}

// Delete Album (GET request)
if (isset($_GET['delete_id'])) {
    $album_id = intval($_GET['delete_id']);
    $user_id = $_SESSION['user_id'];

    // Verify album ownership
    $verify_sql = "SELECT AlbumID FROM gallery_album WHERE AlbumID = ? AND UserID = ?";
    $verify_stmt = $db->conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $album_id, $user_id);
    $verify_stmt->execute();
    
    if ($verify_stmt->get_result()->num_rows === 0) {
        header("Location: ../pages/my_albums.php?error=Album not found or access denied");
        exit();
    }

    // Delete album (cascade will delete associated photos)
    $delete_sql = "DELETE FROM gallery_album WHERE AlbumID = ?";
    $delete_stmt = $db->conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $album_id);

    if ($delete_stmt->execute()) {
        header("Location: ../pages/my_albums.php?success=Album deleted successfully");
        exit();
    } else {
        header("Location: ../pages/my_albums.php?error=Failed to delete album");
        exit();
    }
}
?>