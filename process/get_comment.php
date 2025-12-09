<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['foto_id'])) {
    $foto_id = intval($_GET['foto_id']);
    
    $sql = "SELECT k.*, u.Username, u.NamaLengkap 
            FROM gallery_komentarfoto k 
            JOIN gallery_user u ON k.UserID = u.UserID 
            WHERE k.FotoID = ? 
            ORDER BY k.TanggalKomentar DESC 
            LIMIT 50";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $foto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'id' => $row['KomentarID'],
            'username' => $row['Username'],
            'nama_lengkap' => $row['NamaLengkap'],
            'komentar' => $row['IsiKomentar'],
            'tanggal' => $row['TanggalKomentar'],
            'user_id' => $row['UserID']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($comments);
}
?>