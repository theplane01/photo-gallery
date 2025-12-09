<?php
$title = "Photo Detail";
include '../includes/header.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

if ($is_logged_in) {
    include '../includes/auth_check.php';
}

include '../includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: explore.php");
    exit();
}

$photo_id = $_GET['id'];
$photo = getPhotoById($photo_id);

if (!$photo) {
    header("Location: explore.php");
    exit();
}

// Tentukan halaman kembali berdasarkan referer atau parameter
$back_url = 'explore.php'; // default untuk pengunjung belum login
if ($is_logged_in) {
    $back_url = 'dashboard.php';
}

if (isset($_GET['from'])) {
    $back_url = $_GET['from'];
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'explore.php') !== false) {
        $back_url = 'explore.php';
    } elseif (strpos($referer, 'dashboard.php') !== false && $is_logged_in) {
        $back_url = 'dashboard.php';
    } elseif (strpos($referer, 'album.php') !== false) {
        $back_url = 'album.php';
    }
}

// Get initial like status and count
$like_data = [];
if ($is_logged_in) {
    $like_sql = "SELECT COUNT(*) as like_count FROM gallery_likefoto WHERE FotoID = ?";
    $like_stmt = $db->conn->prepare($like_sql);
    $like_stmt->bind_param("i", $photo_id);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result()->fetch_assoc();
    
    $user_like_sql = "SELECT * FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?";
    $user_like_stmt = $db->conn->prepare($user_like_sql);
    $user_like_stmt->bind_param("ii", $photo_id, $_SESSION['user_id']);
    $user_like_stmt->execute();
    $user_liked = $user_like_stmt->get_result()->num_rows > 0;
    
    $like_data = [
        'like_count' => $like_result['like_count'],
        'user_liked' => $user_liked
    ];
} else {
    // For non-logged users, just get like count
    $like_sql = "SELECT COUNT(*) as like_count FROM gallery_likefoto WHERE FotoID = ?";
    $like_stmt = $db->conn->prepare($like_sql);
    $like_stmt->bind_param("i", $photo_id);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result()->fetch_assoc();
    
    $like_data = [
        'like_count' => $like_result['like_count'],
        'user_liked' => false
    ];
}

// Get existing comments for this photo (WITH profile_pic)
$comments_sql = "SELECT k.*, u.Username, u.NamaLengkap, u.profile_pic 
                 FROM gallery_komentarfoto k 
                 JOIN gallery_user u ON k.UserID = u.UserID 
                 WHERE k.FotoID = ? 
                 ORDER BY k.TanggalKomentar DESC";
$comments_stmt = $db->conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $photo_id);
$comments_stmt->execute();
$existing_comments = $comments_stmt->get_result();
?>

