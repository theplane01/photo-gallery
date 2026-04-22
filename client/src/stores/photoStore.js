import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePhotoStore = defineStore('photo', () => {
  const photos = ref([])
  const currentPhoto = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const setPhotos = (newPhotos) => {
    photos.value = newPhotos
  }

  const setCurrentPhoto = (photo) => {
    currentPhoto.value = photo
  }

  const setLoading = (value) => {
    loading.value = value
  }

  const setError = (err) => {
    error.value = err
  }

  return {
    photos,
    currentPhoto,
    loading,
    error,
    setPhotos,
    setCurrentPhoto,
    setLoading,
    setError
  }
})
