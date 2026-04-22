<template>
  <!-- Enhanced Sticky Navigation -->
  <nav class="sticky-nav" :class="{ scrolled: isScrolled }" id="mainNav">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
      <div class="flex justify-between items-center py-4">
        <!-- Logo with Animation -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500 floating">
              <i class="fas fa-camera text-white text-lg"></i>
            </div>
            <div class="hidden md:block">
              <span class="text-xl font-bold text-white">PhotoGallery</span>
              <p class="text-xs text-gray-400 -mt-1">Share Your Moments</p>
            </div>
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-6">
          <template v-if="authStore.isLoggedIn">
            <!-- User sudah login -->
            <router-link to="/" class="text-gray-300 hover:text-white hover-glow font-medium transition-all duration-300 flex items-center space-x-1">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-home text-blue-400"></i>
              </div>
              <span class="hidden lg:inline">Beranda</span>
            </router-link>

            <router-link to="/explore" class="text-gray-300 hover:text-white hover-glow font-medium transition-all duration-300 flex items-center space-x-1">
              <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-compass text-purple-400"></i>
              </div>
              <span class="hidden lg:inline">Explore</span>
            </router-link>

            <router-link to="/add-photo" class="group relative bg-gradient-to-r from-blue-600 to-purple-700 text-white px-5 py-2.5 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold flex items-center space-x-2 shadow-lg shadow-blue-500/20">
              <i class="fas fa-plus"></i>
              <span class="hidden sm:inline">Upload Foto</span>
            </router-link>

            <!-- Enhanced Profile Dropdown -->
            <div class="relative dropdown" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
              <button class="flex items-center space-x-3 text-white hover:text-blue-400 focus:outline-none group transition-all duration-300">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative overflow-hidden">
                  <i class="fas fa-user text-white relative z-10"></i>
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-600/50 to-purple-700/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div class="text-left hidden lg:block">
                  <p class="font-medium text-sm">{{ authStore.user?.username }}</p>
                  <p class="text-xs text-gray-400">{{ authStore.user?.level || 'User' }}</p>
                </div>
                <i class="fas fa-chevron-down text-xs transform group-hover:rotate-180 transition-transform duration-300 hidden lg:inline" :class="{ 'rotate-180': dropdownOpen }"></i>
              </button>

              <!-- Dropdown Menu -->
              <div v-if="dropdownOpen" class="absolute right-0 mt-3 w-56 bg-gray-800/95 backdrop-blur-lg rounded-2xl shadow-2xl py-3 z-50 border border-gray-700/50 animate-fade-in">
                <div class="px-4 py-3 border-b border-gray-700/50">
                  <p class="text-white font-semibold">{{ authStore.user?.nama_lengkap || authStore.user?.username }}</p>
                  <p class="text-gray-400 text-sm truncate">{{ authStore.user?.email }}</p>
                </div>

                <router-link to="/dashboard" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-blue-600/20 hover:text-white transition-all duration-300 group/menu">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                    <i class="fas fa-tachometer-alt text-white text-sm"></i>
                  </div>
                  <span>Dashboard</span>
                  <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                </router-link>

                <router-link to="/profile" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-green-600/20 hover:text-white transition-all duration-300 group/menu">
                  <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                    <i class="fas fa-user text-white text-sm"></i>
                  </div>
                  <span>Profile</span>
                  <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                </router-link>

                <router-link to="/my-albums" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-purple-600/20 hover:text-white transition-all duration-300 group/menu">
                  <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                    <i class="fas fa-folder text-white text-sm"></i>
                  </div>
                  <span>My Albums</span>
                  <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                </router-link>

                <div class="border-t border-gray-700/50 my-2"></div>

                <button @click="logout" class="w-full flex items-center space-x-3 px-4 py-3 text-red-400 hover:bg-red-600/20 hover:text-red-300 transition-all duration-300 group/menu">
                  <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center group-hover/menu:scale-110 transition-transform duration-300">
                    <i class="fas fa-sign-out-alt text-white text-sm"></i>
                  </div>
                  <span>Logout</span>
                  <i class="fas fa-chevron-right text-xs ml-auto opacity-0 group-hover/menu:opacity-100 translate-x-2 group-hover/menu:translate-x-0 transition-all duration-300"></i>
                </button>
              </div>
            </div>
          </template>

          <template v-else>
            <!-- User belum login -->
            <router-link to="/" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Beranda</router-link>
            <router-link to="/explore" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Explore</router-link>
            <router-link to="/login" class="text-gray-300 hover:text-white hover-glow font-medium transition-colors duration-300">Login</router-link>
            <router-link to="/register" class="bg-gradient-to-r from-blue-600 to-purple-700 text-white px-6 py-2.5 rounded-xl hover:from-blue-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-0.5 font-semibold shadow-lg shadow-blue-500/20">
              Daftar Sekarang
            </router-link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden hamburger focus:outline-none" :class="{ active: mobileMenuOpen }">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>

      <!-- Mobile Navigation Menu -->
      <div v-if="mobileMenuOpen" class="mobile-dropdown md:hidden active">
        <div class="py-4 space-y-4 border-t border-gray-700/50 mt-2">
          <template v-if="authStore.isLoggedIn">
            <!-- Mobile Menu untuk logged in users -->
            <div class="flex items-center space-x-3 p-3 bg-gray-800/50 rounded-lg mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center relative overflow-hidden">
                <i class="fas fa-user text-white relative z-10"></i>
              </div>
              <div>
                <p class="font-medium text-white">{{ authStore.user?.username }}</p>
                <p class="text-xs text-gray-400">{{ authStore.user?.level || 'User' }}</p>
              </div>
            </div>

            <router-link to="/" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-home text-blue-400"></i>
              </div>
              <span>Beranda</span>
            </router-link>

            <router-link to="/explore" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-purple-600/20 hover:text-white rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-compass text-purple-400"></i>
              </div>
              <span>Explore</span>
            </router-link>

            <router-link to="/add-photo" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-plus text-blue-400"></i>
              </div>
              <span>Upload Foto</span>
            </router-link>

            <router-link to="/dashboard" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-tachometer-alt text-blue-400"></i>
              </div>
              <span>Dashboard</span>
            </router-link>

            <router-link to="/profile" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-green-600/20 hover:text-white rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-user text-green-400"></i>
              </div>
              <span>Profile</span>
            </router-link>

            <router-link to="/my-albums" class="flex items-center space-x-3 p-3 text-gray-300 hover:bg-purple-600/20 hover:text-white rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-folder text-purple-400"></i>
              </div>
              <span>My Albums</span>
            </router-link>

            <div class="border-t border-gray-700/50 my-2"></div>

            <button @click="logout" class="w-full flex items-center space-x-3 p-3 text-red-400 hover:bg-red-600/20 hover:text-red-300 rounded-lg transition-colors">
              <div class="w-8 h-8 bg-gradient-to-br from-red-500/20 to-red-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-sign-out-alt text-red-400"></i>
              </div>
              <span>Logout</span>
            </button>
          </template>

          <template v-else>
            <!-- Mobile Menu untuk guests -->
            <router-link to="/" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
              Beranda
            </router-link>
            <router-link to="/explore" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
              Explore
            </router-link>
            <router-link to="/login" class="block p-3 text-gray-300 hover:bg-blue-600/20 hover:text-white rounded-lg transition-colors">
              Login
            </router-link>
            <router-link to="/register" class="block p-3 bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-lg text-center hover:from-blue-700 hover:to-purple-800 transition-all duration-300">
              Daftar Sekarang
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore.js'
import { useRouter } from 'vue-router'
import { ref, onMounted, onUnmounted } from 'vue'

const authStore = useAuthStore()
const router = useRouter()
const mobileMenuOpen = ref(false)
const dropdownOpen = ref(false)
const isScrolled = ref(false)

const logout = () => {
  authStore.logout()
  router.push('/login')
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 10
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
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

.dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.mobile-dropdown {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease-out;
}

.mobile-dropdown.active {
  max-height: 500px;
}

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

.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
