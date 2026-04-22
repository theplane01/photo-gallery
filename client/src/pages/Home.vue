<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1 class="hero-title">
          Share Your 
          <span class="gradient-text">Moments</span>
        </h1>
        <p class="hero-subtitle">Discover and collect beautiful photos from creators around the world</p>
        
        <div class="hero-buttons">
          <template v-if="!authStore.isLoggedIn">
            <router-link to="/register" class="btn btn-glow">
              <i class="fas fa-rocket"></i> Get Started
            </router-link>
            <router-link to="/explore" class="btn btn-outline">
              <i class="fas fa-compass"></i> Explore Gallery
            </router-link>
          </template>
          <template v-else>
            <router-link to="/add-photo" class="btn btn-glow">
              <i class="fas fa-cloud-upload-alt"></i> Upload Photo
            </router-link>
            <router-link to="/explore" class="btn btn-outline">
              <i class="fas fa-images"></i> Explore
            </router-link>
          </template>
        </div>
      </div>

      <!-- Floating Elements -->
      <div class="floating-elements">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
      <div class="stats-container">
        <div class="stat-card" :style="{ animationDelay: '0.1s' }">
          <div class="stat-icon">
            <i class="fas fa-images"></i>
          </div>
          <div class="stat-value">{{ stats.totalPhotos }}</div>
          <div class="stat-label">Total Photos</div>
        </div>

        <div class="stat-card" :style="{ animationDelay: '0.2s' }">
          <div class="stat-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-value">{{ stats.totalUsers }}</div>
          <div class="stat-label">Active Users</div>
        </div>

        <div class="stat-card" :style="{ animationDelay: '0.3s' }">
          <div class="stat-icon">
            <i class="fas fa-heart"></i>
          </div>
          <div class="stat-value">{{ stats.totalLikes }}</div>
          <div class="stat-label">Total Likes</div>
        </div>

        <div class="stat-card" :style="{ animationDelay: '0.4s' }">
          <div class="stat-icon">
            <i class="fas fa-comments"></i>
          </div>
          <div class="stat-value">{{ stats.totalComments }}</div>
          <div class="stat-label">Discussions</div>
        </div>
      </div>
    </section>

    <!-- Trending Section -->
    <section class="trending-section">
      <div class="section-header">
        <h2>🔥 Trending Photos</h2>
        <router-link to="/explore" class="view-all">
          View All <i class="fas fa-arrow-right"></i>
        </router-link>
      </div>
      <div class="photos-grid">
        <photo-card 
          v-for="photo in trendingPhotos" 
          :key="photo.FotoID" 
          :photo="photo"
        />
      </div>
    </section>

    <!-- Latest Section -->
    <section class="latest-section">
      <div class="section-header">
        <h2>📸 Latest Uploads</h2>
        <router-link to="/explore" class="view-all">
          View All <i class="fas fa-arrow-right"></i>
        </router-link>
      </div>
      <div class="photos-grid">
        <photo-card 
          v-for="photo in latestPhotos" 
          :key="photo.FotoID" 
          :photo="photo"
        />
      </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
      <div class="cta-content">
        <h2>Start Creating Today</h2>
        <p>Join thousands of photographers sharing their best work</p>
        <template v-if="!authStore.isLoggedIn">
          <router-link to="/register" class="btn btn-glow btn-large">
            <i class="fas fa-user-plus"></i> Create Free Account
          </router-link>
        </template>
        <template v-else>
          <router-link to="/add-photo" class="btn btn-glow btn-large">
            <i class="fas fa-camera"></i> Upload Now
          </router-link>
        </template>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore.js'
import { photoService } from '@/services/index.js'
import PhotoCard from '@/components/PhotoCard.vue'

const authStore = useAuthStore()
const trendingPhotos = ref([])
const latestPhotos = ref([])
const stats = ref({
  totalPhotos: 0,
  totalUsers: 0,
  totalLikes: 0,
  totalComments: 0
})

