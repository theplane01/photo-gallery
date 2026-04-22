<template>
  <div class="my-albums-page">
    <div class="container">
      <div class="header">
        <h1>My Albums</h1>
        <router-link to="/create-album" class="btn-new">+ New Album</router-link>
      </div>

      <div v-if="albums.length === 0" class="empty">
        <p>No albums yet. Create one to get started!</p>
      </div>

      <div v-else class="albums-grid">
        <div v-for="album in albums" :key="album.AlbumID" class="album-card">
          <router-link :to="`/album/${album.AlbumID}`" class="album-link">
            <div class="album-cover">
              <p>📁</p>
              <span class="photo-count">{{ album.photo_count }} photos</span>
            </div>
            <h3>{{ album.NamaAlbum }}</h3>
          </router-link>
          <div class="album-actions">
            <router-link :to="`/edit-album/${album.AlbumID}`" class="btn-edit">Edit</router-link>
            <button @click="deleteAlbum(album.AlbumID)" class="btn-delete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore.js'
import { albumService } from '../services/index.js'

const router = useRouter()
const authStore = useAuthStore()
const albums = ref([])

onMounted(async () => {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }

  try {
    const response = await albumService.getUserAlbums(authStore.user.userId)
    albums.value = response.data
  } catch (error) {
    console.error('Error loading albums:', error)
  }
})

const deleteAlbum = async (albumId) => {
  if (!confirm('Are you sure?')) return

  try {
    await albumService.deleteAlbum(albumId)
    albums.value = albums.value.filter(a => a.AlbumID !== albumId)
  } catch (error) {
    console.error('Error deleting album:', error)
  }
}
</script>

<style scoped>
.my-albums-page {
  min-height: 100vh;
  padding: 2rem;
  background: #f5f5f5;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header h1 {
  margin: 0;
  color: #333;
}

.btn-new {
  display: inline-block;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
}

.empty {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 8px;
}

.albums-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
}

.album-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.album-card:hover {
  transform: translateY(-4px);
}

.album-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.album-cover {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  height: 150px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  position: relative;
}

.photo-count {
  position: absolute;
  bottom: 0.5rem;
  right: 0.5rem;
  background: rgba(0, 0, 0, 0.5);
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.75rem;
}

.album-card h3 {
  margin: 1rem;
  color: #333;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.album-actions {
  display: flex;
  gap: 0.5rem;
  padding: 0 1rem 1rem;
}

.btn-edit,
.btn-delete {
  flex: 1;
  padding: 0.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  text-decoration: none;
  text-align: center;
  transition: opacity 0.3s;
}

.btn-edit {
  background: #667eea;
  color: white;
}

.btn-delete {
  background: #ff6b6b;
  color: white;
}

.btn-edit:hover,
.btn-delete:hover {
  opacity: 0.8;
}
</style>
