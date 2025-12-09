<?php
$title = "My Profile";
include '../includes/header.php';
include '../includes/auth_check.php';

require_once '../config/database.php';
$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$sql = "SELECT * FROM gallery_user WHERE UserID = ?";
$stmt = $db->conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Hitung statistik sederhana
$photo_sql = "SELECT COUNT(*) as total_photos FROM gallery_foto WHERE UserID = ?";
$photo_stmt = $db->conn->prepare($photo_sql);
$photo_stmt->bind_param("i", $user_id);
$photo_stmt->execute();
$photo_result = $photo_stmt->get_result();
$photo_stats = $photo_result->fetch_assoc();

$album_sql = "SELECT COUNT(*) as total_albums FROM gallery_album WHERE UserID = ?";
$album_stmt = $db->conn->prepare($album_sql);
$album_stmt->bind_param("i", $user_id);
$album_stmt->execute();
$album_result = $album_stmt->get_result();
$album_stats = $album_result->fetch_assoc();

// Query untuk jumlah likes - dengan error handling
$like_stats = ['total_likes' => 0];
try {
    // Coba query untuk likes
    $like_sql = "SELECT COUNT(*) as total_likes FROM gallery_like_foto WHERE UserID = ?";
    $like_stmt = $db->conn->prepare($like_sql);
    if ($like_stmt) {
        $like_stmt->bind_param("i", $user_id);
        $like_stmt->execute();
        $like_result = $like_stmt->get_result();
        $like_stats = $like_result->fetch_assoc();
    }
} catch (Exception $e) {
    // Jika tabel tidak ada, gunakan nilai default 0
    $like_stats = ['total_likes' => 0];
}

// Query untuk jumlah komentar - dengan error handling
$comment_stats = ['total_comments' => 0];
try {
    // Coba query untuk komentar
    $comment_sql = "SELECT COUNT(*) as total_comments FROM gallery_komentar_foto WHERE UserID = ?";
    $comment_stmt = $db->conn->prepare($comment_sql);
    if ($comment_stmt) {
        $comment_stmt->bind_param("i", $user_id);
        $comment_stmt->execute();
        $comment_result = $comment_stmt->get_result();
        $comment_stats = $comment_result->fetch_assoc();
    }
} catch (Exception $e) {
    // Coba dengan tabel alternatif jika tabel pertama tidak ada
    try {
        // Coba dengan nama tabel yang mungkin berbeda
        $comment_sql2 = "SELECT COUNT(*) as total_comments FROM gallery_komentar WHERE UserID = ?";
        $comment_stmt2 = $db->conn->prepare($comment_sql2);
        if ($comment_stmt2) {
            $comment_stmt2->bind_param("i", $user_id);
            $comment_stmt2->execute();
            $comment_result2 = $comment_stmt2->get_result();
            $comment_stats = $comment_result2->fetch_assoc();
        }
    } catch (Exception $e2) {
        // Jika kedua tabel tidak ada, gunakan nilai default 0
        $comment_stats = ['total_comments' => 0];
    }
}

// Atau jika Anda tidak yakin nama tabel yang benar, gunakan default saja
// $like_stats = ['total_likes' => 0];
// $comment_stats = ['total_comments' => 0];
?>

