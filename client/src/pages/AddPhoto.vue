<template>
  <div class="add-photo-page">
    <div class="container">
      <h1>Upload New Photo</h1>
      <form @submit.prevent="handleUpload" class="upload-form">
        <div class="form-group">
          <label>Photo Title</label>
          <input v-model="form.judul_foto" type="text" required />
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea v-model="form.deskripsi_foto" rows="4"></textarea>
        </div>

        <div class="form-group">
          <label>Album (optional)</label>
          <select v-model="form.album_id">
            <option value="">Select an album</option>
            <option v-for="album in userAlbums" :key="album.AlbumID" :value="album.AlbumID">
              {{ album.NamaAlbum }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label>Photo File</label>
          <input type="file" @change="handleFileChange" accept="image/*" required />
          <p v-if="form.file" class="file-name">Selected: {{ form.file.name }}</p>
        </div>

        <button type="submit" class="btn-submit">Upload Photo</button>
      </form>

      <div v-if="error" class="error">{{ error }}</div>
      <div v-if="success" class="success">Photo uploaded successfully!</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore.js'
import { photoService, albumService } from '../services/index.js'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({ judul_foto: '', deskripsi_foto: '', album_id: '', file: null })
const userAlbums = ref([])
const error = ref('')
const success = ref('')

onMounted(async () => {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }

  try {
    const response = await albumService.getUserAlbums(authStore.user.userId)
    userAlbums.value = response.data
  } catch (err) {
    console.error('Error loading albums:', err)
  }
})

const handleFileChange = (e) => {
  form.value.file = e.target.files[0]
}

const handleUpload = async () => {
  try {
    error.value = ''
    success.value = ''

    if (!form.value.file) {
      error.value = 'Please select a file'
      return
    }

    const formData = new FormData()
    formData.append('judul_foto', form.value.judul_foto)
    formData.append('deskripsi_foto', form.value.deskripsi_foto)
    formData.append('album_id', form.value.album_id || null)
    formData.append('file', form.value.file)

    await photoService.uploadPhoto(formData)
    success.value = 'Photo uploaded successfully!'
    form.value = { judul_foto: '', deskripsi_foto: '', album_id: '', file: null }

    setTimeout(() => {
      router.push('/my-photos')
    }, 2000)
  } catch (err) {
    error.value = err.response?.data?.error || 'Upload failed'
  }
}
</script>

<style scoped>
.add-photo-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 2rem;
}

.container {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.container h1 {
  color: #333;
  margin-bottom: 2rem;
}

.upload-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #333;
  font-weight: bold;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.file-name {
  margin-top: 0.5rem;
  color: #666;
  font-size: 0.9rem;
}

.btn-submit {
  padding: 0.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
  transition: opacity 0.3s;
}

.btn-submit:hover {
  opacity: 0.9;
}

.error {
  background: #fee;
  color: #c33;
  padding: 1rem;
  border-radius: 4px;
}

.success {
  background: #efe;
  color: #3c3;
  padding: 1rem;
  border-radius: 4px;
}
</style>
