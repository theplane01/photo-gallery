<?php
// Tambahkan ini di BARIS PALING ATAS
ob_start(); // Start output buffering

$title = "Upload Photo";
include '../includes/header.php';
include '../includes/auth_check.php';

require_once '../config/database.php';
$user_id = $_SESSION['user_id'];

// Get user's albums
$albums_sql = "SELECT * FROM gallery_album WHERE UserID = ? ORDER BY NamaAlbum";
$albums_stmt = $db->conn->prepare($albums_sql);
$albums_stmt->bind_param("i", $user_id);
$albums_stmt->execute();
$albums = $albums_stmt->get_result();

// Check if user has any albums
$has_albums = $albums->num_rows > 0;

// If no albums and not coming from album creation, redirect to albums page
if (!$has_albums && !isset($_GET['from_album'])) {
    $_SESSION['redirect_to_upload'] = true;
    
    // Clear output buffer before header
    ob_end_clean();
    header("Location: my_albums.php?error=Please create an album first before uploading photos");
    exit();
}

// Pre-select album if coming from album detail
$preselected_album = isset($_GET['album_id']) ? intval($_GET['album_id']) : '';
?>

<!-- Rest of your HTML code -->

<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Upload New Photo</h1>

        <?php if (!$has_albums): ?>
            <!-- No Albums Warning -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-yellow-800">No Albums Found</h3>
                </div>
                <p class="text-yellow-700 mb-4">
                    You need to create an album before you can upload photos. Albums help you organize your photos.
                </p>
                <a href="my_albums.php" 
                   class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create Your First Album
                </a>
            </div>
        <?php endif; ?>

        <form action="../process/photo_process.php" method="POST" enctype="multipart/form-data" class="space-y-6 <?php echo !$has_albums ? 'opacity-50 pointer-events-none' : ''; ?>">
            <div>
                <label for="judul_foto" class="block text-sm font-medium text-gray-700 mb-2">Photo Title *</label>
                <input type="text" id="judul_foto" name="judul_foto" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="deskripsi_foto" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="deskripsi_foto" name="deskripsi_foto" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div>
                <label for="album_id" class="block text-sm font-medium text-gray-700 mb-2">Album *</label>
                <select id="album_id" name="album_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Album</option>
                    <?php while ($album = $albums->fetch_assoc()): ?>
                        <option value="<?php echo $album['AlbumID']; ?>" 
                                <?php echo $album['AlbumID'] == $preselected_album ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($album['NamaAlbum']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <p class="text-sm text-gray-500 mt-1">
                    Don't see the album you want? 
                    <a href="my_albums.php" class="text-blue-600 hover:text-blue-800">Create a new album</a>
                </p>
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Photo File *</label>
                <input type="file" id="foto" name="foto" accept="image/*" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Supported formats: JPG, JPEG, PNG, GIF. Max size: 5MB</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <div class="flex justify-end space-x-4">
                <a href="dashboard.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit" name="add_photo" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
                        <?php echo !$has_albums ? 'disabled' : ''; ?>>
                    Upload Photo
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>