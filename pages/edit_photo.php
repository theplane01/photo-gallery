<?php
$title = "Edit Photo";
include '../includes/header.php';
include '../includes/auth_check.php';
include '../includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$photo_id = $_GET['id'];
$photo = getPhotoById($photo_id);

// Check if user owns the photo or is admin
if (!$photo || ($_SESSION['user_id'] != $photo['UserID'] && !isAdmin())) {
    header("Location: dashboard.php");
    exit();
}

$albums = getAllAlbums();
?>

<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Photo</h1>

        <form action="../process/photo_process.php" method="POST" class="space-y-6">
            <input type="hidden" name="foto_id" value="<?php echo $photo['FotoID']; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="judul_foto" class="block text-sm font-medium text-gray-700 mb-2">Photo Title *</label>
                    <input type="text" id="judul_foto" name="judul_foto" required
                           value="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="album_id" class="block text-sm font-medium text-gray-700 mb-2">Album</label>
                    <select id="album_id" name="album_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Album</option>
                        <?php 
                        $albums = getAllAlbums();
                        while ($album = $albums->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $album['AlbumID']; ?>" 
                                    <?php echo $album['AlbumID'] == $photo['AlbumID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($album['NamaAlbum']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="deskripsi_foto" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="deskripsi_foto" name="deskripsi_foto" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($photo['DeskripsiFoto']); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo</label>
                <img src="../uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                     alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                     class="w-64 h-48 object-cover rounded-lg border">
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
                <button type="submit" name="edit_photo" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Update Photo
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>