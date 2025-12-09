<?php
// Pastikan session sudah start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// Get user's albums if logged in
$user_albums = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $albums_sql = "SELECT a.*, COUNT(f.FotoID) as photo_count 
                   FROM gallery_album a 
                   LEFT JOIN gallery_foto f ON a.AlbumID = f.AlbumID 
                   WHERE a.UserID = ? 
                   GROUP BY a.AlbumID 
                   ORDER BY a.TanggalDibuat DESC 
                   LIMIT 10";
    $albums_stmt = $db->conn->prepare($albums_sql);
    $albums_stmt->bind_param("i", $user_id);
    $albums_stmt->execute();
    $user_albums = $albums_stmt->get_result();
}
?>

<!-- Sidebar -->
<div class="sidebar fixed left-0 top-0 h-screen w-64 bg-white border-r border-gray-200 flex flex-col z-40">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <i class="fas fa-camera-retro text-2xl text-blue-600"></i>
            <span class="text-xl font-bold text-gray-800">PhotoGallery</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 overflow-y-auto">
        <nav class="p-4 space-y-2">
            <a href="../index.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                <i class="fas fa-home w-5"></i>
                <span>Beranda</span>
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <i class="fas fa-compass w-5"></i>
                    <span>Explore</span>
                </a>
                
                <a href="add_photo.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'add_photo.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <i class="fas fa-plus w-5"></i>
                    <span>Upload Foto</span>
                </a>
                
                <a href="my_albums.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'my_albums.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <i class="fas fa-folder w-5"></i>
                    <span>Album Saya</span>
                </a>
                
                <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'Admin'): ?>
                    <a href="admin.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                        <i class="fas fa-cog w-5"></i>
                        <span>Admin Panel</span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <i class="fas fa-sign-in-alt w-5"></i>
                    <span>Login</span>
                </a>
                
                <a href="register.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <i class="fas fa-user-plus w-5"></i>
                    <span>Register</span>
                </a>
            <?php endif; ?>
        </nav>

        <!-- User Albums Section -->
        <?php if (isset($_SESSION['user_id']) && $user_albums->num_rows > 0): ?>
            <div class="px-4 py-2">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Album Saya</h3>
                    <a href="my_albums.php" class="text-xs text-blue-600 hover:text-blue-800">Lihat semua</a>
                </div>
                <div class="space-y-1">
                    <?php while ($album = $user_albums->fetch_assoc()): ?>
                        <a href="album_detail.php?id=<?php echo $album['AlbumID']; ?>" 
                           class="flex items-center justify-between p-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition group">
                            <div class="flex items-center space-x-2 truncate">
                                <i class="fas fa-folder text-yellow-500 w-4"></i>
                                <span class="truncate"><?php echo htmlspecialchars($album['NamaAlbum']); ?></span>
                            </div>
                            <span class="text-xs text-gray-400 bg-gray-200 px-1 rounded"><?php echo $album['photo_count']; ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php elseif (isset($_SESSION['user_id'])): ?>
            <div class="px-4 py-2">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Album Saya</h3>
                <div class="text-center py-4">
                    <i class="fas fa-folder-open text-2xl text-gray-300 mb-2"></i>
                    <p class="text-xs text-gray-500">Belum ada album</p>
                    <a href="my_albums.php" class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">Buat album</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- User Profile Section -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">
                        <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?>
                    </p>
                    <p class="text-xs text-gray-500 truncate">@<?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 hidden" id="sidebarOverlay"></div>

<style>
.sidebar {
    transition: transform 0.3s ease;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .sidebar.open {
        transform: translateX(0);
    }
}

/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>