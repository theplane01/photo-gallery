<?php
$title = "Register";
include '../includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-user-plus text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Join Our Community</h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">Create your account to start sharing photos, creating albums, and connecting with photography enthusiasts</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side - Benefits -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-3"></i>
                        Why Join Us?
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-cloud-upload-alt text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Unlimited Uploads</h3>
                                <p class="text-gray-600 text-sm mt-1">Share as many photos as you want</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-folder text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Organize Albums</h3>
                                <p class="text-gray-600 text-sm mt-1">Create unlimited albums to organize your photos</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Community</h3>
                                <p class="text-gray-600 text-sm mt-1">Connect with other photographers worldwide</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-yellow-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Secure Storage</h3>
                                <p class="text-gray-600 text-sm mt-1">Your photos are safe and private</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Already have an account? 
                            <a href="login.php" class="text-blue-600 font-semibold hover:text-blue-800">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-700 px-8 py-6">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            Create Your Account
                        </h2>
                        <p class="text-blue-100 mt-2">Fill in your details to get started</p>
                    </div>
                    
                    <div class="p-8">
                        <form class="space-y-8" action="../process/register_process.php" method="POST" id="registerForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                                            Username *
                                        </label>
                                        <input id="username" name="username" type="text" required 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                               placeholder="Choose a username">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                            Email Address *
                                        </label>
                                        <input id="email" name="email" type="email" required 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                               placeholder="your@email.com">
                                    </div>
                                    
                                    <div>
                                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-id-card text-purple-500 mr-2"></i>
                                            Full Name
                                        </label>
                                        <input id="nama_lengkap" name="nama_lengkap" type="text" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                               placeholder="Your full name (optional)">
                                    </div>
                                </div>
                                
                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                                            Address
                                        </label>
                                        <textarea id="alamat" name="alamat" rows="4"
                                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"
                                                  placeholder="Your address (optional)"></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-lock text-red-500 mr-2"></i>
                                            Password *
                                        </label>
                                        <input id="password" name="password" type="password" required 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                               placeholder="Create a password">
                                    </div>
                                    
                                    <div>
                                        <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-lock text-red-500 mr-2"></i>
                                            Confirm Password *
                                        </label>
                                        <input id="confirm_password" name="confirm_password" type="password" required 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                               placeholder="Confirm your password">
                                    </div>
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

                            <?php if (isset($_GET['success'])): ?>
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <span class="font-semibold"><?php echo htmlspecialchars($_GET['success']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-shield-alt text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-blue-800 mb-2">Privacy & Security</h4>
                                        <p class="text-blue-700 text-sm">Your information is encrypted and secure. We never share your personal data with third parties.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="terms" class="ml-3 block text-sm text-gray-700">
                                    I agree to the 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">Terms of Service</a> 
                                    and 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">Privacy Policy</a>
                                </label>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 pt-6 border-t border-gray-200">
                                <a href="login.php" 
                                   class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-8 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 w-full sm:w-auto justify-center">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Back to Login</span>
                                </a>
                                
                                <button type="submit" 
                                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-10 py-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 shadow-lg hover:shadow-xl w-full sm:w-auto justify-center">
                                    <i class="fas fa-user-plus text-xl"></i>
                                    <span class="text-lg">Create Account</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Password match validation
    function validatePasswords() {
        if (password.value && confirmPassword.value) {
            if (password.value !== confirmPassword.value) {
                confirmPassword.classList.add('border-red-500', 'bg-red-50');
                return false;
            } else {
                confirmPassword.classList.remove('border-red-500', 'bg-red-50');
                confirmPassword.classList.add('border-green-500', 'bg-green-50');
                return true;
            }
        }
        return true;
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    form.addEventListener('submit', function(e) {
        if (!validatePasswords()) {
            e.preventDefault();
            alert('Passwords do not match!');
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = `
            <div class="flex items-center justify-center">
                <i class="fas fa-spinner fa-spin mr-3 text-xl"></i>
                <span class="text-lg">Creating Account...</span>
            </div>
        `;
        submitBtn.disabled = true;
    });
    
    // Add focus effects to form inputs
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-200', 'rounded-xl');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-200', 'rounded-xl');
        });
    });
});
</script>

<style>
@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

input:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.sticky {
    position: sticky;
}
</style>

<?php include '../includes/footer.php'; ?>