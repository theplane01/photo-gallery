<template>
  <div class="album-detail-page">
    <div v-if="loading" class="loading">Loading...</div>
    <template v-else-if="album">
      <div class="album-header">
        <div class="header-content">
          <h1>{{ album.NamaAlbum }}</h1>
          <p class="creator">by <strong>{{ album.Username }}</strong></p>
          <p class="description">{{ album.Deskripsi }}</p>
          <p class="photo-count">📷 {{ album.photo_count }} photos</p>
        </div>
      </div>

      <div class="album-photos-section">
        <h2>Photos in this album</h2>
        <div v-if="album.photos.length === 0" class="empty">
          <p>No photos in this album yet</p>
        </div>
        <div v-else class="photos-grid">
          <photo-card 
            v-for="photo in album.photos" 
            :key="photo.FotoID" 
            :photo="photo"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { albumService } from '../services/index.js'
import PhotoCard from '../components/PhotoCard.vue'

const route = useRoute()
const album = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const albumId = route.params.albumId
    const response = await albumService.getAlbumById(albumId)
    album.value = response.data
  } catch (error) {
    console.error('Error loading album:', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.album-detail-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.album-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 2rem;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
}

.header-content h1 {
  font-size: 2.5rem;
  margin: 0 0 0.5rem 0;
}

.creator {
  font-size: 1rem;
  margin: 0 0 1rem 0;
  opacity: 0.9;
}

.description {
  font-size: 1.1rem;
  margin: 0 0 1rem 0;
  line-height: 1.6;
}

.photo-count {
  font-size: 0.95rem;
  opacity: 0.9;
}

.album-photos-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 2rem;
}

.album-photos-section h2 {
  color: #333;
  margin-bottom: 2rem;
}

.empty {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 8px;
  color: #999;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
}

.loading {
  text-align: center;
  padding: 4rem;
  color: #666;
}

@media (max-width: 768px) {
  .header-content h1 {
    font-size: 1.8rem;
  }

  .photos-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}
</style>
