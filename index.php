<?php
session_start();
$title = "PhotoGallery - Beranda";

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Ambil data user untuk foto profil (jika sudah login)
if ($is_logged_in) {
    require_once __DIR__ . '/config/database.php';
    $db = new Database();
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT profile_pic FROM gallery_user WHERE UserID = ?";
    $stmt = $db->conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    
    $profile_pic = $user_data['profile_pic'] ?? null;
} else {
    $profile_pic = null;
}

// Get trending photos for homepage
require_once 'config/database.php';
$db = new Database();

// Get trending photos (most liked in last 7 days)
$trending_sql = "SELECT f.*, u.Username, u.profile_pic, COUNT(l.LikeID) as like_count 
                 FROM gallery_foto f 
                 LEFT JOIN gallery_likefoto l ON f.FotoID = l.FotoID 
                 LEFT JOIN gallery_user u ON f.UserID = u.UserID 
                 WHERE f.TanggalUnggah >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY f.FotoID 
                 ORDER BY like_count DESC 
                 LIMIT 6";
$trending_photos = $db->conn->query($trending_sql);

// Get latest photos
$latest_sql = "SELECT f.*, u.Username, u.profile_pic 
               FROM gallery_foto f 
               LEFT JOIN gallery_user u ON f.UserID = u.UserID 
               ORDER BY f.TanggalUnggah DESC 
               LIMIT 6";
$latest_photos = $db->conn->query($latest_sql);

// Get total stats
$stats_sql = "SELECT 
              (SELECT COUNT(*) FROM gallery_foto) as total_photos,
              (SELECT COUNT(*) FROM gallery_user) as total_users,
              (SELECT COUNT(*) FROM gallery_komentarfoto) as total_comments,
              (SELECT COUNT(*) FROM gallery_likefoto) as total_likes";