<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="<?php echo $back_url; ?>" 
           class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white transition-colors duration-200 group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i>
            <span>Kembali ke <?php echo ($back_url == 'explore.php') ? 'Explore' : (($back_url == 'album.php') ? 'Album' : 'Dashboard'); ?></span>
        </a>
    </div>

    <!-- Enhanced Photo Detail Card -->
    <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 hover:shadow-3xl border border-gray-700">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-0">
            <!-- Enhanced Photo Section -->
            <div class="relative bg-gradient-to-br from-gray-900 to-black p-8">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>
                
                <!-- Photo Container -->
                <div class="relative z-10">
                    <div class="bg-gray-900 rounded-xl p-4 shadow-2xl border border-gray-700">
                        <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                             alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                             class="w-full h-auto max-h-96 object-cover rounded-lg shadow-lg transform transition-transform duration-500 hover:scale-105"
                             id="photoImage">
                    </div>
                    
                    <!-- Enhanced Action Buttons -->
                    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-3">
                            <!-- Enhanced Like Button -->
                            <button id="likeButton" 
                                    class="group relative flex items-center space-x-3 px-6 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1 <?php echo $like_data['user_liked'] ?? false ? 'bg-gradient-to-r from-red-600 to-pink-700 text-white shadow-lg shadow-red-700/30' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 border border-gray-700'; ?>"
                                    <?php if (!$is_logged_in): ?>onclick="showLoginAlert()"<?php endif; ?>>
                                <div class="relative">
                                    <i class="fas fa-heart text-lg <?php echo $like_data['user_liked'] ?? false ? 'text-white' : 'text-gray-400'; ?> group-hover:scale-110 transition-transform duration-300"></i>
                                    <?php if ($like_data['user_liked'] ?? false): ?>
                                        <div class="absolute inset-0 animate-ping opacity-75">
                                            <i class="fas fa-heart text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span id="likeCount" class="font-semibold"><?php echo $like_data['like_count'] ?? 0; ?></span>
                                <span id="likeText" class="hidden sm:inline"><?php echo $like_data['user_liked'] ?? false ? 'Liked' : 'Like'; ?></span>
                            </button>
                            
                            <!-- Enhanced Comment Toggle Button -->
                            <button id="commentToggle" 
                                    class="group flex items-center space-x-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-700 text-white rounded-xl hover:from-blue-700 hover:to-cyan-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-blue-700/30">
                                <i class="fas fa-comment text-lg group-hover:scale-110 transition-transform duration-300"></i>
                                <span class="font-semibold">Komentar</span>
                                <span id="commentCount" class="bg-gray-900 text-blue-400 px-2 py-1 rounded-full text-xs font-bold min-w-6">
                                    <?php echo $existing_comments->num_rows; ?>
                                </span>
                            </button>
                        </div>
                        
                        <!-- Enhanced Download Button -->
                        <a href="../process/download.php?id=<?php echo $photo['FotoID']; ?>" 
                           class="group flex items-center space-x-2 bg-gradient-to-r from-green-600 to-emerald-700 text-white px-6 py-3 rounded-xl hover:from-green-700 hover:to-emerald-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-green-700/30">
                            <i class="fas fa-download group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="font-semibold">Download</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Photo Details Section -->
            <div class="p-8 bg-gradient-to-br from-gray-800 to-gray-900">
                <!-- Photo Title -->
                <div class="mb-6">
                    <h1 class="text-4xl font-bold text-white mb-3" id="photoTitle">
                        <?php echo htmlspecialchars($photo['JudulFoto']); ?>
                    </h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                        <span class="flex items-center space-x-1">
                            <i class="fas fa-user-circle text-blue-400"></i>
                            <span>by <?php echo htmlspecialchars($photo['Username']); ?></span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <i class="fas fa-calendar text-purple-400"></i>
                            <span><?php echo date('F j, Y', strtotime($photo['TanggalUnggah'])); ?></span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <i class="fas fa-images text-green-400"></i>
                            <span><?php echo htmlspecialchars($photo['NamaAlbum'] ?? 'No Album'); ?></span>
                        </span>
                    </div>
                </div>
                
                <!-- Photo Description -->
                <div class="mb-8">
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-gray-300">Deskripsi Foto</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed bg-gray-900 p-4 rounded-xl border border-gray-700 shadow-sm" id="photoDescription">
                        <?php echo htmlspecialchars($photo['DeskripsiFoto']); ?>
                    </p>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/50 p-4 rounded-xl border border-blue-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-eye text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-white"><?php echo $photo['Views'] ?? '0'; ?></p>
                                <p class="text-sm text-gray-400">Views</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-900/50 to-green-800/50 p-4 rounded-xl border border-green-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-download text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-white"><?php echo $photo['Downloads'] ?? '0'; ?></p>
                                <p class="text-sm text-gray-400">Downloads</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                <?php if ($is_logged_in && ($_SESSION['user_id'] == $photo['UserID'] || isAdmin())): ?>
                    <div class="border-t border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-300 mb-4 flex items-center">
                            <i class="fas fa-cog text-gray-500 mr-2"></i>
                            Photo Management
                        </h3>
                        <div class="flex space-x-3">
                            <?php if ($_SESSION['user_id'] == $photo['UserID'] || isAdmin()): ?>
                                <a href="edit_photo.php?id=<?php echo $photo['FotoID']; ?>" 
                                   class="flex items-center space-x-2 bg-gradient-to-r from-yellow-600 to-amber-700 text-white px-5 py-2.5 rounded-xl hover:from-yellow-700 hover:to-amber-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit Photo</span>
                                </a>
                            <?php endif; ?>
                            <?php if (isAdmin()): ?>
                                <a href="../process/delete_photo.php?id=<?php echo $photo['FotoID']; ?>" 
                                   class="flex items-center space-x-2 bg-gradient-to-r from-red-600 to-rose-700 text-white px-5 py-2.5 rounded-xl hover:from-red-700 hover:to-rose-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md"
                                   onclick="return confirm('Are you sure you want to delete this photo?')">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete Photo</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enhanced Comments Section -->
    <div id="commentsSection" class="mt-8 bg-gray-800 rounded-2xl shadow-2xl overflow-hidden transition-all duration-500 border border-gray-700">
        <!-- Comments Header -->
        <div class="bg-gradient-to-r from-blue-800 to-purple-900 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                        <i class="fas fa-comments text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Komentar</h2>
                        <p class="text-blue-200">Bagikan pendapat Anda tentang foto ini</p>
                    </div>
                </div>
                <div id="commentsCountBadge" class="bg-gray-900 text-blue-400 px-4 py-2 rounded-xl font-bold text-lg shadow-lg">
                    <?php echo $existing_comments->num_rows; ?>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if ($is_logged_in): ?>
                <!-- Enhanced Comment Form -->
                <div class="mb-8 p-6 bg-gradient-to-br from-gray-800/80 to-blue-900/30 rounded-2xl border border-blue-800 shadow-sm">
                    <form id="commentForm" class="space-y-4">
                        <input type="hidden" name="foto_id" value="<?php echo $photo_id; ?>">
                        <div>
                            <label for="commentInput" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                                <i class="fas fa-edit text-blue-400 mr-2"></i>
                                Tambah Komentar
                            </label>
                            <div class="relative">
                                <textarea id="commentInput" name="komentar" rows="4" 
                                          placeholder="Bagikan pendapat Anda tentang foto ini..."
                                          class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent resize-none transition-all duration-300 text-white placeholder-gray-500"></textarea>
                                <div class="absolute bottom-3 right-3 text-sm text-gray-500">
                                    <span id="charCount">0</span>/500
                                </div>
                            </div>
                        </div>
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none font-semibold flex items-center space-x-2"
                                id="submitComment">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Komentar</span>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Login Prompt untuk Comment -->
                <div class="mb-8 p-6 bg-gradient-to-br from-gray-800/80 to-blue-900/30 rounded-2xl border border-blue-800 shadow-sm">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Login untuk Berkomentar</h3>
                        <p class="text-gray-400 mb-4">Bergabung dengan diskusi dan bagikan pendapat Anda</p>
                        <a href="login.php?redirect=detail.php?id=<?php echo $photo_id; ?>" 
                           class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 font-semibold inline-flex items-center space-x-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login Sekarang</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Enhanced Comments List -->
            <div class="space-y-6">
                <div id="commentsList" class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    <!-- Load existing comments from PHP -->
                    <?php if ($existing_comments->num_rows > 0): ?>
                        <?php while ($comment = $existing_comments->fetch_assoc()): ?>
                            <div class="bg-gray-900 p-5 rounded-xl border border-gray-700 comment-item shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <?php if (!empty($comment['profile_pic'])): ?>
                                                <img src="../uploads/profiles/<?php echo htmlspecialchars($comment['profile_pic']); ?>" 
                                                     alt="<?php echo htmlspecialchars($comment['Username']); ?>"
                                                     class="w-12 h-12 rounded-xl object-cover shadow-md border-2 border-blue-600">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-700 rounded-xl flex items-center justify-center shadow-md">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($comment['profile_pic']) && !empty($comment['profile_pic'])): ?>
                                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-600 rounded-full border-2 border-gray-900 flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="font-bold text-white block">
                                                <?php echo htmlspecialchars($comment['NamaLengkap'] ?: $comment['Username']); ?>
                                            </span>
                                            <span class="text-gray-400 text-sm">
                                                @<?php echo htmlspecialchars($comment['Username']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-400 bg-gray-800 px-3 py-1 rounded-full">
                                        <?php 
                                        $comment_date = new DateTime($comment['TanggalKomentar']);
                                        echo $comment_date->format('M j, Y \a\t g:i A');
                                        ?>
                                    </span>
                                </div>
                                <p class="text-gray-300 ml-14 leading-relaxed"><?php echo htmlspecialchars($comment['IsiKomentar']); ?></p>
                                
                                <!-- Comment Actions -->
                                <div class="flex items-center space-x-4 mt-3 ml-14">
                                    <button class="text-gray-500 hover:text-red-400 transition-colors duration-200 text-sm flex items-center space-x-1">
                                        <i class="far fa-heart"></i>
                                        <span>Suka</span>
                                    </button>
                                    <button class="text-gray-500 hover:text-blue-400 transition-colors duration-200 text-sm flex items-center space-x-1">
                                        <i class="far fa-comment"></i>
                                        <span>Balas</span>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div id="noComments" class="text-center py-12 bg-gradient-to-br from-gray-800/50 to-gray-900/30 rounded-2xl border-2 border-dashed border-gray-700">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-comment-slash text-gray-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-400 mb-2">Belum ada komentar</h3>
                            <p class="text-gray-500 max-w-md mx-auto">Jadilah yang pertama berkomentar dan bagikan pendapat Anda tentang foto ini!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="commentsLoading" class="text-center py-8 hidden">
                    <div class="inline-flex items-center space-x-3 bg-blue-900/30 px-6 py-3 rounded-xl">
                        <i class="fas fa-spinner fa-spin text-blue-400 text-xl"></i>
                        <p class="text-blue-400 font-medium">Memuat komentar...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript untuk Like dan Komentar Realtime -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoId = <?php echo $photo_id; ?>;
    const likeButton = document.getElementById('likeButton');
    const likeCount = document.getElementById('likeCount');
    const likeText = document.getElementById('likeText');
    const commentToggle = document.getElementById('commentToggle');
    const commentsSection = document.getElementById('commentsSection');
    const commentForm = document.getElementById('commentForm');
    const commentInput = document.getElementById('commentInput');
    const submitComment = document.getElementById('submitComment');
    const commentsList = document.getElementById('commentsList');
    const commentsLoading = document.getElementById('commentsLoading');
    const noComments = document.getElementById('noComments');
    const commentCount = document.getElementById('commentCount');
    const commentsCountBadge = document.getElementById('commentsCountBadge');
    const charCount = document.getElementById('charCount');

    // Current like state
    let isLiked = <?php echo $like_data['user_liked'] ? 'true' : 'false'; ?>;
    let currentLikeCount = <?php echo $like_data['like_count']; ?>;

    // Character counter for comment input
    commentInput.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        if (count > 500) {
            charCount.classList.add('text-red-400', 'font-bold');
        } else {
            charCount.classList.remove('text-red-400', 'font-bold');
        }
    });

    // Comment toggle functionality
    commentToggle.addEventListener('click', function() {
        commentsSection.classList.toggle('hidden');
        commentToggle.classList.toggle('active');
    });

    // Enhanced Like functionality
    likeButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!<?php echo $is_logged_in ? 'true' : 'false'; ?>) {
            showNotification('Silakan login untuk menyukai foto', 'warning');
            return;
        }

        // Add click animation
        likeButton.classList.add('scale-95');
        setTimeout(() => {
            likeButton.classList.remove('scale-95');
        }, 150);

        // Prepare form data
        const formData = new FormData();
        formData.append('foto_id', photoId);
        formData.append('action', isLiked ? 'unlike' : 'like');

        // Update UI immediately for better UX
        updateLikeUI(!isLiked);

        // Send AJAX request
        fetch('../process/like_process.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Like response:', data);
            
            if (data.success) {
                if (data.action === 'liked') {
                    showNotification('Foto berhasil disukai!', 'success');
                } else if (data.action === 'unliked') {
                    showNotification('Like berhasil dihapus', 'info');
                }
            } else {
                // Revert UI if failed
                updateLikeUI(isLiked);
                showNotification(data.message || 'Gagal menyukai foto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert UI on error
            updateLikeUI(isLiked);
            showNotification('Terjadi kesalahan', 'error');
        });
    });

    // Function to update like UI
    function updateLikeUI(liked) {
        isLiked = liked;
        
        if (isLiked) {
            // Change to liked state
            likeButton.classList.remove('bg-gray-800', 'text-gray-300', 'hover:bg-gray-700', 'border-gray-700');
            likeButton.classList.add('bg-gradient-to-r', 'from-red-600', 'to-pink-700', 'text-white', 'shadow-lg', 'shadow-red-700/30');
            likeButton.querySelector('i').classList.remove('text-gray-400');
            likeButton.querySelector('i').classList.add('text-white');
            likeText.textContent = 'Liked';
            
            // Update count (add 1)
            currentLikeCount = parseInt(currentLikeCount) + 1;
            likeCount.textContent = currentLikeCount;
            
            // Add heart burst animation
            createHeartAnimation();
        } else {
            // Change to unliked state
            likeButton.classList.remove('bg-gradient-to-r', 'from-red-600', 'to-pink-700', 'text-white', 'shadow-lg', 'shadow-red-700/30');
            likeButton.classList.add('bg-gray-800', 'text-gray-300', 'hover:bg-gray-700', 'border', 'border-gray-700');
            likeButton.querySelector('i').classList.remove('text-white');
            likeButton.querySelector('i').classList.add('text-gray-400');
            likeText.textContent = 'Like';
            
            // Update count (subtract 1, but not below 0)
            currentLikeCount = Math.max(0, parseInt(currentLikeCount) - 1);
            likeCount.textContent = currentLikeCount;
        }
    }

    // Enhanced comment form submission
    <?php if ($is_logged_in): ?>
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const commentText = commentInput.value.trim();
        if (!commentText) {
            showNotification('Komentar tidak boleh kosong', 'error');
            return;
        }

        if (commentText.length > 500) {
            showNotification('Komentar terlalu panjang (maksimal 500 karakter)', 'error');
            return;
        }

        submitComment.disabled = true;
        submitComment.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

        const formData = new FormData();
        formData.append('foto_id', photoId);
        formData.append('komentar', commentText);

        fetch('../process/comment_process.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                commentInput.value = '';
                charCount.textContent = '0';
                addCommentToDOM(data.comment);
                updateCommentCount(1);
                showNotification('Komentar berhasil ditambahkan', 'success');
                
                // Hide no comments message if it's shown
                if (noComments) {
                    noComments.style.display = 'none';
                }
                
                // Add success animation to comment form
                commentForm.classList.add('animate-pulse');
                setTimeout(() => {
                    commentForm.classList.remove('animate-pulse');
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding comment', 'error');
        })
        .finally(() => {
            submitComment.disabled = false;
            submitComment.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Komentar';
        });
    });
    <?php endif; ?>

    // Update comment count
    function updateCommentCount(change) {
        const currentCount = parseInt(commentCount.textContent) || 0;
        const newCount = currentCount + change;
        
        commentCount.textContent = newCount;
        commentsCountBadge.textContent = newCount;
        
        // Add count animation
        commentCount.classList.add('scale-125');
        commentsCountBadge.classList.add('scale-110');
        setTimeout(() => {
            commentCount.classList.remove('scale-125');
            commentsCountBadge.classList.remove('scale-110');
        }, 300);
    }

    // Enhanced add comment to DOM with profile picture
    function addCommentToDOM(comment) {
        const commentElement = document.createElement('div');
        commentElement.className = 'bg-gray-900 p-5 rounded-xl border border-gray-700 comment-item shadow-sm hover:shadow-md transition-all duration-300 transform -translate-y-2 opacity-0';
        
        const commentDate = new Date(comment.tanggal);
        const formattedDate = commentDate.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // Create avatar HTML based on profile_pic availability
        const avatarHTML = comment.profile_pic 
            ? `<img src="../uploads/profiles/${comment.profile_pic}" 
                     alt="${comment.username}"
                     class="w-12 h-12 rounded-xl object-cover shadow-md border-2 border-blue-600">`
            : `<div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-700 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-user text-white"></i>
               </div>`;

        commentElement.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        ${avatarHTML}
                        ${comment.profile_pic ? `
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-600 rounded-full border-2 border-gray-900 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>` : ''}
                    </div>
                    <div>
                        <span class="font-bold text-white block">${comment.nama_lengkap || comment.username}</span>
                        <span class="text-gray-400 text-sm">@${comment.username}</span>
                    </div>
                </div>
                <span class="text-sm text-gray-400 bg-gray-800 px-3 py-1 rounded-full">${formattedDate}</span>
            </div>
            <p class="text-gray-300 ml-14 leading-relaxed">${escapeHtml(comment.komentar)}</p>
            <div class="flex items-center space-x-4 mt-3 ml-14">
                <button class="text-gray-500 hover:text-red-400 transition-colors duration-200 text-sm flex items-center space-x-1 like-comment-btn" data-comment-id="${comment.id}">
                    <i class="far fa-heart"></i>
                    <span>Suka</span>
                </button>
                <button class="text-gray-500 hover:text-blue-400 transition-colors duration-200 text-sm flex items-center space-x-1 reply-comment-btn" data-comment-id="${comment.id}">
                    <i class="far fa-comment"></i>
                    <span>Balas</span>
                </button>
            </div>
        `;
        
        // Add new comment at the top of the list
        const firstComment = commentsList.querySelector('.comment-item');
        if (firstComment) {
            commentsList.insertBefore(commentElement, firstComment);
        } else {
            commentsList.appendChild(commentElement);
        }
        
        // Animate in
        setTimeout(() => {
            commentElement.classList.remove('-translate-y-2', 'opacity-0');
            commentElement.classList.add('translate-y-0', 'opacity-100');
        }, 50);
    }

    // Create heart animation for like
    function createHeartAnimation() {
        const heart = document.createElement('div');
        heart.innerHTML = '<i class="fas fa-heart text-red-500"></i>';
        heart.className = 'absolute text-2xl animate-burst';
        heart.style.left = '50%';
        heart.style.top = '50%';
        heart.style.transform = 'translate(-50%, -50%)';
        heart.style.zIndex = '100';
        heart.style.pointerEvents = 'none';
        
        likeButton.appendChild(heart);
        
        setTimeout(() => {
            heart.remove();
        }, 1000);
    }

    // Utility functions
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showNotification(message, type) {
        // Remove existing notification
        const existingNotification = document.querySelector('.custom-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        const typeConfig = {
            success: { bg: 'bg-gradient-to-r from-green-600 to-emerald-700', icon: 'fa-check-circle' },
            error: { bg: 'bg-gradient-to-r from-red-600 to-rose-700', icon: 'fa-exclamation-circle' },
            warning: { bg: 'bg-gradient-to-r from-yellow-600 to-amber-700', icon: 'fa-exclamation-triangle' },
            info: { bg: 'bg-gradient-to-r from-blue-600 to-cyan-700', icon: 'fa-info-circle' }
        };
        
        const config = typeConfig[type] || typeConfig.success;
        
        // Add margin-top to appear below sticky navbar
        notification.className = `custom-notification fixed top-24 right-6 px-6 py-4 rounded-xl shadow-2xl z-[9999] transform transition-all duration-500 translate-x-full ${config.bg} text-white flex items-center space-x-3`;
        notification.innerHTML = `
            <i class="fas ${config.icon} text-xl"></i>
            <span class="font-semibold">${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 4 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 4000);
    }
});

