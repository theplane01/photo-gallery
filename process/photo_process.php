<?php
require_once '../config/database.php';
include '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Add Photo
    if (isset($_POST['add_photo'])) {
        $judul_foto = $db->escape_string($_POST['judul_foto']);
        $deskripsi_foto = $db->escape_string($_POST['deskripsi_foto']);
        $album_id = !empty($_POST['album_id']) ? $db->escape_string($_POST['album_id']) : NULL;
        $user_id = $_SESSION['user_id'];
        $tanggal_unggah = date('Y-m-d');

        // File upload handling
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Validate file type
            if (!in_array($file['type'], $allowed_types)) {
                header("Location: ../pages/add_photo.php?error=Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.");
                exit();
            }

            // Validate file size
            if ($file['size'] > $max_size) {
                header("Location: ../pages/add_photo.php?error=File too large. Maximum size is 5MB.");
                exit();
            }

            // Dalam bagian add_photo, setelah mendapatkan $album_id
$album_id = !empty($_POST['album_id']) ? intval($_POST['album_id']) : NULL;

// Validate album ownership if album is selected
if ($album_id) {
    $check_album_sql = "SELECT AlbumID FROM gallery_album WHERE AlbumID = ? AND UserID = ?";
    $check_album_stmt = $db->conn->prepare($check_album_sql);
    $check_album_stmt->bind_param("ii", $album_id, $user_id);
    $check_album_stmt->execute();
    $album_result = $check_album_stmt->get_result();

    if ($album_result->num_rows === 0) {
        header("Location: ../pages/add_photo.php?error=Invalid album selected");
        exit();
    }
}

            // Generate unique filename
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = '../uploads/' . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Insert into database
                $sql = "INSERT INTO gallery_foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->conn->prepare($sql);
                $stmt->bind_param("ssssii", $judul_foto, $deskripsi_foto, $tanggal_unggah, $filename, $album_id, $user_id);

                if ($stmt->execute()) {
                    header("Location: ../pages/add_photo.php?success=Photo uploaded successfully");
                    exit();
                } else {
                    // Delete uploaded file if database insert fails
                    unlink($upload_path);
                    header("Location: ../pages/add_photo.php?error=Failed to upload photo");
                    exit();
                }
            } else {
                header("Location: ../pages/add_photo.php?error=Failed to upload file");
                exit();
            }
        } else {
            header("Location: ../pages/add_photo.php?error=Please select a photo");
            exit();
        }
    }

    // Edit Photo
    if (isset($_POST['edit_photo'])) {
        $foto_id = $db->escape_string($_POST['foto_id']);
        $judul_foto = $db->escape_string($_POST['judul_foto']);
        $deskripsi_foto = $db->escape_string($_POST['deskripsi_foto']);
        $album_id = !empty($_POST['album_id']) ? $db->escape_string($_POST['album_id']) : NULL;

        // Check if user owns the photo or is admin
        $check_sql = "SELECT UserID FROM gallery_foto WHERE FotoID = ?";
        $check_stmt = $db->conn->prepare($check_sql);
        $check_stmt->bind_param("i", $foto_id);
        $check_stmt->execute();
        $photo = $check_stmt->get_result()->fetch_assoc();

        if (!$photo || ($_SESSION['user_id'] != $photo['UserID'] && !isAdmin())) {
            header("Location: ../pages/dashboard.php");
            exit();
        }

        // Update photo details
        $sql = "UPDATE gallery_foto SET JudulFoto = ?, DeskripsiFoto = ?, AlbumID = ? WHERE FotoID = ?";
        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("ssii", $judul_foto, $deskripsi_foto, $album_id, $foto_id);

        if ($stmt->execute()) {
            header("Location: ../pages/edit_photo.php?id=$foto_id&success=Photo updated successfully");
            exit();
        } else {
            header("Location: ../pages/edit_photo.php?id=$foto_id&error=Failed to update photo");
            exit();
        }
    }
}
?>