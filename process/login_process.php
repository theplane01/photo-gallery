<?php
session_start(); // PASTIKAN session_start() ADA DI AWAL
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->escape_string($_POST['username']);
    $password = $_POST['password'];

    // Debug: lihat apa yang diterima
    error_log("Login attempt: username=$username, password=$password");

    $sql = "SELECT * FROM gallery_user WHERE Username = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Debug: lihat data user dari database
        error_log("User found: " . print_r($user, true));
        
        // Bandingkan password plain text
        if ($password === $user['Password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['nama_lengkap'] = $user['NamaLengkap'];
            $_SESSION['level'] = $user['Level'];
            
            // Debug: session set
            error_log("Session set: user_id=" . $_SESSION['user_id']);
            
            // Redirect ke dashboard
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            error_log("Password mismatch: input=$password, db=" . $user['Password']);
        }
    } else {
        error_log("User not found: $username");
    }
    
    // Jika gagal, redirect kembali ke login dengan error
    header("Location: ../pages/login.php?error=Invalid username or password");
    exit();
}
?>