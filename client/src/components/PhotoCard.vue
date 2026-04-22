<template>
  <div class="photo-card" @click="goToDetail">
    <!-- Image Container -->
    <div class="photo-image-container">
      <img 
        :src="photo.LokasiFile" 
        :alt="photo.JudulFoto" 
        class="photo-image"
        loading="lazy"
      />
      
      <!-- Gradient Overlay -->
      <div class="gradient-overlay"></div>
      
      <!-- Image Overlay Actions -->
      <div class="image-overlay">
        <button class="action-btn view-btn">
          <i class="fas fa-eye"></i>
          <span>View</span>
        </button>
        <button class="action-btn like-btn">
          <i class="fas fa-heart"></i>
        </button>
      </div>

      <!-- Badge -->
      <div class="badge-trending">
        <i class="fas fa-fire"></i> Trending
      </div>
    </div>

    <!-- Info Section -->
    <div class="photo-info">
      <!-- User Profile -->
      <div class="user-section">
        <div class="user-avatar">
          <img 
            :src="photo.ProfilePicture || 'https://via.placeholder.com/40'" 
            :alt="photo.Username"
            class="avatar"
          />
        </div>
        <div class="user-details">
          <p class="username">{{ photo.Username }}</p>
          <p class="upload-date">{{ formatDate(photo.TanggalUpload) }}</p>
        </div>
      </div>

      <!-- Photo Title -->
      <h3 class="photo-title">{{ photo.JudulFoto }}</h3>

      <!-- Photo Description (if available) -->
      <p v-if="photo.DeskripsiFoto" class="photo-description">
        {{ truncateText(photo.DeskripsiFoto, 100) }}
      </p>

      <!-- Stats -->
      <div class="photo-stats">
        <div class="stat-item">
          <i class="fas fa-heart"></i>
          <span>{{ photo.JumlahLike || 0 }}</span>
        </div>
        <div class="stat-item">
          <i class="fas fa-comment"></i>
          <span>{{ photo.JumlahKomen || 0 }}</span>
        </div>
        <div class="stat-item">
          <i class="fas fa-share-alt"></i>
          <span>Share</span>
        </div>
      </div>

      <!-- Album Tag -->
      <div v-if="photo.NamaAlbum" class="album-tag">
        <i class="fas fa-folder"></i> {{ photo.NamaAlbum }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

defineProps({
  photo: {
    type: Object,
    required: true
  }
})

const goToDetail = () => {
  router.push(`/photo/${photo.FotoID}`)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'short',
    month: 'short',
    year: 'numeric'
  })
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<style scoped>
.photo-card {
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  animation: fadeInUp 0.6s ease-out;
}

.photo-card:hover {
  transform: translateY(-12px);
  border-color: rgba(59, 130, 246, 0.5);
  box-shadow: 0 20px 40px rgba(59, 130, 246, 0.2);
  background: rgba(30, 41, 59, 0.95);
}

/* Image Container */
.photo-image-container {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(100, 120, 200, 0.1), rgba(139, 92, 246, 0.1));
}

.photo-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.photo-card:hover .photo-image {
  transform: scale(1.1) rotate(1deg);
}

.gradient-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0) 0%,
    rgba(0, 0, 0, 0.3) 70%,
    rgba(0, 0, 0, 0.6) 100%
  );
  opacity: 0;
  transition: opacity 0.3s;
}

.photo-card:hover .gradient-overlay {
  opacity: 1;
}

/* Image Overlay */
.image-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  background: rgba(0, 0, 0, 0.4);
  opacity: 0;
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.photo-card:hover .image-overlay {
  opacity: 1;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 3rem;
  height: 3rem;
  background: rgba(59, 130, 246, 0.9);
  color: white;
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
  padding: 0;
}

.action-btn:hover {
  background: rgba(139, 92, 246, 1);
  transform: scale(1.1);
}

.like-btn {
  width: 2.5rem;
  height: 2.5rem;
  background: rgba(239, 68, 68, 0.9);
}

.like-btn:hover {
  background: rgba(239, 68, 68, 1);
}

/* Badge */
.badge-trending {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: linear-gradient(135deg, #ff6b6b, #ff8787);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 2rem;
  font-size: 0.75rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
  animation: pulse 2s infinite;
}

/* Info Section */
.photo-info {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* User Section */
.user-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid rgba(59, 130, 246, 0.5);
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
}

.avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.username {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 600;
  color: white;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.upload-date {
  margin: 0;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Photo Title */
.photo-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: white;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Description */
.photo-description {
  margin: 0;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Stats */
.photo-stats {
  display: flex;
  gap: 1rem;
  padding: 1rem 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.3s;
}

.stat-item:hover {
  color: #3b82f6;
  transform: scale(1.05);
}

.stat-item i {
  font-size: 0.9rem;
}

/* Album Tag */
.album-tag {
  font-size: 0.75rem;
  color: rgba(59, 130, 246, 0.8);
  background: rgba(59, 130, 246, 0.1);
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  width: fit-content;
}

/* Animations */
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

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

@media (max-width: 768px) {
  .photo-card {
    border-radius: 0.75rem;
  }

  .photo-info {
    padding: 1rem;
  }

  .photo-title {
    font-size: 1rem;
  }

  .action-btn {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 0.9rem;
  }

  .badge-trending {
    font-size: 0.65rem;
    padding: 0.4rem 0.75rem;
  }
}
</style>
