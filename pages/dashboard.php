<?php
$title = "Dashboard";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/functions.php';

$search = $_GET['search'] ?? '';
$user_id = $_SESSION['user_id'];

// Get user's photos with search filter
$photos_sql = "SELECT f.*, u.Username, a.NamaAlbum 
               FROM gallery_foto f 
               LEFT JOIN gallery_user u ON f.UserID = u.UserID 
               LEFT JOIN gallery_album a ON f.AlbumID = a.AlbumID 
               WHERE f.UserID = ?";

$params = [$user_id];
$types = 'i';

if ($search) {
    $photos_sql .= " AND (f.JudulFoto LIKE ? OR f.DeskripsiFoto LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$photos_sql .= " ORDER BY f.TanggalUnggah DESC";

$stmt = $db->conn->prepare($photos_sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$photos = $stmt->get_result();

// Get user stats
$stats_sql = "SELECT 
              (SELECT COUNT(*) FROM gallery_foto WHERE UserID = ?) as total_photos,
              (SELECT COUNT(*) FROM gallery_album WHERE UserID = ?) as total_albums,
              (SELECT COUNT(*) FROM gallery_likefoto WHERE FotoID IN (SELECT FotoID FROM gallery_foto WHERE UserID = ?)) as total_likes,
              (SELECT COUNT(*) FROM gallery_komentarfoto WHERE FotoID IN (SELECT FotoID FROM gallery_foto WHERE UserID = ?)) as total_comments";

$stats_stmt = $db->conn->prepare($stats_sql);
$stats_stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result()->fetch_assoc();

// Get recent albums
$albums_sql = "SELECT a.*, COUNT(f.FotoID) as photo_count 
               FROM gallery_album a 
               LEFT JOIN gallery_foto f ON a.AlbumID = f.AlbumID 
               WHERE a.UserID = ? 
               GROUP BY a.AlbumID 
               ORDER BY a.TanggalDibuat DESC 
               LIMIT 4";
$albums_stmt = $db->conn->prepare($albums_sql);
$albums_stmt->bind_param('i', $user_id);
$albums_stmt->execute();
$recent_albums = $albums_stmt->get_result();
?>

<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Enhanced Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="../index.php" class="inline-flex items-center text-sm font-medium text-gray-300 hover:text-blue-400 transition-colors duration-200">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-home text-white text-xs"></i>
                        </div>
                        Beranda
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                        <span class="ml-1 text-sm font-medium text-gray-400 md:ml-2">Dashboard</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Enhanced Search Bar -->
    <div class="mb-8">
        <form method="GET" class="flex gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari foto Anda..." 
                       class="w-full pl-10 pr-4 py-3 bg-gray-800 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>
            <button type="submit" class="group relative bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-blue-500/20 font-semibold flex items-center space-x-2 overflow-hidden">
                <i class="fas fa-search"></i>
                <span>Search</span>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-800 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
            </button>
            <?php if ($search): ?>
                <a href="dashboard.php" class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-6 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg font-semibold flex items-center space-x-2">
                    <i class="fas fa-times"></i>
                    <span>Clear</span>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Welcome Section with Stats -->
    <div class="bg-gradient-to-br from-gray-800 via-gray-800 to-gray-900 rounded-2xl shadow-2xl p-8 mb-8 overflow-hidden relative border border-gray-700">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <!-- Animated background elements -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-full translate-x-1/2 translate-y-1/2"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                        Welcome back, <span class="gradient-text"><?php echo $_SESSION['nama_lengkap'] ?? $_SESSION['username']; ?></span>!
                    </h1>
                    <p class="text-gray-300 text-lg">Kelola dan jelajahi koleksi foto pribadi Anda</p>
                </div>
                <div class="flex space-x-3 mt-4 lg:mt-0">
                    <a href="explore.php" class="group relative bg-gray-700/50 backdrop-blur-sm text-white px-6 py-3 rounded-xl hover:bg-gray-600/50 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 border border-gray-600">
                        <i class="fas fa-compass"></i>
                        <span>Explore Gallery</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20 opacity-0 group-hover:opacity-100 rounded-xl transition-opacity duration-500"></div>
                    </a>
                    <a href="my_albums.php" class="group relative bg-gray-700/50 backdrop-blur-sm text-white px-6 py-3 rounded-xl hover:bg-gray-600/50 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 border border-gray-600">
                        <i class="fas fa-folder"></i>
                        <span>My Albums</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-green-600/20 to-teal-600/20 opacity-0 group-hover:opacity-100 rounded-xl transition-opacity duration-500"></div>
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="group bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center transform transition-all duration-300 hover:scale-105 border border-gray-700 hover:border-blue-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-images text-blue-400 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-white"><?php echo $stats_result['total_photos']; ?></p>
                    <p class="text-gray-300 text-sm">Total Photos</p>
                </div>
                
                <div class="group bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center transform transition-all duration-300 hover:scale-105 border border-gray-700 hover:border-purple-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-folder text-purple-400 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-white"><?php echo $stats_result['total_albums']; ?></p>
                    <p class="text-gray-300 text-sm">Albums</p>
                </div>
                
                <div class="group bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center transform transition-all duration-300 hover:scale-105 border border-gray-700 hover:border-pink-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500/20 to-pink-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-heart text-pink-400 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-white"><?php echo $stats_result['total_likes']; ?></p>
                    <p class="text-gray-300 text-sm">Total Likes</p>
                </div>
                
                <div class="group bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 text-center transform transition-all duration-300 hover:scale-105 border border-gray-700 hover:border-green-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-comment text-green-400 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-white"><?php echo $stats_result['total_comments']; ?></p>
                    <p class="text-gray-300 text-sm">Comments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Albums -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <div class="w-2 h-6 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full mr-3"></div>
                    Quick Actions
                </h2>
                <div class="space-y-3">
                    <a href="add_photo.php" class="group flex items-center space-x-3 p-3 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:from-green-900/20 hover:to-emerald-900/20 hover:border-green-500/30 transition-all duration-300">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Upload Photo</p>
                            <p class="text-sm text-gray-400">Add new photo to your collection</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-600 ml-auto group-hover:text-green-400 transition-colors duration-300"></i>
                    </a>
                    
                    <a href="my_albums.php" class="group flex items-center space-x-3 p-3 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:from-blue-900/20 hover:to-cyan-900/20 hover:border-blue-500/30 transition-all duration-300">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-folder-plus text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Create Album</p>
                            <p class="text-sm text-gray-400">Organize your photos in albums</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-600 ml-auto group-hover:text-blue-400 transition-colors duration-300"></i>
                    </a>
                    
                    <a href="explore.php" class="group flex items-center space-x-3 p-3 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:from-purple-900/20 hover:to-violet-900/20 hover:border-purple-500/30 transition-all duration-300">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-compass text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Explore Gallery</p>
                            <p class="text-sm text-gray-400">Discover photos from community</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-600 ml-auto group-hover:text-purple-400 transition-colors duration-300"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Albums -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <div class="w-2 h-6 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mr-3"></div>
                        Recent Albums
                    </h2>
                    <a href="my_albums.php" class="text-blue-400 hover:text-blue-300 font-semibold text-sm flex items-center space-x-1 group">
                        <span>View All</span>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform duration-300"></i>
                    </a>
                </div>
                
                <?php if ($recent_albums->num_rows > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php while ($album = $recent_albums->fetch_assoc()): ?>
                            <a href="album_detail.php?id=<?php echo $album['AlbumID']; ?>" class="group">
                                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 p-4 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:border-blue-500/30">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-folder text-white"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-white truncate group-hover:text-blue-400 transition-colors duration-200">
                                                <?php echo htmlspecialchars($album['NamaAlbum']); ?>
                                            </h3>
                                            <p class="text-sm text-gray-400"><?php echo $album['photo_count']; ?> photos</p>
                                        </div>
                                    </div>
                                    <?php if ($album['Deskripsi']): ?>
                                        <p class="text-gray-300 text-sm line-clamp-2"><?php echo htmlspecialchars($album['Deskripsi']); ?></p>
                                    <?php endif; ?>
                                    <div class="flex justify-between items-center mt-3 text-xs text-gray-500">
                                        <span>Created: <?php echo date('M j, Y', strtotime($album['TanggalDibuat'])); ?></span>
                                        <span class="flex items-center space-x-1 text-blue-400 group-hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-xl border-2 border-dashed border-gray-700">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                            <i class="fas fa-folder-open text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-300 mb-2">No Albums Yet</h3>
                        <p class="text-gray-400 mb-4">Create your first album to organize photos</p>
                        <a href="my_albums.php" class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-2 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 inline-flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Create Album</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- My Photos Section -->
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-gray-700">
        <!-- Section Header -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 border-b border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-camera mr-3 text-blue-400"></i>
                    My Photos
                </h2>
                <span class="bg-blue-600/20 text-blue-300 px-3 py-1 rounded-full text-sm font-semibold border border-blue-500/30">
                    <?php echo $photos->num_rows; ?> photos
                </span>
            </div>
        </div>

        <div class="p-6">
            <?php if ($photos->num_rows > 0): ?>
                <!-- Enhanced Photos Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php while ($photo = $photos->fetch_assoc()): ?>
                        <div class="group bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-700 hover:border-blue-500/30">
                            <!-- Photo Container -->
                            <div class="relative overflow-hidden">
                                <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                                     alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                                     class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                                
                                <!-- Album Badge -->
                                <div class="absolute top-3 right-3 bg-black/70 text-white px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm border border-gray-700">
                                    <?php echo htmlspecialchars($photo['NamaAlbum'] ?? 'No Album'); ?>
                                </div>
                                
                                <!-- Overlay Actions -->
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/70 transition-all duration-500 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex space-x-3 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        <a href="detail.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 font-semibold text-sm flex items-center space-x-2">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                        <a href="../process/download.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-2 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-105 font-semibold text-sm flex items-center space-x-2">
                                            <i class="fas fa-download"></i>
                                            <span>Download</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Photo Details -->
                            <div class="p-4">
                                <h3 class="font-bold text-white mb-2 line-clamp-1 group-hover:text-blue-400 transition-colors duration-200">
                                    <?php echo htmlspecialchars($photo['JudulFoto']); ?>
                                </h3>
                                <p class="text-gray-400 text-sm mb-3 line-clamp-2">
                                    <?php echo htmlspecialchars($photo['DeskripsiFoto']); ?>
                                </p>
                                
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('M j, Y', strtotime($photo['TanggalUnggah'])); ?></span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fas fa-user"></i>
                                        <span>You</span>
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex justify-between items-center pt-3 border-t border-gray-700">
                                    <a href="detail.php?id=<?php echo $photo['FotoID']; ?>" 
                                       class="text-blue-400 hover:text-blue-300 text-sm font-semibold flex items-center space-x-1 transition-colors duration-200 group/link">
                                        <span>View Details</span>
                                        <i class="fas fa-chevron-right text-xs group-hover/link:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                    <div class="flex space-x-3">
                                        <a href="../process/download.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="text-green-400 hover:text-green-300 transition-colors duration-200 transform hover:scale-110" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="edit_photo.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="text-yellow-400 hover:text-yellow-300 transition-colors duration-200 transform hover:scale-110" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../process/delete_photo.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="text-red-400 hover:text-red-300 transition-colors duration-200 transform hover:scale-110" 
                                           onclick="return confirm('Are you sure you want to delete this photo?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-12 bg-gradient-to-br from-gray-800/50 to-gray-900/50 rounded-xl border-2 border-dashed border-gray-700">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-700 to-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                        <i class="fas fa-images text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-300 mb-2">
                        <?php echo $search ? 'No photos found' : 'No photos yet'; ?>
                    </h3>
                    <p class="text-gray-400 mb-6 max-w-md mx-auto">
                        <?php echo $search ? 'Try a different search term' : 'Start building your photo collection by uploading your first photo!'; ?>
                    </p>
                    <a href="add_photo.php" class="group relative bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-blue-500/20 inline-flex items-center space-x-2 font-semibold overflow-hidden">
                        <i class="fas fa-plus"></i>
                        <span>Upload First Photo</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-800 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Custom animations for dashboard */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.photo-card {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
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

/* Hover glow for cards */
.hover-glow-card {
    transition: box-shadow 0.3s ease;
}

.hover-glow-card:hover {
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
}

/* Smooth image loading */
.img-loading {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Gradient border animation */
.gradient-border-animated {
    position: relative;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
    border-radius: inherit;
    padding: 1px;
}

.gradient-border-animated::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1px;
    background: inherit;
    -webkit-mask: 
        linear-gradient(#fff 0 0) content-box, 
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: borderRotate 3s linear infinite;
}

@keyframes borderRotate {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to photo cards
    const photoCards = document.querySelectorAll('.group.bg-gradient-to-br');
    photoCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('photo-card');
    });

    // Add scroll effect to navbar
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // Stats cards hover effect
    const statsCards = document.querySelectorAll('.group.bg-gray-800\\/50');
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.3)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '';
        });
    });

    // Image lazy loading
    const images = document.querySelectorAll('img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.6s ease';
                
                setTimeout(() => {
                    img.style.opacity = '1';
                }, 100);
                
                imageObserver.unobserve(img);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });

    images.forEach(img => {
        if (!img.complete) {
            img.style.opacity = '0';
            imageObserver.observe(img);
        }
    });

    // Search input enhancement
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-500');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-500');
        });
    }

    // Quick action cards animation
    const quickActionCards = document.querySelectorAll('a.group.flex');
    quickActionCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Add hover effect to album cards
    const albumCards = document.querySelectorAll('a.group');
    albumCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const innerDiv = this.querySelector('div');
            if (innerDiv) {
                innerDiv.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.4)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const innerDiv = this.querySelector('div');
            if (innerDiv) {
                innerDiv.style.boxShadow = '';
            }
        });
    });
});

// Initialize on page load
window.addEventListener('load', function() {
    const nav = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    }
});
</script>

<?php include '../includes/footer.php'; ?>