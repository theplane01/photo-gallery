<?php
$title = "Explore Photos";
include '../includes/auth_check.php';
include '../includes/header.php';

require_once '../config/database.php';
$db = new Database();

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$album_filter = isset($_GET['album']) ? intval($_GET['album']) : 0;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'recent';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build base query
$base_sql = "SELECT f.*, u.Username, u.profile_pic, a.NamaAlbum,
            (SELECT COUNT(*) FROM gallery_likefoto WHERE FotoID = f.FotoID) as total_likes,
            (SELECT COUNT(*) FROM gallery_komentarfoto WHERE FotoID = f.FotoID) as total_comments
            FROM gallery_foto f 
            LEFT JOIN gallery_user u ON f.UserID = u.UserID 
            LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID 
            WHERE 1=1";

$params = [];
$types = "";

// Add search filter
if (!empty($search_query)) {
    $base_sql .= " AND (f.JudulFoto LIKE ? OR f.deskripsi_foto LIKE ? OR u.Username LIKE ?)";
    $search_term = "%{$search_query}%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $types .= "sss";
}

// Add album filter
if ($album_filter > 0) {
    $base_sql .= " AND f.AlbumID = ?";
    $params[] = $album_filter;
    $types .= "i";
}

// Add sorting
switch ($sort_by) {
    case 'popular':
        $base_sql .= " ORDER BY total_likes DESC";
        break;
    case 'oldest':
        $base_sql .= " ORDER BY f.TanggalUnggah ASC";
        break;
    case 'commented':
        $base_sql .= " ORDER BY total_comments DESC";
        break;
    default: // recent
        $base_sql .= " ORDER BY f.TanggalUnggah DESC";
        break;
}

// Get photos with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$photos_per_page = 24;
$offset = ($page - 1) * $photos_per_page;

$photos_sql = $base_sql . " LIMIT ? OFFSET ?";
$stmt = $db->conn->prepare($photos_sql);

// Bind parameters
if (!empty($params)) {
    $params[] = $photos_per_page;
    $params[] = $offset;
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $photos_per_page, $offset);
}

$stmt->execute();
$photos = $stmt->get_result();

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM gallery_foto f WHERE 1=1";
if (!empty($search_query)) {
    $count_sql .= " AND (f.JudulFoto LIKE ? OR f.deskripsi_foto LIKE ?)";
    $count_stmt = $db->conn->prepare($count_sql);
    $search_term = "%{$search_query}%";
    $count_stmt->bind_param("ss", $search_term, $search_term);
} else {
    $count_stmt = $db->conn->prepare($count_sql);
}
$count_stmt->execute();
$total_result = $count_stmt->get_result();
$total_photos = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_photos / $photos_per_page);

// Get trending photos (most liked in last 7 days)
$trending_sql = "SELECT f.*, u.Username, COUNT(l.LikeID) as like_count 
                 FROM gallery_foto f 
                 LEFT JOIN gallery_likefoto l ON f.FotoID = l.FotoID 
                 LEFT JOIN gallery_user u ON f.UserID = u.UserID 
                 WHERE f.TanggalUnggah >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY f.FotoID 
                 ORDER BY like_count DESC 
                 LIMIT 5";
$trending_photos = $db->conn->query($trending_sql);

// Get top albums
$albums_sql = "SELECT a.*, u.Username, COUNT(f.FotoID) as photo_count 
               FROM gallery_album a 
               LEFT JOIN gallery_foto f ON a.AlbumID = f.AlbumID 
               LEFT JOIN gallery_user u ON a.UserID = u.UserID 
               GROUP BY a.AlbumID 
               ORDER BY photo_count DESC, a.TanggalDibuat DESC 
               LIMIT 5";
$top_albums = $db->conn->query($albums_sql);
?>

