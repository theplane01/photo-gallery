<?php
// Pastikan session sudah start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data user untuk foto profil (jika sudah login)
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config/database.php';
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhotoGallery - <?php echo $title ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

    /* Perbaikan untuk dropdown desktop */
.dropdown:hover .dropdown-menu {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
}

/* Pastikan dropdown tidak terpengaruh oleh mobile */
@media (min-width: 768px) {
    .dropdown-menu {
        display: block !important;
    }
}

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
        
        /* Smooth transitions */
        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Gradient text effect */
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        
        /* Hover glow effect */
        .hover-glow:hover {
            text-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }
        
        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
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
    </style>
</head>
<body class="bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    <!-- Enhanced Sticky Navigation -->
    <nav class="sticky-nav" id="mainNav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo with Animation -->
                <div class="flex items-center">
                    <a href="../index.php" class="flex items-center space-x-3 group">
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
                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                        <!-- User sudah login -->
                        <a href="../index.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-all duration-300 flex items-center space-x-1 <?php echo $current_page == 'index.php' ? 'text-blue-400 font-semibold' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-home text-blue-400"></i>
                            </div>
                            <span class="hidden lg:inline">Beranda</span>
                        </a>
                        
                        <a href="explore.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-all duration-300 flex items-center space-x-1 <?php echo $current_page == 'explore.php' ? 'text-purple-400 font-semibold' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-compass text-purple-400"></i>
                            </div>
                            <span class="hidden lg:inline">Explore</span>
                        </a>
                        
                        <a href="add_photo.php" class="group relative bg-gradient-to-r from-blue-600 to-purple-700 text-white px-5 py-2.5 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 shadow-lg shadow-blue-500/20">
                            <i class="fas fa-plus"></i>
                            <span class="hidden sm:inline">Upload Foto</span>
                        </a>
                        
                        <!-- Enhanced Profile Dropdown -->
                        <div class="relative dropdown">
                            <button class="flex items-center space-x-3 text-white hover:text-blue-400 focus:outline-none group transition-all duration-300">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative overflow-hidden">
                                    <?php if (!empty($profile_pic)): ?>
                                        <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_pic); ?>" 
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
                                
                                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-blue-600/20 hover:text-white transition-all duration-300 <?php echo $current_page == 'dashboard.php' ? 'bg-blue-600/20 text-white' : ''; ?> group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-tachometer-alt text-white text-sm"></i>
                                    </div>
                                    <span>Dashboard</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                                
                                <a href="profile.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-green-600/20 hover:text-white transition-all duration-300 <?php echo $current_page == 'profile.php' ? 'bg-green-600/20 text-white' : ''; ?> group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span>Profile</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                                
                                <a href="my_albums.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-purple-600/20 hover:text-white transition-all duration-300 <?php echo $current_page == 'my_albums.php' ? 'bg-purple-600/20 text-white' : ''; ?> group/menu">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                        <i class="fas fa-folder text-white text-sm"></i>
                                    </div>
                                    <span>My Albums</span>
                                    <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                </a>
                                
                                <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'Admin'): ?>
                                    <a href="admin.php" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-yellow-600/20 hover:text-white transition-all duration-300 <?php echo $current_page == 'admin.php' ? 'bg-yellow-600/20 text-white' : ''; ?> group/menu">
                                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                                            <i class="fas fa-cog text-white text-sm"></i>
                                        </div>
                                        <span>Admin Panel</span>
                                        <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <div class="border-t border-gray-700/50 my-2"></div>
                                
                                <a href="../process/logout.php" class="flex items-center space-x-3 px-4 py-3 text-red-400 hover:bg-red-600/20 hover:text-red-300 transition-all duration-300 group/menu">
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
                        <a href="../index.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Beranda</a>
                        <a href="login.php" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Login</a>
                        <a href="register.php" class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-2.5 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold shadow-lg shadow-blue-500/20">
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
                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                        <!-- Mobile Menu for logged in users -->
                        <div class="flex items-center space-x-3 p-3 bg-gray-800/50 rounded-lg mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative overflow-hidden">
                                <?php if (!empty($profile_pic)): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_pic); ?>" 
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
                        
                        <a href="../index.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors <?php echo $current_page == 'index.php' ? 'bg-blue-600/20 text-white' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-home text-blue-400"></i>
                            </div>
                            <span>Beranda</span>
                        </a>
                        
                        <a href="explore.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-purple-600/20 hover:text-white rounded-lg transition-colors <?php echo $current_page == 'explore.php' ? 'bg-purple-600/20 text-white' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-compass text-purple-400"></i>
                            </div>
                            <span>Explore</span>
                        </a>
                        
                        <a href="add_photo.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus text-blue-400"></i>
                            </div>
                            <span>Upload Foto</span>
                        </a>
                        
                        <a href="dashboard.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors <?php echo $current_page == 'dashboard.php' ? 'bg-blue-600/20 text-white' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tachometer-alt text-blue-400"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>
                        
                        <a href="profile.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-green-600/20 hover:text-white rounded-lg transition-colors <?php echo $current_page == 'profile.php' ? 'bg-green-600/20 text-white' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-green-400"></i>
                            </div>
                            <span>Profile</span>
                        </a>
                        
                        <a href="my_albums.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-purple-600/20 hover:text-white rounded-lg transition-colors <?php echo $current_page == 'my_albums.php' ? 'bg-purple-600/20 text-white' : ''; ?>">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-purple-400"></i>
                            </div>
                            <span>My Albums</span>
                        </a>
                        
                        <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'Admin'): ?>
                            <a href="admin.php" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-yellow-600/20 hover:text-white rounded-lg transition-colors <?php echo $current_page == 'admin.php' ? 'bg-yellow-600/20 text-white' : ''; ?>">
                                <div class="w-8 h-8 bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cog text-yellow-400"></i>
                                </div>
                                <span>Admin Panel</span>
                            </a>
                        <?php endif; ?>
                        
                        <div class="border-t border-gray-700/50 my-2"></div>
                        
                        <a href="../process/logout.php" class="flex items-center space-x-3 p-3 text-red-400 hover:bg-red-600/20 hover:text-red-300 rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500/20 to-red-600/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sign-out-alt text-red-400"></i>
                            </div>
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <!-- Mobile Menu for guests -->
                        <a href="../index.php" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            Beranda
                        </a>
                        <a href="login.php" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
                            Login
                        </a>
                        <a href="register.php" class="block p-3 bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-lg text-center hover:from-blue-700 hover:to-purple-800 transition-all duration-300">
                            Daftar Sekarang
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <script>
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
    
    // Close dropdowns when clicking elsewhere
    // Close dropdowns when clicking elsewhere (only for desktop)
document.addEventListener('click', function(event) {
    // Only run for desktop (not mobile)
    if (window.innerWidth >= 768) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) {
                    menu.style.opacity = '0';
                    menu.style.visibility = 'hidden';
                    menu.style.transform = 'translateY(-10px)';
                }
            }
        });
    }
});
});
</script>

    <!-- Main Content -->
    <main class="relative z-0">