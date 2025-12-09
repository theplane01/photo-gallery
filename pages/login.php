<?php
$title = "Login";
include '../includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <!-- Left Side - Illustration/Info -->
        <div class="hidden md:block">
            <div class="max-w-lg">
                <div class="mb-8">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-camera text-white text-2xl"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome Back!</h1>
                    <p class="text-gray-600 text-lg">Sign in to access your photo gallery, manage albums, and connect with other photography enthusiasts.</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-images text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Personal Gallery</h3>
                            <p class="text-gray-600 text-sm">Access and organize all your photos in one place</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Community</h3>
                            <p class="text-gray-600 text-sm">Connect with other photographers and share your work</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-sign-in-alt text-white"></i>
                    </div>
                    Sign In to Your Account
                </h2>
            </div>
            
            <div class="p-8">
                <form class="space-y-6" action="../process/login_process.php" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-user text-blue-500 mr-2"></i>
                                Username *
                            </label>
                            <input id="username" name="username" type="text" required 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter your username">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-lock text-blue-500 mr-2"></i>
                                Password *
                            </label>
                            <input id="password" name="password" type="password" required 
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                            <span class="font-semibold"><?php echo htmlspecialchars($_GET['error']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        
                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-4 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-3"></i>
                                <span class="text-lg">Sign In</span>
                            </div>
                        </button>
                    </div>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Don't have an account?</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="register.php" 
                           class="inline-flex items-center justify-center w-full py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 hover:border-gray-400 transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create New Account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        
        if (!username || !password) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = `
            <div class="flex items-center justify-center">
                <i class="fas fa-spinner fa-spin mr-3"></i>
                <span class="text-lg">Signing In...</span>
            </div>
        `;
        submitBtn.disabled = true;
    });
});
</script>

<style>
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

.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>

<?php include '../includes/footer.php'; ?>