<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100 transition-colors duration-300">
    <!-- Hero Section (lebih kecil) -->
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23ffffff" fill-opacity="0.4" fill-rule="evenodd"/%3E%3C/svg%3E'); background-size: 120px;"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">
                    <span class="gradient-text">Discover Amazing Photos</span>
                </h1>
                <p class="text-lg text-gray-300 mb-6 max-w-2xl mx-auto">
                    Explore thousands of beautiful photos shared by our creative community
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <form method="GET" action="explore.php" class="relative">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search_query); ?>"
                                   placeholder="Search photos by title, description, or username..." 
                                   class="w-full px-5 py-3 bg-gray-800/70 backdrop-blur-sm border border-gray-700 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-xl">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Sidebar - Filters -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <!-- Filters Card -->
                    <div class="bg-gray-800/70 backdrop-blur-sm rounded-xl p-5 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                            <i class="fas fa-filter text-blue-400 mr-2"></i>
                            Filters
                        </h3>
                        
                        <!-- Sort By -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Sort By</label>
                            <div class="space-y-2">
                                <a href="?sort=recent<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg <?php echo $sort_by == 'recent' ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'hover:bg-gray-700/50 text-gray-300'; ?> transition-colors">
                                    <span>Most Recent</span>
                                    <i class="fas fa-clock text-sm"></i>
                                </a>
                                <a href="?sort=popular<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg <?php echo $sort_by == 'popular' ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'hover:bg-gray-700/50 text-gray-300'; ?> transition-colors">
                                    <span>Most Popular</span>
                                    <i class="fas fa-fire text-sm"></i>
                                </a>
                                <a href="?sort=commented<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg <?php echo $sort_by == 'commented' ? 'bg-green-600/20 text-green-300 border border-green-500/30' : 'hover:bg-gray-700/50 text-gray-300'; ?> transition-colors">
                                    <span>Most Discussed</span>
                                    <i class="fas fa-comments text-sm"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="pt-4 border-t border-gray-700/50">
                            <h4 class="text-sm font-medium text-gray-300 mb-3">Quick Stats</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Total Photos</span>
                                    <span class="font-semibold text-white"><?php echo number_format($total_photos); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">This Page</span>
                                    <span class="font-semibold text-white"><?php echo min($photos_per_page, $photos->num_rows); ?></span>
                                </div>
                                <?php if (!empty($search_query)): ?>
                                <div class="flex justify-between items-center text-blue-400">
                                    <span>Search Results</span>
                                    <span class="font-semibold">"<?php echo htmlspecialchars($search_query); ?>"</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Popular Albums -->
                    <?php if ($top_albums && $top_albums->num_rows > 0): ?>
                    <div class="bg-gray-800/70 backdrop-blur-sm rounded-xl p-5 border border-gray-700 shadow-lg">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-2"></i>
                            Popular Albums
                        </h3>
                        <div class="space-y-4">
                            <?php while ($album = $top_albums->fetch_assoc()): ?>
                            <a href="album_detail.php?id=<?php echo $album['AlbumID']; ?>" class="group flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-700/50 transition-colors">
                                <div class="w-10 h-10 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center">
                                    <i class="fas fa-folder text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate"><?php echo htmlspecialchars($album['NamaAlbum']); ?></p>
                                    <p class="text-xs text-gray-400">by <?php echo htmlspecialchars($album['Username']); ?></p>
                                </div>
                                <div class="flex items-center space-x-1 text-blue-400 text-xs">
                                    <i class="fas fa-image"></i>
                                    <span><?php echo $album['photo_count'] ?? 0; ?></span>
                                </div>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Main Photo Grid -->
            <div class="lg:col-span-3">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 p-4 bg-gray-800/50 rounded-xl border border-gray-700/50">
                    <div>
                        <h2 class="text-xl font-bold text-white">
                            <?php if (!empty($search_query)): ?>
                                Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                            <?php else: ?>
                                All Photos
                            <?php endif; ?>
                        </h2>
                        <p class="text-gray-400 text-sm">
                            Showing <?php echo $photos->num_rows; ?> of <?php echo number_format($total_photos); ?> photos
                            <?php if ($sort_by != 'recent'): ?>
                                • Sorted by <?php echo ucfirst($sort_by); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Photo Grid -->
                <?php if ($photos->num_rows > 0): ?>
                    <!-- Grid View Only -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php while ($photo = $photos->fetch_assoc()): ?>
                            <?php
                            // Check if user already liked this photo
                            $already_liked = false;
                            if (isset($_SESSION['user_id'])) {
                                $like_check_sql = "SELECT * FROM gallery_likefoto WHERE FotoID = ? AND UserID = ?";
                                $like_check_stmt = $db->conn->prepare($like_check_sql);
                                $like_check_stmt->bind_param("ii", $photo['FotoID'], $_SESSION['user_id']);
                                $like_check_stmt->execute();
                                $already_liked = $like_check_stmt->get_result()->num_rows > 0;
                            }
                            ?>
                            <div class="group relative bg-gray-800/50 backdrop-blur-sm rounded-xl overflow-hidden border border-gray-700/50 hover:border-blue-500/50 transition-all duration-300 hover:shadow-xl">
                                <!-- Photo -->
                                <a href="detail.php?id=<?php echo $photo['FotoID']; ?>" class="block aspect-square overflow-hidden">
                                    <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                                         alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </a>
                                
                                <!-- Photo Info Overlay -->
                                <div class="absolute bottom-0 left-0 right-0 p-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-gradient-to-t from-black/90 to-transparent">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <?php if (!empty($photo['profile_pic'])): ?>
                                                <img src="../uploads/profiles/<?php echo htmlspecialchars($photo['profile_pic']); ?>" 
                                                     alt="<?php echo htmlspecialchars($photo['Username']); ?>"
                                                     class="w-6 h-6 rounded-full object-cover">
                                            <?php else: ?>
                                                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-white text-xs"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="text-sm font-medium text-white"><?php echo htmlspecialchars($photo['Username']); ?></span>
                                        </div>
                                        <span class="text-xs text-gray-300">
                                            <?php echo date('M d, Y', strtotime($photo['TanggalUnggah'])); ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-sm font-semibold text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($photo['JudulFoto']); ?></h3>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center space-x-4">
                                            <span class="flex items-center space-x-1 text-gray-300">
                                                <i class="fas fa-heart"></i>
                                                <span class="like-count-<?php echo $photo['FotoID']; ?>"><?php echo $photo['total_likes'] ?? 0; ?></span>
                                            </span>
                                            <span class="flex items-center space-x-1 text-gray-300">
                                                <i class="fas fa-comment"></i>
                                                <span><?php echo $photo['total_comments'] ?? 0; ?></span>
                                            </span>
                                        </div>
                                        <a href="detail.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="text-xs bg-gradient-to-r from-blue-600 to-blue-700 text-white px-3 py-1 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">
                                            View
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Like Button -->
<?php if (isset($_SESSION['user_id'])): ?>
<div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
    <form action="../process/like_process.php" method="POST" class="like-form" data-photo-id="<?php echo $photo['FotoID']; ?>" onsubmit="return false;">
        <input type="hidden" name="foto_id" value="<?php echo $photo['FotoID']; ?>">
        <input type="hidden" name="action" value="<?php echo $already_liked ? 'unlike' : 'like'; ?>">
        <button type="button" 
                class="w-10 h-10 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center hover:bg-red-600/80 transition-colors <?php echo $already_liked ? 'text-red-400' : 'text-white'; ?> like-btn-<?php echo $photo['FotoID']; ?>"
                onclick="toggleLike(<?php echo $photo['FotoID']; ?>, '<?php echo $already_liked ? 'true' : 'false'; ?>')">
            <i class="fas fa-heart text-sm"></i>
        </button>
    </form>