<div class="min-h-screen bg-gray-900 text-gray-100 py-8 transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="../index.php" class="text-sm text-gray-400 hover:text-blue-400 flex items-center transition-colors">
                        <i class="fas fa-home mr-2"></i> Home
                    </a>
                </li>
                <li>
                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                    <a href="dashboard.php" class="text-sm text-gray-400 hover:text-blue-400 transition-colors">Dashboard</a>
                </li>
                <li>
                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                    <span class="text-sm text-blue-400 font-medium">My Profile</span>
                </li>
            </ol>
        </nav>

        <!-- Profile Card -->
        <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden mb-8 border border-gray-700">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 p-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-center md:items-end space-y-6 md:space-y-0 md:space-x-8 relative z-10">
                    <!-- Profile Picture -->
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full border-4 border-gray-700 bg-gray-900 flex items-center justify-center shadow-xl group-hover:border-blue-500 transition-all duration-300">
                            <?php if (!empty($user['profile_pic'])): ?>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                     alt="Profile" class="w-full h-full rounded-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user-circle text-6xl text-blue-500"></i>
                            <?php endif; ?>
                        </div>
                        <a href="edit_profile.php" class="absolute bottom-0 right-0 bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 shadow-lg hover:scale-110 transition-all duration-300">
                            <i class="fas fa-camera"></i>
                        </a>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="text-center md:text-left">
                        <h1 class="text-3xl font-bold text-white"><?php echo htmlspecialchars($user['NamaLengkap'] ?: $user['Username']); ?></h1>
                        <p class="text-blue-300 text-lg">@<?php echo htmlspecialchars($user['Username']); ?></p>
                        <div class="mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $user['Level'] === 'Admin' ? 'bg-gradient-to-r from-yellow-900/40 to-yellow-800/40 text-yellow-300 border border-yellow-700/50' : 'bg-gradient-to-r from-blue-900/40 to-blue-800/40 text-blue-300 border border-blue-700/50'; ?>">
                                <i class="fas <?php echo $user['Level'] === 'Admin' ? 'fa-crown mr-1' : 'fa-user mr-1'; ?>"></i>
                                <?php echo htmlspecialchars($user['Level']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Content -->
            <div class="p-8">
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-4 rounded-xl text-center border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:scale-105">
                        <div class="text-2xl font-bold text-blue-400"><?php echo $photo_stats['total_photos']; ?></div>
                        <div class="text-gray-400 text-sm">Photos</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-4 rounded-xl text-center border border-gray-700 hover:border-green-500/50 transition-all duration-300 hover:scale-105">
                        <div class="text-2xl font-bold text-green-400"><?php echo $album_stats['total_albums']; ?></div>
                        <div class="text-gray-400 text-sm">Albums</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-4 rounded-xl text-center border border-gray-700 hover:border-purple-500/50 transition-all duration-300 hover:scale-105">
                        <div class="text-2xl font-bold text-purple-400"><?php echo $like_stats['total_likes']; ?></div>
                        <div class="text-gray-400 text-sm">Likes</div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-4 rounded-xl text-center border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:scale-105">
                        <div class="text-2xl font-bold text-yellow-400"><?php echo $comment_stats['total_comments']; ?></div>
                        <div class="text-gray-400 text-sm">Comments</div>
                    </div>
                </div>
                
                <!-- Profile Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Personal Info -->
                    <div>
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-blue-900/50 to-blue-800/50 flex items-center justify-center mr-3">
                                <i class="fas fa-user-circle text-blue-400"></i>
                            </div>
                            Personal Information
                        </h2>
                        <div class="space-y-4">
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 hover:border-blue-700 transition-colors">
                                <label class="block text-sm text-gray-400">Username</label>
                                <p class="font-medium text-white">@<?php echo htmlspecialchars($user['Username']); ?></p>
                            </div>
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 hover:border-blue-700 transition-colors">
                                <label class="block text-sm text-gray-400">Email</label>
                                <p class="font-medium text-white"><?php echo htmlspecialchars($user['Email']); ?></p>
                            </div>
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 hover:border-blue-700 transition-colors">
                                <label class="block text-sm text-gray-400">Full Name</label>
                                <p class="font-medium text-white"><?php echo htmlspecialchars($user['NamaLengkap'] ?: 'Not set'); ?></p>
                            </div>
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 hover:border-blue-700 transition-colors">
                                <label class="block text-sm text-gray-400">Address</label>
                                <p class="font-medium text-white"><?php echo htmlspecialchars($user['Alamat'] ?: 'Not set'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bio -->
                    <div>
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-green-900/50 to-green-800/50 flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-green-400"></i>
                            </div>
                            About Me
                        </h2>
                        <div class="bg-gray-900/50 p-6 rounded-lg border border-gray-700 hover:border-green-700 transition-colors min-h-[150px]">
                            <?php if (!empty($user['Bio'])): ?>
                                <p class="text-gray-300"><?php echo htmlspecialchars($user['Bio']); ?></p>
                            <?php else: ?>
                                <p class="text-gray-500 italic">No bio added yet. <a href="edit_profile.php" class="text-blue-400 hover:text-blue-300 transition-colors">Add one now</a>.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-white mb-3">Account Info</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center bg-gray-900/30 p-3 rounded-lg border border-gray-700">
                                    <span class="text-gray-400">Member since:</span>
                                    <span class="font-medium text-white"><?php echo date('M Y', strtotime($user['UserID'] == 1 ? '2024-01-01' : '2024-01-01')); ?></span>
                                </div>
                                <div class="flex justify-between items-center bg-gray-900/30 p-3 rounded-lg border border-gray-700">
                                    <span class="text-gray-400">User ID:</span>
                                    <span class="font-medium text-blue-400">#<?php echo $user['UserID']; ?></span>
                                </div>
                                <div class="flex justify-between items-center bg-gray-900/30 p-3 rounded-lg border border-gray-700">
                                    <span class="text-gray-400">Account type:</span>
                                    <span class="font-medium <?php echo $user['Level'] === 'Admin' ? 'text-yellow-400' : 'text-blue-400'; ?>"><?php echo $user['Level']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-700">
                    <a href="edit_profile.php" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-edit mr-2"></i> Edit Profile
                    </a>
                    <a href="dashboard.php" class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 flex items-center border border-gray-600 hover:border-gray-500">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                    <?php if ($user['Level'] === 'Admin'): ?>
                        <a href="admin.php" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-cog mr-2"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Photos -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-purple-900/50 to-purple-800/50 flex items-center justify-center mr-3">
                        <i class="fas fa-images text-purple-400"></i>
                    </div>
                    My Recent Photos
                </h2>
                <a href="add_photo.php" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 flex items-center text-sm shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i> Add Photo
                </a>
            </div>
            
            <?php
            $recent_sql = "SELECT * FROM gallery_foto WHERE UserID = ? ORDER BY TanggalUnggah DESC LIMIT 6";
            $recent_stmt = $db->conn->prepare($recent_sql);
            $recent_stmt->bind_param("i", $user_id);
            $recent_stmt->execute();
            $recent_photos = $recent_stmt->get_result();
            ?>
            
            <?php if ($recent_photos->num_rows > 0): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <?php while ($photo = $recent_photos->fetch_assoc()): ?>
                        <a href="detail.php?id=<?php echo $photo['FotoID']; ?>" class="block group">
                            <div class="aspect-square rounded-xl overflow-hidden shadow-lg border border-gray-700 group-hover:border-blue-500 transition-all duration-300 group-hover:shadow-2xl relative">
                                <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                                     alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-gradient-to-t from-black/80 to-transparent">
                                    <p class="text-white text-sm font-medium truncate"><?php echo htmlspecialchars($photo['JudulFoto']); ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-900 to-gray-800 rounded-full flex items-center justify-center border border-gray-700">
                        <i class="fas fa-camera text-3xl text-gray-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">No Photos Yet</h3>
                    <p class="text-gray-400 mb-4">Start sharing your memories by uploading your first photo!</p>
                    <a href="add_photo.php" class="inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Your First Photo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Smooth transitions for dark mode elements */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
    
    /* Custom scrollbar for dark theme */
    ::-webkit-scrollbar {
        width: 10px;
    }
    
    ::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 5px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>

<?php include '../includes/footer.php'; ?>