<?php
$title = "Admin Panel";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

require_once '../config/database.php';
$db = new Database();

// Handle role update
if (isset($_POST['update_role'])) {
    $target_user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    // Prevent admin from changing their own role
    if ($target_user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot change your own role!";
    } else {
        $update_sql = "UPDATE gallery_user SET Level = ? WHERE UserID = ?";
        $update_stmt = $db->conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_role, $target_user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success'] = "User role updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update user role!";
        }
    }
    
    header("Location: admin.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    if ($delete_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account!";
    } else {
        $delete_sql = "DELETE FROM gallery_user WHERE UserID = ?";
        $delete_stmt = $db->conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $delete_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete user!";
        }
    }
    
    header("Location: admin.php");
    exit();
}

// Get all users
$users_sql = "SELECT * FROM gallery_user ORDER BY UserID DESC";
$users = $db->conn->query($users_sql);

// Get all photos
$photos_sql = "SELECT f.*, u.Username, a.NamaAlbum 
               FROM gallery_foto f 
               LEFT JOIN gallery_user u ON f.UserID = u.UserID 
               LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID 
               ORDER BY f.TanggalUnggah DESC 
               LIMIT 50";
$photos = $db->conn->query($photos_sql);

// Get stats
$total_users = $users->num_rows;
$total_photos_sql = "SELECT COUNT(*) as total FROM gallery_foto";
$total_photos = $db->conn->query($total_photos_sql)->fetch_assoc()['total'];

$total_albums_sql = "SELECT COUNT(*) as total FROM gallery_album";
$total_albums = $db->conn->query($total_albums_sql)->fetch_assoc()['total'];

$total_likes_sql = "SELECT COUNT(*) as total FROM gallery_likefoto";
$total_likes = $db->conn->query($total_likes_sql)->fetch_assoc()['total'];

// Get recent activity
$recent_activity_sql = "SELECT 'photo' as type, JudulFoto as title, TanggalUnggah as date, Username 
                       FROM gallery_foto f 
                       LEFT JOIN gallery_user u ON f.UserID = u.UserID 
                       UNION ALL 
                       SELECT 'album' as type, NamaAlbum as title, TanggalDibuat as date, Username 
                       FROM gallery_album a 
                       LEFT JOIN gallery_user u ON a.UserID = u.UserID 
                       ORDER BY date DESC 
                       LIMIT 10";
$recent_activity = $db->conn->query($recent_activity_sql);
?>

