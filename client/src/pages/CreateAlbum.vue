<template>
  <div class="create-album-page">
    <div class="container">
      <h1>Create New Album</h1>
      <form @submit.prevent="handleCreate" class="album-form">
        <div class="form-group">
          <label>Album Name</label>
          <input v-model="form.nama_album" type="text" required />
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea v-model="form.deskripsi" rows="5"></textarea>
        </div>

        <button type="submit" class="btn-submit">Create Album</button>
      </form>

      <div v-if="error" class="error">{{ error }}</div>
      <div v-if="success" class="success">Album created successfully! Redirecting...</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore.js'
import { albumService } from '../services/index.js'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({ nama_album: '', deskripsi: '' })
const error = ref('')
const success = ref('')

const handleCreate = async () => {
  try {
    error.value = ''
    success.value = ''

    if (!form.value.nama_album.trim()) {
      error.value = 'Album name is required'
      return
    }

    await albumService.createAlbum(form.value)
    success.value = 'Album created successfully!'

    setTimeout(() => {
      router.push('/my-albums')
    }, 2000)
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to create album'
  }
}
</script>

<style scoped>
.create-album-page {
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

.album-form {
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
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
  margin-top: 1rem;
}

.success {
  background: #efe;
  color: #3c3;
  padding: 1rem;
  border-radius: 4px;
  margin-top: 1rem;
}
</style>