function showLoginAlert() {
    showNotification('Silakan login untuk melakukan tindakan ini', 'warning');
}
</script>

<style>
/* Custom animations */
@keyframes burst {
    0% { transform: translate(-50%, -50%) scale(0); opacity: 1; }
    50% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.7; }
    100% { transform: translate(-50%, -50%) scale(2); opacity: 0; }
}

.animate-burst {
    animation: burst 1s ease-out forwards;
}

/* Custom scrollbar for comments */
#commentsList::-webkit-scrollbar {
    width: 8px;
}

#commentsList::-webkit-scrollbar-track {
    background: #374151;
    border-radius: 4px;
}

#commentsList::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #7c3aed, #4f46e5);
    border-radius: 4px;
}

#commentsList::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #8b5cf6, #6366f1);
}

/* Smooth transitions */
.comment-item {
    transition: all 0.3s ease;
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
}

/* Custom notification animation */
.custom-notification {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    margin-top: 80px; /* Space below sticky navbar */
}

/* Gradient text effect */
.gradient-text {
    background: linear-gradient(to right, #8b5cf6, #6366f1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Enhanced focus states */
input:focus, textarea:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Scale animations */
.scale-125 {
    transform: scale(1.25);
}

.scale-110 {
    transform: scale(1.1);
}

/* Fix for sticky navbar overlapping */
.sticky-nav {
    z-index: 100;
}

.custom-notification {
    z-index: 9999 !important; /* Higher than navbar */
    position: fixed !important;
}

/* Make sure content doesn't get hidden behind navbar */
main {
    position: relative;
    z-index: 1;
}

/* Avatar styling for comments */
.comment-avatar-default {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

/* Profile picture styling */
.profile-pic-avatar {
    object-fit: cover;
    background-color: #374151;
}

/* Online status indicator */
.online-indicator {
    box-shadow: 0 0 0 2px #1f2937;
}

/* Animation for new comments */
@keyframes slideInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.comment-slide-in {
    animation: slideInUp 0.3s ease-out forwards;
}
</style>

<?php include '../includes/footer.php'; ?>