<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100 py-8 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="../index.php" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-400 transition-colors duration-200">
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-home text-white text-xs"></i>
                            </div>
                            Beranda
                        </a>
                    </li>
                    <li class="inline-flex items-center">
                        <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                        <a href="dashboard.php" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-400 transition-colors duration-200">
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                            <span class="ml-1 text-sm font-medium text-blue-400 md:ml-2">Admin Panel</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-gradient-to-r from-green-900/40 to-green-800/40 border border-green-700/50 text-green-300 px-6 py-4 rounded-xl shadow-lg mb-8 flex items-center space-x-3 animate-fade-in">
                <div class="w-8 h-8 bg-gradient-to-r from-green-900/50 to-green-800/50 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-400"></i>
                </div>
                <span class="font-semibold"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-gradient-to-r from-red-900/40 to-red-800/40 border border-red-700/50 text-red-300 px-6 py-4 rounded-xl shadow-lg mb-8 flex items-center space-x-3 animate-fade-in">
                <div class="w-8 h-8 bg-gradient-to-r from-red-900/50 to-red-800/50 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <span class="font-semibold"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl shadow-xl p-8 mb-8 overflow-hidden relative border border-gray-700">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23ffffff" fill-opacity="0.4" fill-rule="evenodd"/%3E%3C/svg%3E'); background-size: 120px;"></div>
            </div>
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div class="mb-6 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-900/40 to-blue-800/40 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-cog text-blue-400 text-xl"></i>
                            </div>
                            Admin Panel
                        </h1>
                        <p class="text-gray-300 text-lg">Kelola dan pantau aktivitas sistem PhotoGallery</p>
                    </div>
                    <div class="bg-gradient-to-r from-gray-800/50 to-gray-900/50 backdrop-blur-sm px-4 py-2 rounded-xl border border-gray-700">
                        <p class="text-sm text-gray-300">Welcome, <span class="font-semibold text-white"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl p-6 transform transition-all duration-300 hover:-translate-y-1 border border-gray-700 hover:border-blue-500/50 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Users</p>
                        <p class="text-3xl font-bold text-white"><?php echo $total_users; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-900/50 to-blue-800/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-400">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>Active users</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl p-6 transform transition-all duration-300 hover:-translate-y-1 border border-gray-700 hover:border-green-500/50 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Photos</p>
                        <p class="text-3xl font-bold text-white"><?php echo $total_photos; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-900/50 to-green-800/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-images text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-400">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>Growing collection</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl p-6 transform transition-all duration-300 hover:-translate-y-1 border border-gray-700 hover:border-purple-500/50 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Albums</p>
                        <p class="text-3xl font-bold text-white"><?php echo $total_albums; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-900/50 to-purple-800/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-folder text-purple-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-400">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>Organized content</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl p-6 transform transition-all duration-300 hover:-translate-y-1 border border-gray-700 hover:border-red-500/50 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Total Likes</p>
                        <p class="text-3xl font-bold text-white"><?php echo $total_likes; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-900/50 to-red-800/50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heart text-red-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-400">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>Community engagement</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Users Management -->
            <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-700">
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-6 py-4 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-600/20"></div>
                    </div>
                    <div class="relative z-10 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-blue-900/40 to-blue-800/40 flex items-center justify-center mr-3">
                                <i class="fas fa-users text-blue-400"></i>
                            </div>
                            Users Management
                        </h2>
                        <span class="bg-gradient-to-r from-blue-900/40 to-blue-800/40 text-blue-300 px-3 py-1 rounded-full text-sm font-semibold border border-blue-700/50">
                            <?php echo $total_users; ?> users
                        </span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            <?php 
                            $users->data_seek(0); // Reset pointer
                            while ($user = $users->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-900/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center relative overflow-hidden">
                                                <?php if (!empty($user['profile_pic'])): ?>
                                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                                         alt="Profile" 
                                                         class="w-full h-full rounded-full object-cover">
                                                <?php else: ?>
                                                    <i class="fas fa-user text-white text-sm"></i>
                                                <?php endif; ?>
                                                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/30 to-purple-700/30"></div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-white"><?php echo htmlspecialchars($user['Username']); ?></div>
                                                <div class="text-sm text-gray-400"><?php echo htmlspecialchars($user['Email']); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($user['NamaLengkap']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" class="inline-flex items-center space-x-2">
                                            <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                            <select name="role" class="text-sm border border-gray-600 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-900 text-white
                                                <?php echo $user['Level'] === 'Admin' ? 'bg-gradient-to-r from-purple-900/40 to-purple-800/40 text-purple-300 border-purple-700/50' : 'bg-gradient-to-r from-green-900/40 to-green-800/40 text-green-300 border-green-700/50'; ?>">
                                                <option value="User" <?php echo $user['Level'] === 'User' ? 'selected' : ''; ?>>User</option>
                                                <option value="Admin" <?php echo $user['Level'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                            <?php if ($user['UserID'] != $_SESSION['user_id']): ?>
                                                <button type="submit" name="update_role" 
                                                        class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-3 py-1 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 text-sm flex items-center space-x-1">
                                                    <i class="fas fa-sync-alt text-xs"></i>
                                                    <span>Update</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-500 bg-gray-900/50 px-2 py-1 rounded">(Current User)</span>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if ($user['UserID'] != $_SESSION['user_id']): ?>
                                            <a href="admin.php?delete_id=<?php echo $user['UserID']; ?>" 
                                               class="text-red-400 hover:text-red-300 transition-colors duration-200 flex items-center space-x-1 transform hover:scale-105 hover:bg-red-900/30 px-2 py-1 rounded"
                                               onclick="return confirm('Are you sure you want to delete this user? All their photos and albums will also be deleted.')">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-500">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-700">
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-6 py-4 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-emerald-600/20"></div>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-green-900/40 to-green-800/40 flex items-center justify-center mr-3">
                                <i class="fas fa-history text-green-400"></i>
                            </div>
                            Recent Activity
                        </h2>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <?php if ($recent_activity->num_rows > 0): ?>
                            <?php 
                            $recent_activity->data_seek(0); // Reset pointer
                            while ($activity = $recent_activity->fetch_assoc()): ?>
                                <div class="flex items-center space-x-4 p-3 bg-gradient-to-r from-gray-900/50 to-gray-800/50 rounded-xl hover:from-gray-800 hover:to-gray-700 transition-all duration-200 border border-gray-700/50 group">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                        <?php echo $activity['type'] === 'photo' ? 'bg-gradient-to-r from-blue-900/40 to-blue-800/40 text-blue-400' : 'bg-gradient-to-r from-purple-900/40 to-purple-800/40 text-purple-400'; ?>">
                                        <i class="fas fa-<?php echo $activity['type'] === 'photo' ? 'image' : 'folder'; ?>"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-white group-hover:text-blue-300 transition-colors"><?php echo htmlspecialchars($activity['title']); ?></p>
                                        <p class="text-xs text-gray-400">by <?php echo htmlspecialchars($activity['Username']); ?></p>
                                    </div>
                                    <div class="text-xs text-gray-500 bg-gray-900/50 px-2 py-1 rounded">
                                        <?php echo date('M j, g:i A', strtotime($activity['date'])); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-gray-900 to-gray-800 rounded-full flex items-center justify-center border border-gray-700">
                                    <i class="fas fa-inbox text-2xl text-gray-600"></i>
                                </div>
                                <p>No recent activity</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Photos -->
        <div class="mt-8 bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-700">
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-6 py-4 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-amber-600/20"></div>
                </div>
                <div class="relative z-10 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-yellow-900/40 to-yellow-800/40 flex items-center justify-center mr-3">
                            <i class="fas fa-images text-yellow-400"></i>
                        </div>
                        Recent Photos
                    </h2>
                    <a href="explore.php" class="text-gray-300 hover:text-yellow-300 transition-colors duration-200 text-sm flex items-center space-x-1">
                        <span>View All</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
            
            <div class="p-6">
                <?php if ($photos->num_rows > 0): ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <?php 
                        $photos->data_seek(0); // Reset pointer
                        while ($photo = $photos->fetch_assoc()): ?>
                            <div class="group relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl overflow-hidden hover:shadow-2xl transition-all duration-500 border border-gray-700 hover:border-blue-500/50">
                                <div class="aspect-square overflow-hidden">
                                    <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                                         alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                </div>
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end justify-center p-4">
                                    <div class="text-center text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                        <p class="text-sm font-semibold line-clamp-2 mb-2"><?php echo htmlspecialchars($photo['JudulFoto']); ?></p>
                                        <p class="text-xs text-gray-300 mb-3">by <?php echo htmlspecialchars($photo['Username']); ?></p>
                                        <div class="flex space-x-2 justify-center">
                                            <a href="detail.php?id=<?php echo $photo['FotoID']; ?>" 
                                               class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-3 py-1 rounded text-xs hover:from-blue-700 hover:to-blue-800 transition-all">
                                                View
                                            </a>
                                            <a href="../process/delete_photo.php?id=<?php echo $photo['FotoID']; ?>" 
                                               class="bg-gradient-to-r from-red-600 to-red-700 text-white px-3 py-1 rounded text-xs hover:from-red-700 hover:to-red-800 transition-all"
                                               onclick="return confirm('Are you sure you want to delete this photo?')">
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-gray-900 to-gray-800 rounded-full flex items-center justify-center border border-gray-700">
                            <i class="fas fa-images text-2xl text-gray-600"></i>
                        </div>
                        <p>No photos uploaded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    animation: fadeIn 0.6s ease-out;
}

.animate-fade-in {
    animation: fadeInUp 0.5s ease-out;
}

/* Line clamp utilities */
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Smooth transitions */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(10px)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
}

/* Custom scrollbar for dark theme */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #4f46e5, #7c3aed);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #6366f1, #8b5cf6);
}

/* Table responsive */
@media (max-width: 768px) {
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}

/* Aspect ratio for photos */
.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add confirmation for role changes
    const roleForms = document.querySelectorAll('form[method="POST"]');
    roleForms.forEach(form => {
        const select = form.querySelector('select[name="role"]');
        if (select) {
            const originalValue = select.value;
            
            select.addEventListener('change', function() {
                const newValue = this.value;
                const username = this.closest('tr').querySelector('.text-sm.font-semibold').textContent;
                
                if (confirm(`Are you sure you want to change ${username}'s role to ${newValue}?`)) {
                    form.submit();
                } else {
                    this.value = originalValue;
                }
            });
        }
    });
    
    // Add tooltips for action buttons
    const actionLinks = document.querySelectorAll('a[href*="delete"], a[href*="detail"]');
    actionLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>