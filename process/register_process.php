<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->escape_string($_POST['username']);
    $email = $db->escape_string($_POST['email']);
    $nama_lengkap = $db->escape_string($_POST['nama_lengkap']);
    $alamat = $db->escape_string($_POST['alamat']);
    $password = $_POST['password']; // Simpan sebagai plain text
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: ../pages/register.php?error=Passwords do not match");
        exit();
    }

    // Check if username exists
    $check_sql = "SELECT UserID FROM gallery_user WHERE Username = ? OR Email = ?";
    $check_stmt = $db->conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: ../pages/register.php?error=Username or email already exists");
        exit();
    }

    // Insert user with plain text password (TANPA HASHING)
    $insert_sql = "INSERT INTO gallery_user (Username, Password, Email, NamaLengkap, Alamat, Level) VALUES (?, ?, ?, ?, ?, 'User')";
    $insert_stmt = $db->conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssss", $username, $password, $email, $nama_lengkap, $alamat);

    if ($insert_stmt->execute()) {
        header("Location: ../pages/register.php?success=Registration successful. Please login.");
        exit();
    } else {
        header("Location: ../pages/register.php?error=Registration failed");
        exit();
    }
}
?>