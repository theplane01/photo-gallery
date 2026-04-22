import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAlbumStore = defineStore('album', () => {
  const albums = ref([])
  const currentAlbum = ref(null)
  const loading = ref(false)

  const setAlbums = (newAlbums) => {
    albums.value = newAlbums
  }

  const setCurrentAlbum = (album) => {
    currentAlbum.value = album
  }

  const setLoading = (value) => {
    loading.value = value
  }

  return {
    albums,
    currentAlbum,
    loading,
    setAlbums,
    setCurrentAlbum,
    setLoading
  }
})