</div>
<?php endif; ?>
                                
                                <!-- Album Badge -->
                                <?php if (!empty($photo['NamaAlbum'])): ?>
                                    <div class="absolute top-3 left-3">
                                        <span class="text-xs bg-gradient-to-r from-purple-600/80 to-purple-700/80 backdrop-blur-sm text-white px-2 py-1 rounded-lg">
                                            <?php echo htmlspecialchars($photo['NamaAlbum']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                            <div class="text-sm text-gray-400">
                                Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $sort_by != 'recent' ? '&sort=' . $sort_by : ''; ?>" 
                                       class="px-4 py-2 bg-gray-800/50 border border-gray-700 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <i class="fas fa-chevron-left mr-2"></i> Previous
                                    </a>
                                <?php endif; ?>
                                
                                <div class="flex items-center space-x-1">
                                    <?php 
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    for ($i = $start_page; $i <= $end_page; $i++): 
                                    ?>
                                        <a href="?page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $sort_by != 'recent' ? '&sort=' . $sort_by : ''; ?>" 
                                           class="w-10 h-10 flex items-center justify-center rounded-lg <?php echo $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-800/50 border border-gray-700 hover:bg-gray-700/50'; ?> transition-colors">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?><?php echo $sort_by != 'recent' ? '&sort=' . $sort_by : ''; ?>" 
                                       class="px-4 py-2 bg-gray-800/50 border border-gray-700 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        Next <i class="fas fa-chevron-right ml-2"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center border border-gray-700">
                            <i class="fas fa-images text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">No Photos Found</h3>
                        <p class="text-gray-400 max-w-md mx-auto mb-6">
                            <?php if (!empty($search_query)): ?>
                                No photos match your search "<?php echo htmlspecialchars($search_query); ?>". Try different keywords.
                            <?php else: ?>
                                No photos have been uploaded yet. Be the first to share your photos!
                            <?php endif; ?>
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                            <?php if (!empty($search_query)): ?>
                                <a href="explore.php" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300">
                                    View All Photos
                                </a>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="add_photo.php" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-5 py-2.5 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-300">
                                    <i class="fas fa-plus mr-2"></i> Upload First Photo
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Line clamp utilities */
.line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; }
.line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }

