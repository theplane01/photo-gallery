<?php
$title = "Album Detail";
include '../includes/header.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

if ($is_logged_in) {
    include '../includes/auth_check.php';
}

require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: explore.php");
    exit();
}

$album_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? null;

// Get album details
$album_sql = "SELECT a.*, u.Username as owner_username, u.NamaLengkap as owner_name
              FROM gallery_album a 
              LEFT JOIN gallery_user u ON a.UserID = u.UserID 
              WHERE a.AlbumID = ?";
$album_stmt = $db->conn->prepare($album_sql);
$album_stmt->bind_param("i", $album_id);
$album_stmt->execute();
$album = $album_stmt->get_result()->fetch_assoc();

if (!$album) {
    header("Location: explore.php?error=Album not found");
    exit();
}

// Check if there's a search query
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = "";
if (!empty($search_query)) {
    $search_condition = " AND f.JudulFoto LIKE ?";
}

// Get photos in this album with optional search
$photos_sql = "SELECT f.*, u.Username, u.NamaLengkap 
               FROM gallery_foto f 
               LEFT JOIN gallery_user u ON f.UserID = u.UserID 
               WHERE f.AlbumID = ? $search_condition
               ORDER BY f.TanggalUnggah DESC";
$photos_stmt = $db->conn->prepare($photos_sql);

if (!empty($search_query)) {
    $search_param = "%" . $search_query . "%";
    $photos_stmt->bind_param("is", $album_id, $search_param);
} else {
    $photos_stmt->bind_param("i", $album_id);
}

$photos_stmt->execute();
$photos = $photos_stmt->get_result();
$total_photos = $photos->num_rows;

// Get album stats
$stats_sql = "SELECT 
              COUNT(f.FotoID) as total_photos,
              SUM((SELECT COUNT(*) FROM gallery_likefoto WHERE FotoID = f.FotoID)) as total_likes,
              SUM((SELECT COUNT(*) FROM gallery_komentarfoto WHERE FotoID = f.FotoID)) as total_comments
              FROM gallery_foto f 
              WHERE f.AlbumID = ?";
