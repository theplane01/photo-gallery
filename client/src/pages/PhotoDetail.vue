<template>
  <div class="photo-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loader">
        <div class="spinner"></div>
        <p>Loading photo...</p>
      </div>
    </div>

    <!-- Photo Detail Content -->
    <div v-else-if="photo">
      <!-- Breadcrumb -->
      <div class="breadcrumb">
        <router-link to="/" class="breadcrumb-link">
          <i class="fas fa-home"></i> Home
        </router-link>
        <i class="fas fa-chevron-right"></i>
        <router-link to="/explore" class="breadcrumb-link">
          Explore
        </router-link>
        <i class="fas fa-chevron-right"></i>
        <span class="breadcrumb-current">{{ photo.JudulFoto }}</span>
      </div>

      <!-- Main Container -->
      <div class="detail-container">
        <!-- Photo Viewer -->
        <div class="photo-viewer">
          <div class="image-wrapper">
            <img :src="photo.LokasiFile" :alt="photo.JudulFoto" class="detail-image" />
            <div class="image-overlay">
              <span class="view-counter">
                <i class="fas fa-eye"></i> Views
              </span>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="detail-sidebar">
          <!-- Photo Header -->
          <div class="photo-header-section">
            <h1 class="photo-title">{{ photo.JudulFoto }}</h1>
            <div class="photo-meta">
              <div class="meta-item">
                <span class="meta-label">Album:</span>
                <span class="meta-value">{{ photo.NamaAlbum || 'Uncategorized' }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Uploaded:</span>
                <span class="meta-value">{{ formatDate(photo.TanggalUnggah) }}</span>
              </div>
            </div>
          </div>

          <!-- User Card -->
          <div class="user-card">
            <div class="user-avatar">
              <img 
                :src="photo.ProfilePicture || 'https://via.placeholder.com/50'" 
                :alt="photo.Username"
              />
            </div>
            <div class="user-info">
              <p class="user-name">{{ photo.Username }}</p>
              <p class="user-role">{{ photo.level || 'Photographer' }}</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button @click="toggleLike" :class="['btn-action', 'btn-like', { liked: isLiked }]">
              <i class="fas fa-heart"></i>
              <span>{{ likeCount }}</span>
            </button>
            <button class="btn-action btn-comment">
              <i class="fas fa-comment"></i>
              <span>{{ commentCount }}</span>
            </button>
            <button class="btn-action btn-share">
              <i class="fas fa-share-alt"></i>
              <span>Share</span>
            </button>
          </div>

          <!-- Description -->
          <div class="description-section">
            <h3>Description</h3>
            <p class="description-text">{{ photo.DeskripsiFoto || 'No description provided' }}</p>
          </div>

          <!-- Photo Info Stats -->
          <div class="info-stats">
            <div class="stat">
              <span class="stat-number">{{ likeCount }}</span>
              <span class="stat-label">Likes</span>
            </div>
            <div class="stat">
              <span class="stat-number">{{ commentCount }}</span>
              <span class="stat-label">Comments</span>
            </div>
            <div class="stat">
              <span class="stat-number">{{ photo.TanggalUnggah }}</span>
              <span class="stat-label">Shared</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Comments Section -->
      <section class="comments-section">
        <div class="comments-container">
          <h2 class="comments-title">
            <i class="fas fa-comments"></i> Discussion ({{ comments.length }})
          </h2>

          <!-- Add Comment Form -->
          <div v-if="authStore.isLoggedIn" class="add-comment-form">
            <div class="comment-input-wrapper">
              <img 
                :src="authStore.user?.avatar || 'https://via.placeholder.com/40'" 
                :alt="authStore.user?.username"
                class="comment-avatar"
              />
              <div class="input-group">
                <input 
                  v-model="newComment" 
                  placeholder="Share your thoughts..." 
                  @keyup.enter="addComment"
                  class="comment-input"
                />
                <button @click="addComment" class="btn-send">
                  <i class="fas fa-paper-plane"></i> Post
                </button>
              </div>
            </div>
          </div>

          <div v-else class="login-prompt">
            <p><i class="fas fa-lock"></i> Sign in to leave a comment</p>
            <router-link to="/login" class="btn-login">Sign In</router-link>
          </div>

          <!-- Comments List -->
          <div class="comments-list">
            <div 
              v-for="comment in comments" 
              :key="comment.KomentarID" 
              class="comment-item"
            >
              <div class="comment-avatar-wrapper">
                <img 
                  :src="comment.ProfilePicture || 'https://via.placeholder.com/40'" 
                  :alt="comment.Username"
                  class="comment-avatar"
                />
              </div>

              <div class="comment-content">
                <div class="comment-header">
                  <p class="comment-user">{{ comment.Username }}</p>
                  <span class="comment-date">{{ formatDate(comment.TanggalKomentar) }}</span>
                </div>
                <p class="comment-text">{{ comment.IsiKomentar }}</p>

                <div class="comment-actions">
                  <button class="action-link">
                    <i class="fas fa-thumbs-up"></i> Like
                  </button>
                  <button class="action-link">
                    <i class="fas fa-reply"></i> Reply
                  </button>
                  <button 
                    v-if="authStore.user?.UserID === comment.UserID" 
                    @click="deleteComment(comment.KomentarID)"
                    class="action-link delete"
                  >
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </div>
              </div>
            </div>

            <div v-if="comments.length === 0" class="no-comments">
              <i class="fas fa-inbox"></i>
              <p>No comments yet. Be the first to comment!</p>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Error State -->
    <div v-else class="error-container">
      <div class="error-content">
        <i class="fas fa-exclamation-triangle"></i>
        <h2>Photo Not Found</h2>
        <p>The photo you're looking for doesn't exist or has been removed.</p>
        <router-link to="/explore" class="btn-back">
          <i class="fas fa-arrow-left"></i> Back to Gallery
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore.js'
import { photoService, likeService, commentService } from '@/services/index.js'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const photo = ref(null)
const comments = ref([])
const isLiked = ref(false)
const likeCount = ref(0)
const commentCount = ref(0)
const newComment = ref('')
const loading = ref(true)

onMounted(async () => {
  try {
    const fotoId = route.params.fotoId

    const photoResponse = await photoService.getPhotoById(fotoId)
    photo.value = photoResponse.data
    likeCount.value = photoResponse.data.JumlahLike || 0
    commentCount.value = photoResponse.data.JumlahKomen || 0

    const commentsResponse = await commentService.getPhotoComments(fotoId)
    comments.value = commentsResponse.data.comments || []

    if (authStore.isLoggedIn) {
      const likeStatus = await likeService.getLikeStatus(fotoId)
      isLiked.value = likeStatus.data.liked
    }
  } catch (error) {
    console.error('Error loading photo:', error)
  } finally {
    loading.value = false
  }
})

const toggleLike = async () => {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }

  try {
    await likeService.toggleLike(photo.value.FotoID)
    isLiked.value = !isLiked.value
    likeCount.value += isLiked.value ? 1 : -1
  } catch (error) {
    console.error('Error toggling like:', error)
  }
}

