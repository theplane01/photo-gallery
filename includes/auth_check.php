<?php
// Pastikan tidak ada karakter atau spasi sebelum <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// // Cek jika user tidak login, redirect ke index.php
// if (!isset($_SESSION['user_id'])) {
//     // Hapus output buffer jika ada
//     if (ob_get_length()) ob_end_clean();
//     header("Location: ../index.php");
//     exit();
// }

// Fungsi cek admin/user
function isAdmin() {
    return isset($_SESSION['level']) && $_SESSION['level'] === 'Admin';
}

function isUser() {
    return isset($_SESSION['level']) && $_SESSION['level'] === 'User';
}
// JANGAN ada spasi atau karakter setelah ?>