$stats_result = $db->conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$current_page = 'index.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dropdown animations */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        /* Mobile dropdown */
        .mobile-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .mobile-dropdown.active {
            max-height: 300px;
        }
        
        /* Sticky navbar with blur effect */
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: rgba(17, 24, 39, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sticky-nav.scrolled {
            background: rgba(17, 24, 39, 0.98);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Hamburger menu animation */
        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            margin: 5px auto;
            background-color: white;
            transition: all 0.3s ease-in-out;
        }
        
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger.active span:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        
        .hamburger.active span:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
        
        /* Hero Section Styles */
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .text-glow {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
        
        .btn-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.6);
        }
        
        /* Animasi untuk teks */
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
        
        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out;
        }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        /* Photo card hover effects */
        .photo-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .photo-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Stat card animations */
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(17, 24, 39, 0.5);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #7c3aed, #3b82f6);
            border-radius: 5px;
            border: 2px solid rgba(17, 24, 39, 0.5);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #8b5cf6, #4f46e5);
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    <!-- Enhanced Sticky Navigation -->
    <nav class="sticky-nav" id="mainNav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo with Animation -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500 floating">
                            <i class="fas fa-camera text-white text-lg"></i>
                        </div>
                        <div class="hidden md:block">
                            <span class="text-xl font-bold text-white">PhotoGallery</span>
                            <p class="text-xs text-gray-400 -mt-1">Share Your Moments</p>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <?php if ($is_logged_in): ?>
                        <!-- User sudah login -->
                        <a href="index.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-all duration-300 flex items-center space-x-1 <?php echo $current_page == 'index.php' ? 'text-blue-400 font-semibold' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-home text-blue-400"></i>
                            </div>
                            <span class="hidden lg:inline">Beranda</span>
                        </a>
                        
                        <a href="pages/explore.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-all duration-300 flex items-center space-x-1">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-compass text-purple-400"></i>
                            </div>
                            <span class="hidden lg:inline">Explore</span>
                        </a>
                        
                        <a href="pages/add_photo.php" class="group relative bg-gradient-to-r from-blue-600 to-purple-700 text-white px-5 py-2.5 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 shadow-lg shadow-blue-500/20">
                            <i class="fas fa-plus"></i>
                            <span class="hidden sm:inline">Upload Foto</span>
                        </a>
                        
                        <!-- Enhanced Profile Dropdown -->
                        <div class="relative dropdown">
                            <button class="flex items-center space-x-3 text-white hover:text-blue-400 focus:outline-none group transition-all duration-300">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative overflow-hidden">
                                    <?php if (!empty($profile_pic)): ?>
                                        <img src="uploads/profiles/<?php echo htmlspecialchars($profile_pic); ?>" 
                                             alt="Profile" 
                                             class="w-full h-full rounded-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/50 to-purple-700/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <?php else: ?>
                                        <i class="fas fa-user text-white relative z-10"></i>
                                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-left hidden lg:block">
                                    <p class="font-medium text-sm"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo htmlspecialchars($_SESSION['level'] ?? 'User'); ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-xs transform group-hover:rotate-180 transition-transform duration-300 hidden lg:inline"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-3 w-56 bg-gray-800/95 backdrop-blur-lg rounded-2xl shadow-2xl py-3 dropdown-menu z-50 border border-gray-700/50">
                                <div class="px-4 py-3 border-b border-gray-700/50">
                                    <p class="text-white font-semibold"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></p>
                                    <p class="text-gray-400 text-sm truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                                </div>
                                
                                <a href="pages/dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-blue-600/20 hover:text-white transition-all duration-300 group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-tachometer-alt text-white text-sm"></i>
                                    </div>
                                    <span>Dashboard</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                                
                                <a href="pages/profile.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-green-600/20 hover:text-white transition-all duration-300 group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span>Profile</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                                
                                <a href="pages/my_albums.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-purple-600/20 hover:text-white transition-all duration-300 group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-folder text-white text-sm"></i>
                                    </div>
                                    <span>My Albums</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                                
                                <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'Admin'): ?>
                                    <a href="pages/admin.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-yellow-600/20 hover:text-white transition-all duration-300 group/menu">
                                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                            <i class="fas fa-cog text-white text-sm"></i>
                                        </div>
                                        <span>Admin Panel</span>
                                        <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <div class="border-t border-gray-700/50 my-2"></div>
                                
                                <a href="process/logout.php" class="flex items-center space-x-3 px-4 py-3 text-red-400 hover:bg-red-600/20 hover:text-red-300 transition-all duration-300 group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-sign-out-alt text-white text-sm"></i>
                                    </div>
                                    <span>Logout</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- User belum login -->
                        <a href="index.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Beranda</a>
                        <a href="pages/explore.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Explore</a>
                        <a href="pages/login.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Login</a>
                        <a href="pages/register.php" class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-2.5 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold shadow-lg shadow-blue-500/20">
                            Daftar Sekarang
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuButton" class="md:hidden hamburger focus:outline-none">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            
            <!-- Mobile Navigation Menu -->
            <div id="mobileMenu" class="mobile-dropdown md:hidden">
                <div class="py-4 space-y-4 border-t border-gray-700/50 mt-2">
                    <?php if ($is_logged_in): ?>
                        <!-- Mobile Menu for logged in users -->
                        <div class="flex items-center space-x-3 p-3 bg-gray-800/50 rounded-lg mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative overflow-hidden">
                                <?php if (!empty($profile_pic)): ?>
                                    <img src="uploads/profiles/<?php echo htmlspecialchars($profile_pic); ?>" 
                                         alt="Profile" 
                                         class="w-full h-full rounded-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-user text-white relative z-10"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="font-medium text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                <p class="text-xs text-gray-400"><?php echo htmlspecialchars($_SESSION['level'] ?? 'User'); ?></p>
                            </div>
                        </div>
                        
                        <a href="index.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-home text-blue-400"></i>
                            </div>
                            <span>Beranda</span>
                        </a>
                        
                        <a href="pages/explore.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-purple-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-compass text-purple-400"></i>
                            </div>
                            <span>Explore</span>
                        </a>
                        
                        <a href="pages/add_photo.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-blue-400"></i>
                            </div>
                            <span>Upload Foto</span>
                        </a>
                        
                        <a href="pages/dashboard.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tachometer-alt text-blue-400"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="pages/profile.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-green-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-green-400"></i>
                            </div>
                            <span>Profile</span>
                        </a>
                        
                        <a href="pages/my_albums.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-purple-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-purple-400"></i>
                            </div>
                            <span>My Albums</span>
                        </a>
                        
                        <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'Admin'): ?>
                            <a href="pages/admin.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-yellow-600/20 hover:text-white rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cog text-yellow-400"></i>
                                </div>
                                <span>Admin Panel</span>
                            </a>
                        <?php endif; ?>
                        
                        <div class="border-t border-gray-700/50 my-2"></div>
                        
                        <a href="process/logout.php" class="flex items-center space-x-3 p-3 text-red-400 hover:bg-red-600/20 hover:text-red-300 rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500/20 to-red-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sign-out-alt text-red-400"></i>
                            </div>
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <!-- Mobile Menu for guests -->
                        <a href="index.php" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            Beranda
                        </a>
                        <a href="pages/explore.php" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            Explore
                        </a>
                        <a href="pages/login.php" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            Login
                        </a>
                        <a href="pages/register.php" class="block p-3 bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-lg text-center hover:from-blue-700 hover:to-purple-800 transition-all duration-300">
                            Daftar Sekarang
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative z-0">
        <!-- Hero Section -->
        <section id="hero" class="hero-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="text-center text-white px-4 max-w-4xl mx-auto animate-fade-in-up">
                    <!-- Main Heading -->
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 text-glow">
                        Photo<span class="text-blue-400">Gallery</span>
                    </h1>
                    
                    <!-- Subheading -->
                    <p class="text-xl md:text-2xl mb-12 text-gray-300" style="animation-delay: 0.2s;">
                        Temukan dan Bagikan Momen Terindah Anda
                    </p>
                    
                    <!-- Action Buttons -->
                    <div style="animation-delay: 0.4s;">
                        <?php if (!$is_logged_in): ?>
                            <!-- Untuk pengguna yang belum login -->
                            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                                <a href="pages/explore.php" 
                                   class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg btn-glow flex items-center space-x-3 w-full sm:w-auto justify-center">
                                    <i class="fas fa-compass"></i>
                                    <span>Jelajahi Sekarang</span>
                                </a>
                                <a href="pages/register.php" 
                                   class="border-2 border-white text-white px-8 py-4 rounded-xl hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg flex items-center space-x-3 w-full sm:w-auto justify-center">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Daftar Gratis</span>
                                </a>
                            </div>
                            
                            <!-- Additional Info -->
                            <p class="mt-8 text-gray-400 text-sm md:text-base">
                                Jelajahi ribuan foto menarik tanpa perlu mendaftar
                            </p>
                        <?php else: ?>
                            <!-- Untuk pengguna yang sudah login -->
                            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                                <a href="pages/explore.php" 
                                   class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg btn-glow flex items-center space-x-3 w-full sm:w-auto justify-center">
                                    <i class="fas fa-compass"></i>
                                    <span>Jelajahi Gallery</span>
                                </a>
                                <a href="pages/add_photo.php" 
                                   class="border-2 border-white text-white px-8 py-4 rounded-xl hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg flex items-center space-x-3 w-full sm:w-auto justify-center">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Upload Foto</span>
                                </a>
                            </div>
                            
                            <!-- Welcome Message -->
                            <p class="mt-8 text-gray-300 text-base md:text-lg">
                                Selamat datang kembali, <span class="text-blue-400 font-semibold"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></span>!
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Scroll Indicator (hanya untuk non-login) -->
                    <?php if (!$is_logged_in): ?>
                        <div class="mt-16 animate-bounce">
                            <a href="#trending" class="text-white hover:text-blue-400 transition-colors duration-300 flex flex-col items-center">
                                <span class="text-sm mb-2">Jelajahi Trending</span>
                                <i class="fas fa-chevron-down text-xl"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-12 bg-gradient-to-b from-gray-800 to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="stat-card bg-gradient-to-br from-blue-900/50 to-blue-800/50 p-6 rounded-2xl border border-blue-800 text-center hover:shadow-xl">
                        <div class="text-3xl font-bold text-white mb-2"><?php echo number_format($stats['total_photos']); ?></div>
                        <div class="flex items-center justify-center space-x-2 text-blue-300">
                            <i class="fas fa-image"></i>
                            <span>Total Foto</span>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-gradient-to-br from-purple-900/50 to-purple-800/50 p-6 rounded-2xl border border-purple-800 text-center hover:shadow-xl">
                        <div class="text-3xl font-bold text-white mb-2"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="flex items-center justify-center space-x-2 text-purple-300">
                            <i class="fas fa-users"></i>
                            <span>Pengguna</span>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-gradient-to-br from-green-900/50 to-green-800/50 p-6 rounded-2xl border border-green-800 text-center hover:shadow-xl">
                        <div class="text-3xl font-bold text-white mb-2"><?php echo number_format($stats['total_likes']); ?></div>
                        <div class="flex items-center justify-center space-x-2 text-green-300">
                            <i class="fas fa-heart"></i>
                            <span>Like</span>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-gradient-to-br from-yellow-900/50 to-yellow-800/50 p-6 rounded-2xl border border-yellow-800 text-center hover:shadow-xl">
                        <div class="text-3xl font-bold text-white mb-2"><?php echo number_format($stats['total_comments']); ?></div>
                        <div class="flex items-center justify-center space-x-2 text-yellow-300">
                            <i class="fas fa-comments"></i>
                            <span>Komentar</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trending Photos Section -->
        <section id="trending" class="py-16 bg-gradient-to-b from-gray-900 to-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        <i class="fas fa-fire text-red-500 mr-2"></i>
                        <span class="gradient-text">Trending Photos</span>
                    </h2>
                    <p class="text-gray-400 max-w-2xl mx-auto">
                        Foto paling populer minggu ini yang banyak disukai komunitas
                    </p>
                </div>

                <?php if ($trending_photos && $trending_photos->num_rows > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($photo = $trending_photos->fetch_assoc()): ?>
                            <div class="photo-card bg-gray-800/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-gray-700/50">
                                <a href="pages/detail.php?id=<?php echo $photo['FotoID']; ?>" class="block aspect-square overflow-hidden">
                                    <img src="uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                                         alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                </a>
                                
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <?php if (!empty($photo['profile_pic'])): ?>
                                                <img src="uploads/profiles/<?php echo htmlspecialchars($photo['profile_pic']); ?>" 
                                                     alt="<?php echo htmlspecialchars($photo['Username']); ?>"
                                                     class="w-8 h-8 rounded-full object-cover">
                                            <?php else: ?>
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-white text-xs"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="text-sm font-medium text-white"><?php echo htmlspecialchars($photo['Username']); ?></span>
                                        </div>
                                        <span class="flex items-center space-x-1 text-red-400">
                                            <i class="fas fa-fire"></i>
                                            <span class="text-sm"><?php echo $photo['like_count']; ?></span>
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-base font-semibold text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($photo['JudulFoto']); ?></h3>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400">
                                            <?php echo date('M d, Y', strtotime($photo['TanggalUnggah'])); ?>
                                        </span>
                                        <a href="pages/detail.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="text-blue-400 hover:text-blue-300 font-medium">
                                            Lihat Detail →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="text-center mt-12">
                        <a href="pages/explore.php?sort=popular" 
                           class="inline-flex items-center bg-gradient-to-r from-red-600 to-orange-600 text-white px-8 py-3 rounded-xl hover:from-red-700 hover:to-orange-700 transition-all duration-300 transform hover:-translate-y-1 font-semibold shadow-lg shadow-red-500/20">
                            <i class="fas fa-fire mr-2"></i>
                            Lihat Semua Trending
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center border border-gray-700">
                            <i class="fas fa-fire text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Belum Ada Trending</h3>
                        <p class="text-gray-400 max-w-md mx-auto">
                            Jadilah yang pertama memulai tren dengan mengupload foto menarik!
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Latest Photos Section -->
        <section class="py-16 bg-gradient-to-b from-gray-800 to-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        <i class="fas fa-clock text-blue-400 mr-2"></i>
                        <span class="gradient-text">Latest Photos</span>
                    </h2>
                    <p class="text-gray-400 max-w-2xl mx-auto">
                        Foto terbaru yang baru saja diupload oleh komunitas
                    </p>
                </div>

                <?php if ($latest_photos && $latest_photos->num_rows > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($photo = $latest_photos->fetch_assoc()): ?>
                            <div class="photo-card bg-gray-800/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-gray-700/50">
                                <a href="pages/detail.php?id=<?php echo $photo['FotoID']; ?>" class="block aspect-square overflow-hidden">
                                    <img src="uploads/<?php echo htmlspecialchars($photo['LokasiFile']); ?>" 
                                         alt="<?php echo htmlspecialchars($photo['JudulFoto']); ?>"
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                </a>
                                
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <?php if (!empty($photo['profile_pic'])): ?>
                                                <img src="uploads/profiles/<?php echo htmlspecialchars($photo['profile_pic']); ?>" 
                                                     alt="<?php echo htmlspecialchars($photo['Username']); ?>"
                                                     class="w-8 h-8 rounded-full object-cover">
                                            <?php else: ?>
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-white text-xs"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="text-sm font-medium text-white"><?php echo htmlspecialchars($photo['Username']); ?></span>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            <?php 
                                            $upload_time = strtotime($photo['TanggalUnggah']);
                                            $time_diff = time() - $upload_time;
                                            
                                            if ($time_diff < 3600) {
                                                echo ceil($time_diff / 60) . ' menit lalu';
                                            } elseif ($time_diff < 86400) {
                                                echo ceil($time_diff / 3600) . ' jam lalu';
                                            } else {
                                                echo ceil($time_diff / 86400) . ' hari lalu';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-base font-semibold text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($photo['JudulFoto']); ?></h3>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400">
                                            <?php echo date('M d, Y', strtotime($photo['TanggalUnggah'])); ?>
                                        </span>
                                        <a href="pages/detail.php?id=<?php echo $photo['FotoID']; ?>" 
                                           class="text-blue-400 hover:text-blue-300 font-medium">
                                            Lihat Detail →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="text-center mt-12">
                        <a href="pages/explore.php" 
                           class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 font-semibold shadow-lg shadow-blue-500/20">
                            <i class="fas fa-compass mr-2"></i>
                            Jelajahi Semua Foto
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center border border-gray-700">
                            <i class="fas fa-images text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Belum Ada Foto</h3>
                        <p class="text-gray-400 max-w-md mx-auto">
                            Jadilah yang pertama mengupload foto dan inspirasi komunitas!
                        </p>
                        <?php if ($is_logged_in): ?>
                            <a href="pages/add_photo.php" 
                               class="mt-4 inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 font-semibold">
                                <i class="fas fa-plus mr-2"></i>
                                Upload Foto Pertama
                            </a>
                        <?php else: ?>
                            <a href="pages/register.php" 
                               class="mt-4 inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 font-semibold">
                                <i class="fas fa-user-plus mr-2"></i>
                                Daftar untuk Upload
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Features Section (hanya untuk non-login) -->
        <?php if (!$is_logged_in): ?>
        <section id="features" class="py-20 bg-gradient-to-b from-gray-900 to-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-white mb-16">
                    Mengapa Bergabung dengan <span class="text-blue-400">PhotoGallery</span>?
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-800/50 rounded-2xl p-8 text-center hover:bg-gray-700/50 transition-all duration-300 transform hover:-translate-y-2 border border-gray-700/50">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-cloud-upload-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Upload Tanpa Batas</h3>
                        <p class="text-gray-400">Upload foto Anda dengan kualitas tertinggi tanpa batasan jumlah</p>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-2xl p-8 text-center hover:bg-gray-700/50 transition-all duration-300 transform hover:-translate-y-2 border border-gray-700/50">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Komunitas Aktif</h3>
                        <p class="text-gray-400">Bergabung dengan komunitas fotografer yang saling mendukung dan menginspirasi</p>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-2xl p-8 text-center hover:bg-gray-700/50 transition-all duration-300 transform hover:-translate-y-2 border border-gray-700/50">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-heart text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Interaksi Sosial</h3>
                        <p class="text-gray-400">Dapatkan apresiasi dengan sistem like dan komentar pada foto Anda</p>
                    </div>
                </div>
                
                <!-- CTA Bottom -->
                <div class="text-center mt-16">
                    <a href="pages/register.php" 
                       class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg btn-glow inline-flex items-center space-x-3">
                        <i class="fas fa-user-plus"></i>
                        <span>Bergabung Sekarang - Gratis!</span>
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Call to Action Section -->
        <section class="py-20 bg-gradient-to-r from-blue-900/50 to-purple-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                        Siap Berbagi Momen Anda?
                    </h2>
                    <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                        Bergabunglah dengan ribuan fotografer lainnya dan bagikan karya terbaik Anda
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <?php if ($is_logged_in): ?>
                            <a href="pages/add_photo.php" 
                               class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg flex items-center space-x-3">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Upload Foto Baru</span>
                            </a>
                            <a href="pages/explore.php" 
                               class="border-2 border-white text-white px-8 py-4 rounded-xl hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg flex items-center space-x-3">
                                <i class="fas fa-compass"></i>
                                <span>Jelajahi Gallery</span>
                            </a>
                        <?php else: ?>
                            <a href="pages/register.php" 
                               class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg flex items-center space-x-3">
                                <i class="fas fa-user-plus"></i>
                                <span>Daftar Gratis</span>
                            </a>
                            <a href="pages/explore.php" 
                               class="border-2 border-white text-white px-8 py-4 rounded-xl hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:-translate-y-1 font-bold text-lg flex items-center space-x-3">
                                <i class="fas fa-compass"></i>
                                <span>Jelajahi Sekarang</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-camera text-white"></i>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-white">PhotoGallery</span>
                        <p class="text-xs text-gray-400 -mt-1">Share Your Moments</p>
                    </div>
                </div>
                
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-6 pt-6 text-center">
                <p class="text-gray-400">&copy; <?php echo date('Y'); ?> PhotoGallery. All rights reserved.</p>
                <p class="text-gray-500 text-sm mt-2">Made with <i class="fas fa-heart text-red-500"></i> for photography lovers</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
    // Navbar Mobile Menu Script
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenuButton.classList.toggle('active');
                mobileMenu.classList.toggle('active');
                
                if (mobileMenu.classList.contains('active')) {
                    mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                } else {
                    mobileMenu.style.maxHeight = '0';
                }
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                    mobileMenuButton.classList.remove('active');
                    mobileMenu.classList.remove('active');
                    mobileMenu.style.maxHeight = '0';
                }
            });
        }
        
        // Sticky navbar scroll effect
        const navbar = document.getElementById('mainNav');
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Smooth scroll untuk navigasi
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all photo cards
        document.querySelectorAll('.photo-card, .stat-card').forEach(el => {
            observer.observe(el);
        });

        // Add loading animation for images
        const bgImage = new Image();
        bgImage.src = 'assets/images/hero-bg.jpg';
        bgImage.onload = function() {
            document.querySelector('.hero-section').style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('${bgImage.src}')`;
        };
    });
    </script>
</body>
</html>