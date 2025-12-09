<?php
// comment_process.php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Silakan login untuk berkomentar'
    ]);
    exit();
}

require_once '../config/database.php';
$db = new Database();

$user_id = $_SESSION['user_id'];
$foto_id = $_POST['foto_id'] ?? 0;
$komentar = $_POST['komentar'] ?? '';

// Validate input
if (!$foto_id || empty(trim($komentar))) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Komentar tidak boleh kosong'
    ]);
    exit();
}

if (strlen($komentar) > 500) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Komentar terlalu panjang (maksimal 500 karakter)'
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
        'status' => 'error',
        'message' => 'Foto tidak ditemukan'
    ]);
    exit();
}

// Insert comment
$insert_sql = "INSERT INTO gallery_komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES (?, ?, ?, NOW())";
$insert_stmt = $db->conn->prepare($insert_sql);
$insert_stmt->bind_param("iis", $foto_id, $user_id, $komentar);

if ($insert_stmt->execute()) {
    // Get user info for the response (INCLUDING profile_pic)
    $user_sql = "SELECT Username, NamaLengkap, profile_pic, Email FROM gallery_user WHERE UserID = ?";
    $user_stmt = $db->conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result()->fetch_assoc();
    
    // Get comment ID
    $comment_id = $insert_stmt->insert_id;
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Komentar berhasil ditambahkan',
        'comment' => [
            'id' => $comment_id,
            'username' => $user_result['Username'],
            'nama_lengkap' => $user_result['NamaLengkap'] ?? $user_result['Username'],
            'profile_pic' => $user_result['profile_pic'] ?? null,
            'komentar' => htmlspecialchars($komentar),
            'tanggal' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambahkan komentar'
    ]);
}
?>