/* Aspect ratios */
.aspect-square { aspect-ratio: 1 / 1; }

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Sticky positioning for sidebar */
.sticky {
    position: sticky;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
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

/* Smooth image zoom */
img {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(10px)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
}

/* Mobile responsive */
@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize like button states
    initializeLikeButtons();
});

function initializeLikeButtons() {
    // Set initial state for all like buttons
    document.querySelectorAll('[class^="like-btn-"]').forEach(button => {
        const photoId = button.className.match(/like-btn-(\d+)/)[1];
        // State already set by PHP
    });
}

async function toggleLike(photoId, isLiked) {
    // Convert string to boolean
    isLiked = (isLiked === 'true');
    
    console.log('Toggling like:', { photoId, isLiked });
    
    const likeBtn = document.querySelector(`.like-btn-${photoId}`);
    const likeCount = document.querySelector(`.like-count-${photoId}`);
    const form = document.querySelector(`.like-form[data-photo-id="${photoId}"]`);
    
    if (!likeBtn || !likeCount || !form) {
        console.error('Elements not found!');
        return;
    }
    
    // Show loading state
    const originalHtml = likeBtn.innerHTML;
    likeBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
    likeBtn.disabled = true;
    
    try {
        // Prepare form data
        const formData = new FormData(form);
        const action = isLiked ? 'unlike' : 'like';
        formData.set('action', action);
        
        console.log('Sending action:', action);
        
        // Send AJAX request
        const response = await fetch('../process/like_process.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        console.log('Server response:', data);
        
        if (data.success) {
            // Toggle button state
            if (data.action === 'liked') {
                // Update button to liked state
                likeBtn.innerHTML = '<i class="fas fa-heart text-sm text-red-400"></i>';
                likeBtn.classList.remove('text-white');
                likeBtn.classList.add('text-red-400');
                
                // Update form action for next click
                form.querySelector('input[name="action"]').value = 'unlike';
                
                // Update like count
                const currentCount = parseInt(likeCount.textContent) || 0;
                likeCount.textContent = currentCount + 1;
                
                // Update onclick for next click
                likeBtn.setAttribute('onclick', `toggleLike(${photoId}, 'true')`);
                
            } else if (data.action === 'unliked') {
                // Update button to unliked state
                likeBtn.innerHTML = '<i class="fas fa-heart text-sm text-white"></i>';
                likeBtn.classList.remove('text-red-400');
                likeBtn.classList.add('text-white');
                
                // Update form action for next click
                form.querySelector('input[name="action"]').value = 'like';
                
                // Update like count
                const currentCount = parseInt(likeCount.textContent) || 0;
                likeCount.textContent = Math.max(0, currentCount - 1);
                
                // Update onclick for next click
                likeBtn.setAttribute('onclick', `toggleLike(${photoId}, 'false')`);
            }
            
            console.log('Like state updated successfully');
        } else {
            console.error('Error from server:', data.message);
            // Revert button
            likeBtn.innerHTML = originalHtml;
            
            // Show error message
            if (data.message && data.message.includes('login')) {
                alert('Please login to like photos');
            } else if (data.message && data.message.includes('Already liked')) {
                // Force refresh the page if already liked error
                location.reload();
            }
        }
    } catch (error) {
        console.error('Fetch error:', error);
        likeBtn.innerHTML = originalHtml;
        alert('Network error. Please try again.');
    } finally {
        likeBtn.disabled = false;
    }
}
</script>

<?php include '../includes/footer.php'; ?>