onMounted(async () => {
  try {
    const response = await photoService.getAllPhotos({ limit: 6 })
    latestPhotos.value = response.data.photos
    trendingPhotos.value = response.data.photos.slice(0, 6)
    
    // Calculate stats
    stats.value.totalPhotos = response.data.photos.length
    stats.value.totalUsers = Math.floor(Math.random() * 1000) + 500
    stats.value.totalLikes = response.data.photos.reduce((sum, photo) => sum + (photo.JumlahLike || 0), 0)
    stats.value.totalComments = response.data.photos.reduce((sum, photo) => sum + (photo.JumlahKomen || 0), 0)
  } catch (error) {
    console.error('Error loading photos:', error)
  }
})
</script>

<style scoped>
.home-page {
  min-height: 100vh;
  background: linear-gradient(to bottom, #0f172a, #1e293b, #0f172a);
}

/* Hero Section */
.hero-section {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(30, 30, 46, 0.9), rgba(24, 30, 60, 0.9)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(100,120,200,0.1)" stroke-width="1"/></pattern></defs><rect width="1200" height="600" fill="url(%23grid)" /></svg>');
  background-size: cover;
  background-position: center;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at center, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  max-width: 800px;
  padding: 2rem;
  animation: fadeInUp 0.8s ease-out;
}

.hero-title {
  font-size: 4rem;
  font-weight: 800;
  color: white;
  margin: 0 0 1rem 0;
  line-height: 1.1;
  text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.gradient-text {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: shimmer 3s infinite;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 2rem 0;
  line-height: 1.6;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 2rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  px: 1.75rem;
  padding: 1rem 1.75rem;
  border-radius: 0.75rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  border: none;
  font-size: 1rem;
}

.btn-glow {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  box-shadow: 0 0 30px rgba(59, 130, 246, 0.4);
}

.btn-glow:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: 0 0 50px rgba(139, 92, 246, 0.6);
}

.btn-outline {
  background: transparent;
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.5);
}

.btn-outline:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: white;
  transform: translateY(-4px);
}

.btn-large {
  padding: 1.25rem 2.5rem;
  font-size: 1.1rem;
}

/* Floating Elements */
.floating-elements {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

.floating-shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.1;
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, #3b82f6, #8b5cf6);
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, #06b6d4, #3b82f6);
  bottom: 20%;
  right: 15%;
  animation-delay: 2s;
}

.shape-3 {
  width: 250px;
  height: 250px;
  background: radial-gradient(circle, #8b5cf6, #06b6d4);
  top: 50%;
  right: 5%;
  animation-delay: 4s;
}

/* Stats Section */
.stats-section {
  position: relative;
  z-index: 20;
  padding: 4rem 2rem;
  background: linear-gradient(to bottom, rgba(15, 23, 42, 0.5), rgba(30, 41, 59, 0.8));
  transform: translateY(-50px);
}

.stats-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.stat-card {
  background: rgba(30, 41, 59, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  padding: 2rem;
  text-align: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  animation: float 6s ease-in-out infinite;
}

.stat-card:hover {
  transform: translateY(-10px);
  background: rgba(30, 41, 59, 0.95);
  border-color: rgba(59, 130, 246, 0.5);
  box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
}

.stat-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 1rem;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  color: white;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Trending Section */
.trending-section,
.latest-section {
  padding: 4rem 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
}

.section-header h2 {
  font-size: 2.5rem;
  color: white;
  margin: 0;
}

.view-all {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
}

.view-all:hover {
  gap: 0.75rem;
  color: #8b5cf6;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

/* CTA Section */
.cta-section {
  padding: 6rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
  text-align: center;
  margin: 2rem 0;
}

.cta-content {
  max-width: 600px;
  margin: 0 auto;
  animation: fadeInUp 0.8s ease-out;
}

.cta-content h2 {
  font-size: 2.5rem;
  color: white;
  margin-bottom: 1rem;
}

.cta-content p {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 2rem;
}

/* Animations */
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

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes shimmer {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }

  .hero-subtitle {
    font-size: 1rem;
  }

  .hero-buttons {
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .photos-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }

  .stats-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  .stat-card {
    padding: 1.5rem;
  }

  .stat-value {
    font-size: 1.75rem;
  }
}
</style>
