import { createRouter, createWebHistory } from 'vue-router'

// Lazy load auth store
let useAuthStore
import('@/stores/authStore.js').then(m => {
  useAuthStore = m.useAuthStore
})

// Pages
import Home from '@/pages/Home.vue'
import Login from '@/pages/Login.vue'
import Register from '@/pages/Register.vue'
import AddPhoto from '@/pages/AddPhoto.vue'
import PhotoDetail from '@/pages/PhotoDetail.vue'
import MyAlbums from '@/pages/MyAlbums.vue'
import AlbumDetail from '@/pages/AlbumDetail.vue'
import CreateAlbum from '@/pages/CreateAlbum.vue'
import Explore from '@/pages/Explore.vue'

const routes = [
  { path: '/', component: Home, meta: { title: 'Home' } },
  { path: '/login', component: Login, meta: { title: 'Login' } },
  { path: '/register', component: Register, meta: { title: 'Register' } },
  { path: '/add-photo', component: AddPhoto, meta: { requiresAuth: true, title: 'Upload Photo' } },
  { path: '/photo/:fotoId', component: PhotoDetail, meta: { title: 'Photo Detail' } },
  { path: '/my-albums', component: MyAlbums, meta: { requiresAuth: true, title: 'My Albums' } },
  { path: '/album/:albumId', component: AlbumDetail, meta: { title: 'Album' } },
  { path: '/create-album', component: CreateAlbum, meta: { requiresAuth: true, title: 'Create Album' } },
  { path: '/explore', component: Explore, meta: { title: 'Explore' } }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from, next) => {
  if (!useAuthStore) {
    const m = await import('@/stores/authStore.js')
    useAuthStore = m.useAuthStore
  }
  
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login')
  } else {
    document.title = `${to.meta.title || 'Page'} - PhotoGallery`
    next()
  }
})

export default router
