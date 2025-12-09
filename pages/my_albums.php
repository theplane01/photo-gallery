<?php
$title = "My Albums";
include '../includes/header.php';
include '../includes/auth_check.php';

require_once '../config/database.php';
$user_id = $_SESSION['user_id'];

// Get user's albums with photo counts
$albums_sql = "SELECT a.*, 
               COUNT(f.FotoID) as photo_count,
               (SELECT LokasiFile FROM gallery_foto WHERE AlbumID = a.AlbumID ORDER BY TanggalUnggah DESC LIMIT 1) as cover_photo
               FROM gallery_album a 
               LEFT JOIN gallery_foto f ON a.AlbumID = f.AlbumID 
               WHERE a.UserID = ? 
               GROUP BY a.AlbumID 
               ORDER BY a.TanggalDibuat DESC";
$albums_stmt = $db->conn->prepare($albums_sql);
$albums_stmt->bind_param("i", $user_id);
$albums_stmt->execute();
$albums = $albums_stmt->get_result();
?>

<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Enhanced Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="../index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-home text-white text-xs"></i>
                        </div>
                        Beranda
                    </a>
                </li>
                <li class="inline-flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                    <a href="dashboard.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors duration-200">
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">My Albums</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Enhanced Header -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl shadow-xl p-8 mb-8 text-white overflow-hidden relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-folder text-white text-lg"></i>
                        </div>
                        My Albums
                    </h1>
                    <p class="text-blue-100 text-lg">Kelola dan organisir koleksi album foto Anda</p>
                </div>
                <button onclick="openAddAlbumModal()" 
                        class="bg-white text-blue-600 px-8 py-4 rounded-xl hover:bg-blue-50 transition-all duration-300 transform hover:-translate-y-1 shadow-lg font-bold flex items-center space-x-3 group">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <span>Create New Album</span>
                </button>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white opacity-5 rounded-full"></div>
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-white opacity-5 rounded-full"></div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-lg mb-8 flex items-center space-x-3 animate-fade-in">
            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-white"></i>
            </div>
            <span class="font-semibold"><?php echo htmlspecialchars($_GET['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 rounded-xl shadow-lg mb-8 flex items-center space-x-3 animate-fade-in">
            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <span class="font-semibold"><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Albums Grid -->
    <?php if ($albums->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php while ($album = $albums->fetch_assoc()): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 group border border-gray-200">
                    <!-- Album Cover -->
                    <div class="relative h-56 bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden">
                        <?php if ($album['cover_photo']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($album['cover_photo']); ?>" 
                                 alt="<?php echo htmlspecialchars($album['NamaAlbum']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center text-white p-6">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mb-4">
                                    <i class="fas fa-images text-2xl"></i>
                                </div>
                                <p class="text-center text-white text-opacity-80">No photos yet</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Photo Count Badge -->
                        <div class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-2 rounded-xl text-sm font-semibold backdrop-blur-sm flex items-center space-x-2">
                            <i class="fas fa-images text-blue-300"></i>
                            <span><?php echo $album['photo_count']; ?> photos</span>
                        </div>
                        
                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-500 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="flex space-x-3">
                                <a href="album_detail.php?id=<?php echo $album['AlbumID']; ?>" 
                                   class="bg-white text-gray-800 px-5 py-3 rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 font-semibold flex items-center space-x-2">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </a>
                                <button onclick="openEditAlbumModal(<?php echo $album['AlbumID']; ?>, '<?php echo htmlspecialchars($album['NamaAlbum']); ?>', '<?php echo htmlspecialchars($album['Deskripsi']); ?>')" 
                                        class="bg-white text-gray-800 px-5 py-3 rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 font-semibold flex items-center space-x-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Album Info -->
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-3 line-clamp-1 group-hover:text-blue-600 transition-colors duration-200">
                            <?php echo htmlspecialchars($album['NamaAlbum']); ?>
                        </h3>
                        
                        <?php if ($album['Deskripsi']): ?>
                            <p class="text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                                <?php echo htmlspecialchars($album['Deskripsi']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span class="flex items-center space-x-2">
                                <i class="fas fa-calendar text-blue-500"></i>
                                <span>Created: <?php echo date('M j, Y', strtotime($album['TanggalDibuat'])); ?></span>
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <a href="album_detail.php?id=<?php echo $album['AlbumID']; ?>" 
                               class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center space-x-2 transition-colors duration-200 group/view">
                                <span>View Album</span>
                                <i class="fas fa-chevron-right text-xs group-hover/view:translate-x-1 transition-transform duration-200"></i>
                            </a>
                            <div class="flex space-x-3">
                                <button onclick="openEditAlbumModal(<?php echo $album['AlbumID']; ?>, '<?php echo htmlspecialchars($album['NamaAlbum']); ?>', '<?php echo htmlspecialchars($album['Deskripsi']); ?>')" 
                                        class="text-yellow-600 hover:text-yellow-800 transition-colors duration-200 transform hover:scale-110" title="Edit Album">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="../process/album_process.php?delete_id=<?php echo $album['AlbumID']; ?>" 
                                   class="text-red-600 hover:text-red-800 transition-colors duration-200 transform hover:scale-110" 
                                   onclick="return confirm('Are you sure you want to delete this album? All photos in this album will also be deleted.')" 
                                   title="Delete Album">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <!-- Enhanced No Albums State -->
        <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg border-2 border-dashed border-gray-300">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">No Albums Yet</h3>
                <p class="text-gray-500 mb-6">Create your first album to organize your photos and start building your gallery!</p>
                <button onclick="openAddAlbumModal()" 
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg font-bold flex items-center space-x-3 mx-auto group">
                    <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <span>Create First Album</span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Enhanced Add Album Modal -->
<div id="addAlbumModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-500 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-6 py-5 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    Create New Album
                </h3>
                <button onclick="closeAddAlbumModal()" class="text-white hover:text-blue-200 transition-colors duration-200 transform hover:scale-110">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Form -->
        <form action="../process/album_process.php" method="POST" class="p-6 space-y-6">
            <div>
                <label for="nama_album" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-heading text-blue-500 mr-2"></i>
                    Album Name *
                </label>
                <input type="text" id="nama_album" name="nama_album" required
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                       placeholder="Enter a creative name for your album">
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-align-left text-green-500 mr-2"></i>
                    Description
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"
                          placeholder="Describe your album (optional)"></textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeAddAlbumModal()" 
                        class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold">
                    Cancel
                </button>
                <button type="submit" name="add_album" 
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Create Album</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced Edit Album Modal -->
<div id="editAlbumModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-500 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-amber-600 px-6 py-5 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-edit text-white"></i>
                    </div>
                    Edit Album
                </h3>
                <button onclick="closeEditAlbumModal()" class="text-white hover:text-yellow-200 transition-colors duration-200 transform hover:scale-110">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Form -->
        <form action="../process/album_process.php" method="POST" class="p-6 space-y-6">
            <input type="hidden" id="edit_album_id" name="album_id">
            
            <div>
                <label for="edit_nama_album" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-heading text-blue-500 mr-2"></i>
                    Album Name *
                </label>
                <input type="text" id="edit_nama_album" name="nama_album" required
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
            </div>

            <div>
                <label for="edit_deskripsi" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-align-left text-green-500 mr-2"></i>
                    Description
                </label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="4"
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"></textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" onclick="closeEditAlbumModal()" 
                        class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold">
                    Cancel
                </button>
                <button type="submit" name="edit_album" 
                        class="bg-gradient-to-r from-yellow-500 to-amber-600 text-white px-6 py-3 rounded-xl hover:from-yellow-600 hover:to-amber-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update Album</span>
                </button>
            </div>
        </form>
    </div>
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

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.album-card {
    animation: fadeInUp 0.6s ease-out;
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

/* Modal animations */
.modal-enter {
    transform: scale(0.95);
    opacity: 0;
}

.modal-enter-active {
    transform: scale(1);
    opacity: 1;
    transition: all 0.3s ease-out;
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(10px)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #a78bfa, #6366f1);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #8b5cf6, #4f46e5);
}
</style>

<script>
// Enhanced Modal functions
function openAddAlbumModal() {
    const modal = document.getElementById('addAlbumModal');
    const modalContent = modal.querySelector('.transform');
    modal.classList.remove('hidden');
    
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Focus on first input
    setTimeout(() => {
        document.getElementById('nama_album').focus();
    }, 300);
}

function closeAddAlbumModal() {
    const modal = document.getElementById('addAlbumModal');
    const modalContent = modal.querySelector('.transform');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.remove('opacity-100');
        modal.classList.add('hidden');
    }, 300);
}

function openEditAlbumModal(albumId, namaAlbum, deskripsi) {
    const modal = document.getElementById('editAlbumModal');
    const modalContent = modal.querySelector('.transform');
    
    document.getElementById('edit_album_id').value = albumId;
    document.getElementById('edit_nama_album').value = namaAlbum;
    document.getElementById('edit_deskripsi').value = deskripsi || '';
    
    modal.classList.remove('hidden');
    
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Focus on first input
    setTimeout(() => {
        document.getElementById('edit_nama_album').focus();
    }, 300);
}

function closeEditAlbumModal() {
    const modal = document.getElementById('editAlbumModal');
    const modalContent = modal.querySelector('.transform');
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.remove('opacity-100');
        modal.classList.add('hidden');
    }, 300);
}

// Close modals when clicking outside or pressing Escape
document.addEventListener('click', function(event) {
    const addModal = document.getElementById('addAlbumModal');
    const editModal = document.getElementById('editAlbumModal');
    
    if (event.target === addModal) {
        closeAddAlbumModal();
    }
    if (event.target === editModal) {
        closeEditAlbumModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddAlbumModal();
        closeEditAlbumModal();
    }
});

// Add animation to album cards
document.addEventListener('DOMContentLoaded', function() {
    const albumCards = document.querySelectorAll('.bg-white.rounded-2xl');
    albumCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('album-card');
    });

    // Enhanced form interactions
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-500', 'rounded-xl');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-500', 'rounded-xl');
        });
    });
});

// Enhanced confirmation for delete
function confirmDelete(link) {
    const result = confirm('Are you sure you want to delete this album? All photos in this album will also be deleted.');
    if (result) {
        window.location.href = link.href;
    }
    return false;
}
</script>

<?php include '../includes/footer.php'; ?>