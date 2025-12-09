<?php
$title = "Edit Profile";
include '../includes/header.php';
include '../includes/auth_check.php';

require_once '../config/database.php';
$user_id = $_SESSION['user_id'];

// Ambil data user
$sql = "SELECT * FROM gallery_user WHERE UserID = ?";
$stmt = $db->conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update data teks
    $nama_lengkap = $db->escape_string($_POST['nama_lengkap'] ?? '');
    $alamat = $db->escape_string($_POST['alamat'] ?? '');
    $bio = $db->escape_string($_POST['bio'] ?? '');
    
    // Handle foto profil upload
    $profile_pic = $user['profile_pic']; // Default ke yang lama
    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_pic'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        // Validasi file type
        if (in_array($file['type'], $allowed_types)) {
            // Validasi file size
            if ($file['size'] <= $max_size) {
                // Generate unique filename
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = "profile_" . $user_id . "_" . time() . "." . strtolower($file_extension);
                $upload_path = '../uploads/profiles/' . $filename;
                
                // Pastikan folder uploads/profiles ada
                if (!is_dir('../uploads/profiles')) {
                    mkdir('../uploads/profiles', 0755, true);
                }
                
                // Hapus foto profil lama jika ada
                if (!empty($user['profile_pic']) && file_exists('../uploads/profiles/' . $user['profile_pic'])) {
                    unlink('../uploads/profiles/' . $user['profile_pic']);
                }
                
                // Upload file baru
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $profile_pic = $filename;
                }
            }
        }
    }
    
    // Update query
    $update_sql = "UPDATE gallery_user SET NamaLengkap = ?, Alamat = ?, Bio = ?, profile_pic = ? WHERE UserID = ?";
    $update_stmt = $db->conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $nama_lengkap, $alamat, $bio, $profile_pic, $user_id);
    
    if ($update_stmt->execute()) {
        $success = "Profile updated successfully!";
        // Refresh user data
        $user['NamaLengkap'] = $nama_lengkap;
        $user['Alamat'] = $alamat;
        $user['Bio'] = $bio;
        $user['profile_pic'] = $profile_pic;
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>

<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100 py-8 transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="../index.php" class="text-sm text-gray-400 hover:text-blue-400 flex items-center transition-colors">
                        <i class="fas fa-home mr-2"></i> Home
                    </a>
                </li>
                <li>
                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                    <a href="dashboard.php" class="text-sm text-gray-400 hover:text-blue-400 transition-colors">Dashboard</a>
                </li>
                <li>
                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                    <a href="profile.php" class="text-sm text-gray-400 hover:text-blue-400 transition-colors">Profile</a>
                </li>
                <li>
                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                    <span class="text-sm text-blue-400 font-medium">Edit Profile</span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Profile Picture Upload -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-xl p-6 border border-gray-700">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-blue-900/50 to-blue-800/50 flex items-center justify-center mr-3">
                            <i class="fas fa-camera text-blue-400"></i>
                        </div>
                        Profile Picture
                    </h2>
                    
                    <!-- Current Profile Picture -->
                    <div class="text-center mb-6">
                        <div class="relative inline-block mb-4">
                            <div class="w-40 h-40 rounded-full border-4 border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 shadow-lg flex items-center justify-center mx-auto group hover:border-blue-500 transition-all duration-300">
                                <?php if (!empty($user['profile_pic'])): ?>
                                    <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                         alt="Profile" 
                                         class="w-full h-full rounded-full object-cover"
                                         id="currentProfilePic">
                                <?php else: ?>
                                    <i class="fas fa-user-circle text-7xl text-blue-500" id="defaultProfileIcon"></i>
                                <?php endif; ?>
                                <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-600/20 to-purple-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            <?php if (!empty($user['profile_pic'])): ?>
                                <a href="?remove_pic=1" 
                                   class="absolute top-0 right-0 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 shadow-lg transform hover:scale-110 transition-all duration-300"
                                   onclick="return confirm('Are you sure you want to remove your profile picture?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-gray-400 text-sm">
                            <?php if (!empty($user['profile_pic'])): ?>
                                Current profile picture
                            <?php else: ?>
                                No profile picture set
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Profile Picture Upload Form -->
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-6 text-center hover:border-blue-500 transition-all duration-300 bg-gray-900/50 group">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-cloud-upload-alt text-blue-500 text-2xl"></i>
                            </div>
                            <p class="text-gray-300 mb-4">Upload new profile picture</p>
                            
                            <div class="relative">
                                <input type="file" id="profile_pic" name="profile_pic" accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                       onchange="previewImage(event)">
                                <label for="profile_pic" 
                                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    Choose File
                                </label>
                            </div>
                            <p class="text-gray-400 text-xs mt-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                JPG, PNG, GIF • Max 2MB
                            </p>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-4 hidden">
                                <div class="relative w-32 h-32 mx-auto">
                                    <img id="preview" class="w-full h-full rounded-full object-cover border-4 border-gray-800 shadow-lg">
                                    <div class="absolute inset-0 rounded-full bg-gradient-to-t from-black/30 to-transparent"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info -->
                        <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/30 border border-blue-700/50 rounded-xl p-4">
                            <p class="text-blue-300 text-sm">
                                <i class="fas fa-lightbulb text-blue-400 mr-2"></i>
                                For best results, use a square image (1:1 ratio)
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Profile Information Form -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-700">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 p-6 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23ffffff" fill-opacity="0.4" fill-rule="evenodd"/%3E%3C/svg%3E'); background-size: 120px;"></div>
                        </div>
                        <div class="relative z-10">
                            <h1 class="text-2xl font-bold text-white flex items-center">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-blue-900/40 to-blue-800/40 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-edit text-blue-400"></i>
                                </div>
                                Edit Profile Information
                            </h1>
                            <p class="text-gray-300 mt-2">Update your personal information</p>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <?php if (isset($success)): ?>
                            <div class="bg-gradient-to-r from-green-900/30 to-green-800/30 border-l-4 border-green-500 p-4 rounded-r-lg mb-6 animate-fade-in">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-green-900/50 flex items-center justify-center mr-3">
                                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                    </div>
                                    <p class="text-green-300 font-medium"><?php echo $success; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="bg-gradient-to-r from-red-900/30 to-red-800/30 border-l-4 border-red-500 p-4 rounded-r-lg mb-6 animate-fade-in">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-red-900/50 flex items-center justify-center mr-3">
                                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                                    </div>
                                    <p class="text-red-300 font-medium"><?php echo $error; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                                            <div class="w-8 h-8 rounded-md bg-gradient-to-r from-blue-900/30 to-blue-800/30 flex items-center justify-center mr-2">
                                                <i class="fas fa-user text-blue-400 text-sm"></i>
                                            </div>
                                            Username
                                        </label>
                                        <input type="text" value="<?php echo htmlspecialchars($user['Username']); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-700 rounded-xl bg-gray-900 text-gray-300 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all" disabled>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-info-circle mr-1"></i> Username cannot be changed
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                                            <div class="w-8 h-8 rounded-md bg-gradient-to-r from-blue-900/30 to-blue-800/30 flex items-center justify-center mr-2">
                                                <i class="fas fa-envelope text-blue-400 text-sm"></i>
                                            </div>
                                            Email
                                        </label>
                                        <input type="email" value="<?php echo htmlspecialchars($user['Email']); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-700 rounded-xl bg-gray-900 text-gray-300 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all" disabled>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-info-circle mr-1"></i> Email cannot be changed
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                                            <div class="w-8 h-8 rounded-md bg-gradient-to-r from-purple-900/30 to-purple-800/30 flex items-center justify-center mr-2">
                                                <i class="fas fa-id-card text-purple-400 text-sm"></i>
                                            </div>
                                            Full Name
                                        </label>
                                        <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($user['NamaLengkap'] ?? ''); ?>" 
                                               class="w-full px-4 py-3 border-2 border-gray-700 rounded-xl bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all placeholder-gray-500"
                                               placeholder="Enter your full name">
                                    </div>
                                </div>
                                
                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                                            <div class="w-8 h-8 rounded-md bg-gradient-to-r from-green-900/30 to-green-800/30 flex items-center justify-center mr-2">
                                                <i class="fas fa-map-marker-alt text-green-400 text-sm"></i>
                                            </div>
                                            Address
                                        </label>
                                        <textarea name="alamat" rows="3" 
                                                  class="w-full px-4 py-3 border-2 border-gray-700 rounded-xl bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none placeholder-gray-500"
                                                  placeholder="Enter your address"><?php echo htmlspecialchars($user['Alamat'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-300 mb-2 flex items-center">
                                            <div class="w-8 h-8 rounded-md bg-gradient-to-r from-orange-900/30 to-orange-800/30 flex items-center justify-center mr-2">
                                                <i class="fas fa-align-left text-orange-400 text-sm"></i>
                                            </div>
                                            Bio
                                        </label>
                                        <textarea name="bio" rows="4" 
                                                  class="w-full px-4 py-3 border-2 border-gray-700 rounded-xl bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none placeholder-gray-500"
                                                  placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['Bio'] ?? ''); ?></textarea>
                                        <div class="flex justify-between items-center mt-2 text-sm">
                                            <span class="text-gray-500 flex items-center">
                                                <i class="fas fa-keyboard mr-1"></i>
                                                Maximum 500 characters
                                            </span>
                                            <span id="charCount" class="<?php echo strlen($user['Bio'] ?? '') > 500 ? 'text-red-400 font-bold' : 'text-gray-400'; ?>">
                                                <?php echo strlen($user['Bio'] ?? ''); ?>/500
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden file input for profile pic (will be handled by separate form) -->
                            <input type="file" id="profile_pic_main" name="profile_pic" accept="image/*" class="hidden">
                            
                            <!-- Form Actions -->
                            <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 pt-6 border-t border-gray-700">
                                <a href="profile.php" 
                                   class="bg-gradient-to-r from-gray-700 to-gray-800 text-white px-8 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 w-full sm:w-auto justify-center border border-gray-600 hover:border-gray-500 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Cancel</span>
                                </a>
                                
                                <button type="submit" 
                                        class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 w-full sm:w-auto justify-center shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-save"></i>
                                    <span>Save All Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Password Change Section -->
                <div class="mt-8 bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-red-900/50 to-red-800/50 flex items-center justify-center mr-3">
                            <i class="fas fa-lock text-red-400"></i>
                        </div>
                        Change Password
                    </h2>
                    <p class="text-gray-400 mb-4">Want to change your password? Click the button below.</p>
                    <a href="change_password.php" class="inline-flex items-center bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Profile Picture Preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for bio
    const bioTextarea = document.querySelector('textarea[name="bio"]');
    const charCount = document.getElementById('charCount');
    
    if (bioTextarea && charCount) {
        bioTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + '/500';
            
            if (count > 500) {
                charCount.classList.add('text-red-400', 'font-bold');
                charCount.classList.remove('text-gray-400');
            } else {
                charCount.classList.remove('text-red-400', 'font-bold');
                charCount.classList.add('text-gray-400');
            }
        });
    }
    
    // Profile picture preview
    window.previewImage = function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File is too large. Maximum size is 2MB.');
                event.target.value = '';
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Only JPG, PNG, GIF are allowed.');
                event.target.value = '';
                return;
            }
            
            // Show preview
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                
                // Also update the main form's file input
                const mainInput = document.getElementById('profile_pic_main');
                if (mainInput) {
                    // Create a new DataTransfer object
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    mainInput.files = dataTransfer.files;
                }
            };
            
            reader.readAsDataURL(file);
        }
    };
    
    // Form submission loading state
    const form = document.querySelector('form[method="POST"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Show loading state on submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    <span>Saving Changes...</span>
                `;
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                
                // Restore button after 5 seconds (in case of error)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }, 5000);
            }
        });
    }
});
</script>

<style>
/* Custom animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* File upload styling */
input[type="file"]::-webkit-file-upload-button {
    visibility: hidden;
}

/* Custom scrollbar for dark theme */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Focus styles for dark theme */
input:focus, textarea:focus, select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Placeholder color for dark theme */
::placeholder {
    color: #6b7280;
}

:-ms-input-placeholder {
    color: #6b7280;
}

::-ms-input-placeholder {
    color: #6b7280;
}
</style>

<?php 
// Handle remove profile picture
if (isset($_GET['remove_pic']) && $_GET['remove_pic'] == 1) {
    if (!empty($user['profile_pic']) && file_exists('../uploads/profiles/' . $user['profile_pic'])) {
        unlink('../uploads/profiles/' . $user['profile_pic']);
    }
    
    $remove_sql = "UPDATE gallery_user SET profile_pic = NULL WHERE UserID = ?";
    $remove_stmt = $db->conn->prepare($remove_sql);
    $remove_stmt->bind_param("i", $user_id);
    $remove_stmt->execute();
    
    header("Location: edit_profile.php?success=Profile picture removed successfully");
    exit();
}

include '../includes/footer.php'; 
?>