const addComment = async () => {
  if (!newComment.value.trim() || !authStore.isLoggedIn) return

  try {
    await commentService.addComment(photo.value.FotoID, { isi_komentar: newComment.value })
    newComment.value = ''

    const commentsResponse = await commentService.getPhotoComments(photo.value.FotoID)
    comments.value = commentsResponse.data.comments || []
    commentCount.value = comments.value.length
  } catch (error) {
    console.error('Error adding comment:', error)
  }
}

const deleteComment = async (komentarId) => {
  try {
    await commentService.deleteComment(komentarId)
    comments.value = comments.value.filter(c => c.KomentarID !== komentarId)
    commentCount.value--
  } catch (error) {
    console.error('Error deleting comment:', error)
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'short',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.photo-detail-page {
  min-height: 100vh;
  background: linear-gradient(to bottom, #0f172a, #1e293b, #0f172a);
  padding: 2rem 0;
}

/* Breadcrumb */
.breadcrumb {
  max-width: 1400px;
  margin: 0 auto 2rem;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

.breadcrumb-link {
  color: #3b82f6;
  text-decoration: none;
  transition: color 0.3s;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.breadcrumb-link:hover {
  color: #8b5cf6;
}

.breadcrumb-current {
  color: rgba(255, 255, 255, 0.8);
}

/* Loading State */
.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 400px;
}

.loader {
  text-align: center;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(59, 130, 246, 0.2);
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loader p {
  color: rgba(255, 255, 255, 0.6);
}

/* Error State */
.error-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 400px;
}

.error-content {
  text-align: center;
  color: white;
}

.error-content i {
  font-size: 3rem;
  color: #ef4444;
  margin-bottom: 1rem;
}

.error-content h2 {
  font-size: 1.5rem;
  margin: 0.5rem 0;
}

.error-content p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 1.5rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  text-decoration: none;
  border-radius: 0.75rem;
  transition: all 0.3s;
}

.btn-back:hover {
  transform: translateY(-2px);
}

/* Detail Container */
.detail-container {
  max-width: 1400px;
  margin: 0 auto 3rem;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 2fr 1.2fr;
  gap: 2rem;
  align-items: start;
}

/* Photo Viewer */
.photo-viewer {
  animation: fadeInUp 0.6s ease-out;
}

.image-wrapper {
  position: relative;
  border-radius: 1.5rem;
  overflow: hidden;
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.1);
  aspect-ratio: 4/3;
}

.detail-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.image-overlay {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(10px);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Sidebar */
.detail-sidebar {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  animation: fadeInUp 0.8s ease-out;
}

/* Photo Header Section */
.photo-header-section {
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  padding: 1.5rem;
}

.photo-title {
  font-size: 1.75rem;
  color: white;
  margin: 0 0 1rem 0;
  line-height: 1.3;
}

.photo-meta {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.meta-item {
  display: flex;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.meta-label {
  color: rgba(255, 255, 255, 0.6);
}

.meta-value {
  color: #3b82f6;
  font-weight: 600;
}

/* User Card */
.user-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  padding: 1.5rem;
}

.user-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid rgba(59, 130, 246, 0.5);
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-info {
  flex: 1;
}

.user-name {
  margin: 0;
  font-weight: 600;
  color: white;
}

.user-role {
  margin: 0;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}

/* Action Buttons */
.action-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 0.75rem;
}

.btn-action {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 0.3rem;
  padding: 1rem;
  background: rgba(30, 41, 59, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.75rem;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.3s;
  font-size: 0.9rem;
}

.btn-action:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: rgba(59, 130, 246, 0.5);
  color: #3b82f6;
}

.btn-like.liked {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.5);
  color: #fca5a5;
}

/* Description */
.description-section {
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1rem;
  padding: 1.5rem;
}

.description-section h3 {
  margin: 0 0 1rem 0;
  color: white;
  font-size: 1.1rem;
}

.description-text {
  margin: 0;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
}

/* Info Stats */
.info-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat {
  background: rgba(30, 41, 59, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.75rem;
  padding: 1rem;
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 0.3rem;
}

.stat-label {
  display: block;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.6);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Comments Section */
.comments-section {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem 2rem;
}

.comments-container {
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 1.5rem;
  padding: 2rem;
}

.comments-title {
  margin: 0 0 2rem 0;
  color: white;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Add Comment */
.add-comment-form {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.comment-input-wrapper {
  display: flex;
  gap: 1rem;
}

.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(59, 130, 246, 0.5);
}

.input-group {
  flex: 1;
  display: flex;
  gap: 0.5rem;
}

.comment-input {
  flex: 1;
  padding: 0.75rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.5rem;
  color: white;
  font-size: 0.95rem;
}

.comment-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

.comment-input:focus {
  outline: none;
  border-color: rgba(59, 130, 246, 0.5);
  background: rgba(59, 130, 246, 0.05);
}

.btn-send {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-send:hover {
  transform: translateY(-2px);
}

.login-prompt {
  text-align: center;
  padding: 2rem;
  background: rgba(59, 130, 246, 0.1);
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 0.75rem;
  margin-bottom: 2rem;
}

.login-prompt p {
  color: rgba(255, 255, 255, 0.7);
  margin: 0 0 1rem 0;
}

.btn-login {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  text-decoration: none;
  border-radius: 0.5rem;
  transition: all 0.3s;
}

.btn-login:hover {
  transform: translateY(-2px);
}

/* Comments List */
.comments-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.comment-item {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 0.75rem;
  transition: all 0.3s;
}

.comment-item:hover {
  background: rgba(255, 255, 255, 0.05);
}

.comment-avatar-wrapper {
  flex-shrink: 0;
}

.comment-content {
  flex: 1;
  min-width: 0;
}

.comment-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.comment-user {
  margin: 0;
  color: white;
  font-weight: 600;
}

.comment-date {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}

.comment-text {
  margin: 0 0 0.75rem 0;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.5;
  word-break: break-word;
}

.comment-actions {
  display: flex;
  gap: 1rem;
  font-size: 0.85rem;
}

.action-link {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  transition: color 0.3s;
  padding: 0;
}

.action-link:hover {
  color: #3b82f6;
}

.action-link.delete:hover {
  color: #ef4444;
}

.no-comments {
  text-align: center;
  padding: 2rem;
  color: rgba(255, 255, 255, 0.5);
}

.no-comments i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
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

/* Responsive */
@media (max-width: 1024px) {
  .detail-container {
    grid-template-columns: 1fr;
  }

  .image-wrapper {
    aspect-ratio: 16/9;
  }
}

@media (max-width: 768px) {
  .photo-detail-page {
    padding: 1rem 0;
  }

  .breadcrumb {
    padding: 0 1rem;
    font-size: 0.8rem;
  }

  .detail-container {
    padding: 0 1rem;
    gap: 1rem;
  }

  .photo-title {
    font-size: 1.3rem;
  }

  .action-buttons {
    grid-template-columns: 1fr;
  }

  .comments-container {
    padding: 1.5rem;
  }

  .comment-input-wrapper {
    flex-direction: column;
  }

  .input-group {
    flex-direction: column;
  }

  .comments-section {
    padding: 0 1rem 1rem;
  }
}
</style>
