<template>
  <div class="explore-page">
    <!-- Header Section -->
    <div class="explore-header">
      <h1 class="explore-title">
        <i class="fas fa-compass"></i> Explore Gallery
      </h1>
      <p class="explore-subtitle">Discover amazing photos from our community</p>
    </div>

    <!-- Main Content -->
    <div class="explore-container">
      <!-- Search Section -->
      <div class="search-section">
        <div class="search-wrapper">
          <i class="fas fa-search"></i>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search photos by title or description..." 
            @keyup.enter="search"
            class="search-input"
          />
          <button @click="search" class="btn-search">
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>

        <!-- Filter Options -->
        <div class="filter-options">
          <button class="filter-btn active">
            <i class="fas fa-fire"></i> Trending
          </button>
          <button class="filter-btn">
            <i class="fas fa-star"></i> Most Liked
          </button>
          <button class="filter-btn">
            <i class="fas fa-clock"></i> Latest
          </button>
        </div>
      </div>

      <!-- Photos Grid -->
      <div v-if="photos.length > 0" class="photos-grid">
        <photo-card 
          v-for="photo in photos" 
          :key="photo.FotoID" 
          :photo="photo"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-inbox"></i>
        </div>
        <h2>No Photos Found</h2>
        <p>Try adjusting your search criteria</p>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination-section">
        <button 
          @click="previousPage" 
          :disabled="currentPage === 1"
          class="btn-pagination"
        >
          <i class="fas fa-chevron-left"></i> Previous
        </button>

        <div class="page-indicators">
          <button 
            v-for="page in paginationRange" 
            :key="page"
            @click="goToPage(page)"
            :class="{ active: currentPage === page }"
            class="page-number"
          >
            {{ page }}
          </button>
        </div>

        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="btn-pagination"
        >
          Next <i class="fas fa-chevron-right"></i>
        </button>
      </div>

      <!-- Page Info -->
      <div class="page-info">
        Page <span class="highlight">{{ currentPage }}</span> of <span class="highlight">{{ totalPages }}</span> 
        • Showing <span class="highlight">{{ photos.length }}</span> results
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { photoService } from '@/services/index.js'
import PhotoCard from '@/components/PhotoCard.vue'

const searchQuery = ref('')
const photos = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const limit = 12

const paginationRange = computed(() => {
  const delta = 2
  const range = []
  const rangeWithDots = []
  let l = 0

  for (let i = 1; i <= totalPages.value; i++) {
    if (i === 1 || i === totalPages.value || (i >= currentPage.value - delta && i <= currentPage.value + delta)) {
      range.push(i)
    }
  }

  range.forEach((i) => {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  })

  return rangeWithDots
})

onMounted(() => {
  loadPhotos()
})

const loadPhotos = async () => {
  try {
    const response = await photoService.getAllPhotos({
      search: searchQuery.value,
      page: currentPage.value,
      limit
    })
    photos.value = response.data.photos || []
    totalPages.value = response.data.pagination?.pages || 1
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (error) {
    console.error('Error loading photos:', error)
  }
}

const search = () => {
  currentPage.value = 1
  loadPhotos()
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    loadPhotos()
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    loadPhotos()
  }
}

const goToPage = (page) => {
  if (page !== '...') {
    currentPage.value = page
    loadPhotos()
  }
}
</script>

<style scoped>
.explore-page {
  min-height: 100vh;
  background: linear-gradient(to bottom, #0f172a, #1e293b, #0f172a);
  padding: 2rem 0;
}

/* Header Section */
.explore-header {
  text-align: center;
  padding: 4rem 2rem 3rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.explore-title {
  font-size: 2.5rem;
  color: white;
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.explore-title i {
  color: #3b82f6;
}

.explore-subtitle {
  color: rgba(255, 255, 255, 0.6);
  font-size: 1.1rem;
  margin: 0;
}

/* Container */
.explore-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 3rem 2rem;
}

/* Search Section */
.search-section {
  margin-bottom: 3rem;
  animation: fadeInUp 0.6s ease-out;
}

.search-wrapper {
  position: relative;
  display: flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.search-wrapper i {
  position: absolute;
  left: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(59, 130, 246, 0.6);
  font-size: 1rem;
}

.search-input {
  flex: 1;
  padding: 1rem 1rem 1rem 3rem;
  background: rgba(30, 41, 59, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.75rem;
  color: white;
  font-size: 1rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

.search-input:focus {
  outline: none;
  border-color: rgba(59, 130, 246, 0.6);
  background: rgba(59, 130, 246, 0.05);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-search {
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  border: none;
  border-radius: 0.75rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
}

.btn-search:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(139, 92, 246, 0.3);
}

/* Filter Options */
.filter-options {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(30, 41, 59, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.75rem;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.3s;
}

.filter-btn:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: rgba(59, 130, 246, 0.5);
  color: #3b82f6;
}

.filter-btn.active {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-color: transparent;
  color: white;
}

/* Photos Grid */
.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
  animation: fadeInUp 0.8s ease-out;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-icon {
  font-size: 4rem;
  color: rgba(59, 130, 246, 0.3);
  margin-bottom: 1rem;
}

.empty-state h2 {
  font-size: 1.5rem;
  color: white;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
}

/* Pagination */
.pagination-section {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.btn-pagination {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.75rem;
  color: white;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-pagination:hover:not(:disabled) {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-color: transparent;
  transform: translateY(-2px);
}

.btn-pagination:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-indicators {
  display: flex;
  gap: 0.5rem;
}

.page-number {
  width: 40px;
  height: 40px;
  padding: 0;
  background: rgba(30, 41, 59, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0.5rem;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 600;
}

.page-number:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: rgba(59, 130, 246, 0.5);
  color: #3b82f6;
}

.page-number.active {
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-color: transparent;
  color: white;
}

/* Page Info */
.page-info {
  text-align: center;
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

.highlight {
  color: #3b82f6;
  font-weight: 600;
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
@media (max-width: 768px) {
  .explore-header {
    padding: 2.5rem 1rem;
  }

  .explore-title {
    font-size: 1.75rem;
  }

  .explore-container {
    padding: 1.5rem 1rem;
  }

  .search-wrapper {
    flex-direction: column;
  }

  .btn-search {
    width: 100%;
  }

  .photos-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }

  .pagination-section {
    gap: 0.5rem;
  }

  .page-indicators {
    display: none;
  }
}
</style>