$stats_stmt = $db->conn->prepare($stats_sql);
$stats_stmt->bind_param("i", $album_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
?>

<div class="min-h-screen bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <!-- Enhanced Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="../index.php" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-400 transition-colors duration-200">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-home text-white text-xs"></i>
                        </div>
                        Beranda
                    </a>
                </li>
                <li class="inline-flex items-center">
                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                    <a href="explore.php" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-400 transition-colors duration-200">
                        Explore
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                        <span class="ml-1 text-sm font-medium text-gray-300 md:ml-2"><?php echo htmlspecialchars($album['NamaAlbum']); ?></span>
                    </div>
                </li>
                <?php if (!empty($search_query)): ?>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                            <span class="ml-1 text-sm font-medium text-blue-400 md:ml-2">Search: "<?php echo htmlspecialchars($search_query); ?>"</span>
                        </div>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>

    <!-- Enhanced Album Header -->
    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl p-8 mb-8 text-white overflow-hidden relative border border-gray-700">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-folder text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold"><?php echo htmlspecialchars($album['NamaAlbum']); ?></h1>
                            <?php if (!empty($search_query)): ?>
                                <p class="text-blue-300 mt-2">
                                    <i class="fas fa-search mr-2"></i>
                                    Search results for: <span class="font-semibold">"<?php echo htmlspecialchars($search_query); ?>"</span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4 text-blue-200 mb-4">
                        <span class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span><?php echo htmlspecialchars($album['owner_name'] ?: $album['owner_username']); ?></span>
                        </span>
                        <span class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-sm"></i>
                            </div>
                            <span><?php echo date('F j, Y', strtotime($album['TanggalDibuat'])); ?></span>
                        </span>
                    </div>
                    
                    <?php if ($album['Deskripsi']): ?>
                        <p class="text-lg text-blue-100 leading-relaxed max-w-3xl"><?php echo htmlspecialchars($album['Deskripsi']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <a href="explore.php" 
                       class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                    <?php if ($is_logged_in && ($_SESSION['user_id'] == $album['UserID'] || isAdmin())): ?>
                        <a href="my_albums.php" 
                           class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2">
                            <i class="fas fa-edit"></i>
                            <span>Manage</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Album Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-700">
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['total_photos'] ?? 0; ?></div>
                    <div class="text-blue-200 text-sm">Total Photos</div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-700">
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $total_photos; ?></div>
                    <div class="text-blue-200 text-sm">
                        <?php echo !empty($search_query) ? 'Search Results' : 'Current Photos'; ?>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-700">
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['total_likes'] ?? 0; ?></div>
                    <div class="text-blue-200 text-sm">Total Likes</div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-700">
                    <div class="text-2xl font-bold text-white mb-1"><?php echo $stats['total_comments'] ?? 0; ?></div>
                    <div class="text-blue-200 text-sm">Total Comments</div>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white opacity-5 rounded-full"></div>
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-white opacity-5 rounded-full"></div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-gradient-to-r from-gray-800/80 to-gray-900/50 rounded-2xl shadow-lg p-6 mb-8 border border-gray-700">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <!-- Search Form -->
            <div class="flex-1 w-full">
                <form method="GET" action="" class="relative" id="searchForm">
                    <input type="hidden" name="id" value="<?php echo $album_id; ?>">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-blue-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               value="<?php echo htmlspecialchars($search_query); ?>"
                               placeholder="Search photos by title..." 
                               class="w-full pl-12 pr-12 py-3.5 bg-gray-800 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent shadow-sm transition-all duration-300">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                            <?php if (!empty($search_query)): ?>
                                <button type="button" 
                                        onclick="clearSearch()" 
                                        class="text-gray-400 hover:text-red-400 transition-colors duration-200 p-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-gray-400">
                                    <i class="fas fa-search"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Results Count and Actions -->
            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400"><?php echo $total_photos; ?></div>
                    <div class="text-sm text-gray-400">Photos found</div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <?php if (!empty($search_query)): ?>
                        <a href="album_detail.php?id=<?php echo $album_id; ?>" 
                           class="bg-gray-700 text-gray-300 px-4 py-2.5 rounded-lg hover:bg-gray-600 transition-all duration-300 font-medium flex items-center space-x-2">
                            <i class="fas fa-times"></i>
                            <span>Clear Search</span>
                        </a>
                    <?php endif; ?>
                    
                    <button type="submit" 
                            form="searchForm" 
                            class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-2.5 rounded-lg hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 shadow-md">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Quick Search Tips -->
        <?php if (empty($search_query)): ?>
            <div class="mt-4 text-sm text-gray-400">
                <p class="flex items-center">
                    <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                    <span>Tip: Search for photos by their title. Try keywords related to what you're looking for.</span>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Photos Grid -->
    <?php if ($total_photos > 0): ?>
        <!-- Grid Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-images text-blue-400 mr-3"></i>
                    <?php echo !empty($search_query) ? 'Search Results' : 'Photos in Album'; ?>
                    <span class="ml-3 bg-blue-900/50 text-blue-400 px-3 py-1 rounded-full text-sm font-semibold border border-blue-800">
                        <?php echo $total_photos; ?> photo<?php echo $total_photos !== 1 ? 's' : ''; ?>
                    </span>
                </h2>
                <?php if (!empty($search_query)): ?>
                    <p class="text-gray-400 mt-2">
                        Showing results for: <span class="font-semibold text-blue-400">"<?php echo htmlspecialchars($search_query); ?>"</span>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Sort Options -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-400 hidden md:inline">Sort by:</span>
                <select id="sortSelect" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 min-w-[150px] text-white">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="title_asc">Title (A-Z)</option>
                    <option value="title_desc">Title (Z-A)</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>
        </div>

        <!-- Enhanced Photos Grid -->
        <div id="photosGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php 
            $photo_counter = 0;
            while ($photo = $photos->fetch_assoc()): 
                $photo_counter++;
                
                // Get like count for this photo
                $like_sql = "SELECT COUNT(*) as like_count FROM gallery_likefoto WHERE FotoID = ?";
                $like_stmt = $db->conn->prepare($like_sql);
                $like_stmt->bind_param("i", $photo['FotoID']);
                $like_stmt->execute();
                $like_count = $like_stmt->get_result()->fetch_assoc()['like_count'];
                
                // Get comment count
                $comment_sql = "SELECT COUNT(*) as comment_count FROM gallery_komentarfoto WHERE FotoID = ?";
                $comment_stmt = $db->conn->prepare($comment_sql);
                $comment_stmt->bind_param("i", $photo['FotoID']);
                $comment_stmt->execute();
                $comment_count = $comment_stmt->get_result()->fetch_assoc()['comment_count'];
            ?>
                
                <div class="photo-card bg-gray-800/40 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 group border border-gray-700" 
                     data-id="<?php echo $photo['FotoID']; ?>"
                     data-title="<?php echo htmlspecialchars(strtolower($photo['JudulFoto'])); ?>"
                     data-date="<?php echo strtotime($photo['TanggalUnggah']); ?>"
                     data-likes="<?php echo $like_count; ?>"
                     data-popularity="<?php echo $like_count + $comment_count; ?>">
                    <!-- Photo Container -->
                    <div class="relative overflow-hidden">
                        <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                             alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                             class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300/1f2937/9ca3af?text=Photo+Not+Found';">
                        
                        <!-- Overlay on Hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-4">
                            <div class="text-white transform translate-y-4 group-hover:translate-y-0 transition duration-500">
                                <h3 class="font-bold text-lg mb-2 line-clamp-2">
                                    <?php echo htmlspecialchars($photo['JudulFoto']); ?>
                                </h3>
                                <div class="flex justify-between text-sm mb-3">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-heart text-red-400"></i>
                                        <span><?php echo $like_count; ?></span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-comment text-blue-300"></i>
                                        <span><?php echo $comment_count; ?></span>
                                    </span>
                                </div>
                                <div class="flex justify-between space-x-2">
                                    <a href="detail.php?id=<?php echo $photo['FotoID']; ?>&from=album_detail&album_id=<?php echo $album_id; ?>" 
                                       class="bg-white bg-opacity-20 backdrop-blur-sm text-white px-3 py-2 rounded-lg text-sm hover:bg-opacity-30 transition flex-1 text-center font-semibold flex items-center justify-center space-x-1">
                                        <i class="fas fa-expand"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="../process/download.php?id=<?php echo $photo['FotoID']; ?>" 
                                       class="bg-white bg-opacity-20 backdrop-blur-sm text-white px-3 py-2 rounded-lg text-sm hover:bg-opacity-30 transition flex-1 text-center font-semibold flex items-center justify-center space-x-1">
                                        <i class="fas fa-download"></i>
                                        <span>Download</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="absolute top-3 left-3 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm flex items-center space-x-2">
                            <i class="fas fa-calendar text-blue-300"></i>
                            <span><?php echo date('M j, Y', strtotime($photo['TanggalUnggah'])); ?></span>
                        </div>
                    </div>
                    
                    <!-- Photo Info -->
                    <div class="p-5">
                        <h3 class="font-bold text-white mb-2 line-clamp-1 group-hover:text-blue-400 transition-colors duration-200">
                            <?php echo htmlspecialchars($photo['JudulFoto']); ?>
                        </h3>
                        
                        <?php if ($photo['DeskripsiFoto']): ?>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2 leading-relaxed">
                                <?php echo htmlspecialchars($photo['DeskripsiFoto']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Photo Stats -->
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <div class="flex items-center space-x-3">
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-heart text-red-400"></i>
                                    <span class="font-semibold"><?php echo $like_count; ?></span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-comment text-blue-400"></i>
                                    <span class="font-semibold"><?php echo $comment_count; ?></span>
                                </span>
                            </div>
                            <span class="flex items-center space-x-1 text-xs">
                                <i class="fas fa-user text-gray-500"></i>
                                <span><?php echo htmlspecialchars($photo['NamaLengkap'] ?: $photo['Username']); ?></span>
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                            <a href="detail.php?id=<?php echo $photo['FotoID']; ?>&from=album_detail&album_id=<?php echo $album_id; ?>" 
                               class="text-blue-400 hover:text-blue-300 font-semibold text-sm flex items-center space-x-2 transition-colors duration-200 group/view">
                                <span>View Details</span>
                                <i class="fas fa-chevron-right text-xs group-hover/view:translate-x-1 transition-transform duration-200"></i>
                            </a>
                            <div class="flex space-x-3">
                                <a href="../process/download.php?id=<?php echo $photo['FotoID']; ?>" 
                                   class="text-green-400 hover:text-green-300 transition-colors duration-200 transform hover:scale-110" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php if ($is_logged_in && ($_SESSION['user_id'] == $photo['UserID'] || isAdmin())): ?>
                                    <a href="edit_photo.php?id=<?php echo $photo['FotoID']; ?>" 
                                       class="text-yellow-400 hover:text-yellow-300 transition-colors duration-200 transform hover:scale-110" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <!-- No Search Results Message (hidden by default) -->
        <div id="noResultsMessage" class="hidden text-center py-16 bg-gradient-to-br from-gray-800/50 to-gray-900/30 rounded-2xl shadow-lg border-2 border-dashed border-gray-700">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">No Results Found</h3>
                <p class="text-gray-400 mb-6">We couldn't find any photos matching your search criteria.</p>
                <button onclick="clearSearch()" 
                       class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg inline-flex items-center space-x-2 font-semibold">
                    <i class="fas fa-times"></i>
                    <span>Clear Search</span>
                </button>
            </div>
        </div>
    <?php else: ?>
        <!-- Enhanced No Photos State -->
        <div class="text-center py-16 bg-gradient-to-br from-gray-800/50 to-gray-900/30 rounded-2xl shadow-lg border-2 border-dashed border-gray-700">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-600 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-images text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">
                    <?php echo !empty($search_query) ? 'No Search Results' : 'No Photos Yet'; ?>
                </h3>
                <p class="text-gray-400 mb-6">
                    <?php if (!empty($search_query)): ?>
                        No photos found matching: <span class="font-semibold text-blue-400">"<?php echo htmlspecialchars($search_query); ?>"</span>
                    <?php else: ?>
                        This album doesn't contain any photos yet. Be the first to add one!
                    <?php endif; ?>
                </p>
                <?php if ($is_logged_in && $_SESSION['user_id'] == $album['UserID'] && empty($search_query)): ?>
                    <a href="add_photo.php?album_id=<?php echo $album_id; ?>" 
                       class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg inline-flex items-center space-x-2 font-semibold">
                        <i class="fas fa-plus"></i>
                        <span>Add First Photo</span>
                    </a>
                <?php elseif (!empty($search_query)): ?>
                    <button onclick="clearSearch()" 
                           class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg inline-flex items-center space-x-2 font-semibold">
                        <i class="fas fa-times"></i>
                        <span>Clear Search</span>
                    </button>
                <?php else: ?>
                    <p class="text-gray-500 text-sm">Only the album owner can add photos to this album.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Custom animations */
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

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}

.photo-card {
    animation: fadeInUp 0.6s ease-out;
}

.photo-card.fade-out {
    animation: fadeOut 0.3s ease-out forwards;
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

/* Search highlight */
.highlight {
    background-color: rgba(255, 243, 205, 0.2);
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: 600;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(31, 41, 55, 0.5);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #4f46e5, #7c3aed);
}

/* Loading animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(243, 243, 243, 0.3);
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Search focus effect */
.search-focused {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
}

/* Image loading state */
img {
    transition: opacity 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to photo cards
    const photoCards = document.querySelectorAll('.photo-card');
    photoCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
    });

    // Add loading animation for images
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.classList.add('opacity-0');
        img.addEventListener('load', function() {
            this.classList.remove('opacity-0');
            this.classList.add('opacity-100', 'transition-opacity', 'duration-500');
        });
        
        // Handle image errors
        img.addEventListener('error', function() {
            console.error('Image failed to load:', this.src);
            this.onerror = null;
            this.src = 'https://via.placeholder.com/400x300/1f2937/9ca3af?text=Photo+Not+Found';
        });
    });

    // Auto-submit search form when typing (with debounce)
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            // Show loading indicator
            const searchButton = document.querySelector('button[form="searchForm"]');
            if (searchButton) {
                const originalContent = searchButton.innerHTML;
                searchButton.innerHTML = '<div class="loading-spinner"></div>';
                searchButton.disabled = true;
            }
            
            searchTimeout = setTimeout(function() {
                // Submit form after 800ms of no typing
                document.getElementById('searchForm').submit();
            }, 800);
        });

        // Focus effect for search input
        searchInput.addEventListener('focus', function() {
            this.parentElement.parentElement.classList.add('search-focused');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.parentElement.classList.remove('search-focused');
        });
    }

    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const photosGrid = document.getElementById('photosGrid');
            const photoCards = Array.from(photosGrid.querySelectorAll('.photo-card'));
            
            // Show loading state
            photosGrid.style.opacity = '0.5';
            photosGrid.style.transition = 'opacity 0.3s';
            
            setTimeout(() => {
                switch(this.value) {
                    case 'newest':
                        photoCards.sort((a, b) => b.dataset.date - a.dataset.date);
                        break;
                    case 'oldest':
                        photoCards.sort((a, b) => a.dataset.date - b.dataset.date);
                        break;
                    case 'title_asc':
                        photoCards.sort((a, b) => a.dataset.title.localeCompare(b.dataset.title));
                        break;
                    case 'title_desc':
                        photoCards.sort((a, b) => b.dataset.title.localeCompare(a.dataset.title));
                        break;
                    case 'popular':
                        photoCards.sort((a, b) => b.dataset.popularity - a.dataset.popularity);
                        break;
                }
                
                // Reappend sorted cards
                photoCards.forEach(card => photosGrid.appendChild(card));
                
                // Restore opacity
                photosGrid.style.opacity = '1';
            }, 300);
        });
    }

    // Highlight search term in titles
    const searchQuery = "<?php echo addslashes($search_query); ?>";
    if (searchQuery.trim() !== '') {
        const titles = document.querySelectorAll('.photo-card h3');
        titles.forEach(title => {
            const originalText = title.textContent;
            const regex = new RegExp(`(${searchQuery})`, 'gi');
            const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
            title.innerHTML = highlightedText;
        });
    }
});

// Function to clear search
function clearSearch() {
    window.location.href = 'album_detail.php?id=<?php echo $album_id; ?>';
}

// Function to handle Enter key in search
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('searchForm').submit();
    }
});

// Function to show login alert
function showLoginAlert() {
    // Remove existing notification
    const existingNotification = document.querySelector('.custom-notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = 'custom-notification fixed top-6 right-6 px-6 py-4 rounded-xl shadow-2xl z-50 transform transition-all duration-500 translate-x-full bg-gradient-to-r from-yellow-600 to-amber-700 text-white flex items-center space-x-3';
    notification.innerHTML = `
        <i class="fas fa-exclamation-triangle text-xl"></i>
        <span class="font-semibold">Silakan login untuk melakukan tindakan ini</span>
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
</script>

<?php include '../includes/footer.php'; ?>