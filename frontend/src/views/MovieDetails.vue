<template>
  <div class="movie-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-animation">
        <div class="film-strip"></div>
      </div>
      <h3>Carregando filme...</h3>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">🎬</div>
      <h2>Oops!</h2>
      <p>{{ error }}</p>
      <button @click="retryLoad" class="retry-btn">Tentar Novamente</button>
    </div>

    <!-- Movie Content -->
    <div v-else-if="movieData.id" class="movie-content">
      <!-- Header com backdrop blur -->
      <section class="movie-header">
        <div class="backdrop-container">
          <img
              :src="movieData.backdropPath"
              :alt="movieData.title"
              class="backdrop-image"
          />
          <div class="backdrop-overlay"></div>
        </div>

        <!-- Botão voltar -->
        <button class="back-btn" @click="$router.back()">
          ← Voltar
        </button>

        <div class="header-content">
          <div class="movie-poster-container">
            <div class="poster-frame">
              <img
                  :src="movieData.posterPath"
                  :alt="movieData.title"
                  class="poster-image"
                  @error="handleImageError"
              />
              <div class="poster-glow"></div>
            </div>
          </div>

          <div class="movie-info">
            <div class="title-group">
              <h1 class="movie-title">{{ movieData.title }}</h1>
              <span v-if="movieData.releaseDate" class="release-year">{{ getReleaseYear(movieData.releaseDate) }}</span>
            </div>

            <div class="meta-info">
              <span v-if="movieData.runtimeMinutes" class="duration">{{
                  formatDuration(movieData.runtimeMinutes)
                }}</span>
              <span v-if="movieData.genres?.length" class="genres">{{ getGenresText(movieData.genres) }}</span>
              <span v-if="movieData.originCountry" class="country">{{ movieData.originCountry }}</span>
            </div>

            <div v-if="movieData.tagline" class="tagline">"{{ movieData.tagline }}"</div>
          </div>
        </div>
      </section>

      <!-- Content Section -->
      <section class="movie-details">
        <div class="details-container">
          <!-- Rating and Actions -->
          <div class="rating-actions-section">
            <div class="rating-card">
              <div class="rating-visual">
                <svg class="rating-circle" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="45" class="rating-bg"></circle>
                  <circle
                      cx="50"
                      cy="50"
                      r="45"
                      class="rating-progress"
                      :style="{ strokeDasharray: `${movieData.voteAverage * 28.3} 283` }"
                  ></circle>
                </svg>
                <div class="rating-text">
                  <span class="rating-number">{{ Math.round(movieData.voteAverage * 10) }}</span>
                  <span class="rating-percent">%</span>
                </div>
              </div>
              <div class="rating-info">
                <p class="rating-label">⭐ Nota dos usuários</p>
                <p class="vote-count">{{ movieData.voteCount.toLocaleString() }} avaliações</p>
              </div>
            </div>

            <div class="action-buttons">
              <button
                  @click="toggleFavorite"
                  class="action-btn"
                  :class="{ 'is-favorited': isFavorited }"
                  :disabled="favoritesLoading || isProcessingFavorite"
              >
                <span class="btn-icon">
                  <svg v-if="isFavorited" xmlns="http://www.w3.org/2000/svg" fill="#ff6b6b" viewBox="0 0 24 24"
                       width="20" height="20"><path
                      d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#333" viewBox="0 0 24 24"
                       width="20" height="20"><path
                      d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </span>
                <span>{{ isFavorited ? 'Favoritado' : 'Favoritar' }}</span>
                <span v-if="favoritesLoading || isProcessingFavorite" class="loading-dots"></span>
              </button>
            </div>
          </div>

          <!-- Synopsis -->
          <div v-if="movieData.overview" class="synopsis-section">
            <h2 class="section-title">Sinopse</h2>
            <div class="synopsis-content">
              <p class="synopsis-text">{{ movieData.overview }}</p>
            </div>
          </div>

          <!-- Additional Info Cards -->
          <div class="info-cards">
            <div class="info-card">
              <h3>Informações</h3>
              <div class="info-list">
                <div class="info-item">
                  <span class="info-label">Lançamento</span>
                  <span class="info-value">{{ formatDate(movieData.releaseDate) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Duração</span>
                  <span class="info-value">{{ formatDuration(movieData.runtimeMinutes) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">País</span>
                  <span class="info-value">{{ movieData.originCountry }}</span>
                </div>
              </div>
            </div>

            <div v-if="movieData.genres?.length" class="info-card">
              <h3>Gêneros</h3>
              <div class="genres-list">
                <span v-for="genre in movieData.genres" :key="genre.id" class="genre-tag">{{ genre.name }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- No Movie Found -->
    <div v-else class="no-movie-state">
      <div class="no-movie-icon">🎭</div>
      <h2>Filme não encontrado</h2>
      <p>Não conseguimos encontrar as informações deste filme.</p>
    </div>
  </div>

  <!-- Toast de notificação -->
  <div v-if="showToast" class="toast" :class="toastType">{{ toastMessage }}</div>
</template>

<script setup lang="ts">
import {ref, computed, onMounted, watch} from 'vue'
import {useRoute} from 'vue-router'
import {useMovies} from '@/composables/useMovies.ts'
import {useFavorites} from '@/composables/useFavorites.ts'

interface Genre {
  id: number;
  name: string
}

interface MovieData {
  id: number;
  title: string;
  posterPath: string;
  backdropPath: string;
  releaseDate: string;
  originCountry: string;
  genres: Genre[];
  runtimeMinutes: number;
  tagline: string;
  overview: string;
  voteAverage: number;
  voteCount: number
}

interface MovieDetailsProps {
  id?: string | number
}

const props = defineProps<MovieDetailsProps>()

const {movie, loading, error, getMovie} = useMovies()
const {
  favorites,
  loading: favoritesLoading,
  toggleFavorite: toggleFavoriteApi,
  isFavorite: isFavoriteApi,
  getFavorites
} = useFavorites()
const route = useRoute()
const isProcessingFavorite = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const hasCheckedFavorites = ref(false)

const movieData = computed<MovieData>(() => movie.value || {
  id: 0, title: '', posterPath: '', backdropPath: '',
  releaseDate: '', originCountry: '', genres: [],
  runtimeMinutes: 0, tagline: '', overview: '',
  voteAverage: 0, voteCount: 0
})

const isFavorited = computed(() => {
  return hasCheckedFavorites.value && isFavoriteApi(movieData.value.id)
})

const getReleaseYear = (date: string) => date ? new Date(date).getFullYear().toString() : ''
const getGenresText = (genres: Genre[]) => genres.map(g => g.name).join(' • ')
const formatDuration = (m: number) => m ? `${Math.floor(m / 60)}h ${m % 60}min` : 'N/A'
const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('pt-BR', {
  year: 'numeric',
  month: 'long',
  day: 'numeric'
}) : 'N/A'

const showNotification = (message: string, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  setTimeout(() => showToast.value = false, 5000)
}


const toggleFavorite = async () => {
  if (isProcessingFavorite.value) return
  isProcessingFavorite.value = true
  try {
    console.log('📌 Estado local antes:', isFavorited.value)

    const result = await toggleFavoriteApi(movieData.value.id)
    console.log('📌 Resposta da API:', result)

    await getFavorites()
    console.log('📌 Estado local depois:', isFavorited.value)

    if (isFavorited.value) {
      showNotification('Filme favoritado com sucesso! ❤️', 'success')
    } else {
      showNotification('Filme removido dos favoritos! 💔', 'success')
    }

  } catch (error) {
    console.error('❌ Erro no toggleFavorite:', error)
    showNotification('Erro de conexão. Tente novamente.', 'error')
  } finally {
    isProcessingFavorite.value = false
  }
}

const handleImageError = (e: Event) => (e.target as HTMLImageElement).src = '/placeholder-poster.jpg'
const retryLoad = async () => {
  if (route.params.id) await getMovie(route.params.id)
}

watch(favoritesLoading, (newVal) => {
  if (!newVal) {
    hasCheckedFavorites.value = true
  }
})

onMounted(async () => {
  await getFavorites()
  hasCheckedFavorites.value = true

  if (route.params.id) {
    await getMovie(route.params.id)
  }
})
</script>


<style scoped>
.animate-heart {
  animation: popHeart 0.4s ease forwards;
}

@keyframes popHeart {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.4);
  }
  100% {
    transform: scale(1);
  }
}

.movie-detail-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

/* Loading, Error, No Movie */
.loading-state, .error-state, .no-movie-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  gap: 2rem;
  text-align: center;
  padding: 2rem;
}

.film-strip {
  width: 100%;
  height: 60px;
  background: linear-gradient(90deg, #333 0%, #666 25%, #333 50%, #666 75%, #333 100%);
  border-radius: 8px;
  animation: filmRoll 2s linear infinite;
}

@keyframes filmRoll {
  0% {
    transform: translateX(-20px);
  }
  100% {
    transform: translateX(20px);
  }
}

.retry-btn {
  margin-top: 1rem;
  padding: .75rem 2rem;
  background: rgba(255, 255, 255, .2);
  border: 2px solid rgba(255, 255, 255, .3);
  border-radius: 50px;
  color: white;
  cursor: pointer;
  transition: all .3s ease;
}

.retry-btn:hover {
  background: rgba(255, 255, 255, .3);
  transform: translateY(-2px);
}

/* Header Backdrop */
.movie-header {
  position: relative;
  height: 70vh;
  min-height: 600px;
  display: flex;
  align-items: end;
  overflow: hidden;
}

.backdrop-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.backdrop-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: blur(4px) brightness(0.5);
}

.backdrop-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(102, 126, 234, 0.7) 0%, rgba(118, 75, 162, 0.5) 100%);
}

.header-content {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  display: flex;
  gap: 3rem;
  align-items: end;
}

.poster-frame {
  position: relative;
  width: 280px;
}

.poster-image {
  width: 100%;
  height: auto;
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, .4);
  transition: transform .4s ease;
}

.poster-image:hover {
  transform: scale(1.05);
}

.poster-glow {
  position: absolute;
  top: -20px;
  left: -20px;
  right: -20px;
  bottom: -20px;
  background: linear-gradient(45deg, #667eea, #764ba2);
  border-radius: 30px;
  z-index: -1;
  filter: blur(20px);
  opacity: .5;
}

/* Movie Info */
.movie-info {
  flex: 1;
  padding-bottom: 2rem;
}

.title-group {
  margin-bottom: 1.5rem;
}

.movie-title {
  font-size: 4rem;
  font-weight: 800;
  line-height: 1.1;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, .5);
}

.release-year {
  font-size: 2rem;
  font-weight: 300;
  opacity: .8;
  margin-left: 1rem;
}

.meta-info {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
  font-size: 1.1rem;
}

.duration, .genres, .country {
  padding: .5rem 1rem;
  background: rgba(255, 255, 255, .2);
  border-radius: 25px;
  backdrop-filter: blur(10px);
}

.tagline {
  font-size: 1.3rem;
  font-style: italic;
  opacity: .9;
  font-weight: 300;
}

/* Movie Details */
.movie-details {
  background: white;
  color: #333;
  margin-top: -50px;
  border-radius: 50px 50px 0 0;
  position: relative;
  z-index: 3;
}

.details-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 2rem 2rem;
}

.rating-actions-section {
  display: flex;
  gap: 3rem;
  margin-bottom: 3rem;
  align-items: center;
}

.rating-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 1.5rem;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(102, 126, 234, .3);
}

.rating-visual {
  position: relative;
  width: 80px;
  height: 80px;
}

.rating-circle {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.rating-bg {
  fill: none;
  stroke: rgba(255, 255, 255, .3);
  stroke-width: 10;
}

.rating-progress {
  fill: none;
  stroke: #fff;
  stroke-width: 10;
  stroke-linecap: round;
  transition: stroke-dasharray .6s ease;
}

.rating-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}

.rating-number {
  font-size: 1.5rem;
  font-weight: 700;
}

.rating-percent {
  font-size: 1rem;
}

.rating-info {
  text-align: left;
}

.rating-label {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
}

.vote-count {
  font-size: .9rem;
  opacity: .9;
  margin: .5rem 0 0;
}

/* Action Button */
.action-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: .5rem;
  padding: .75rem 1.5rem;
  border: 2px solid #e2e8f0;
  border-radius: 50px;
  background: white;
  color: #333;
  cursor: pointer;
  transition: all .3s ease;
  font-weight: 500;
  position: relative;
}

.action-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
}

.action-btn:disabled {
  opacity: .6;
  cursor: not-allowed;
}

.action-btn.is-favorited {
  background: linear-gradient(135deg, #ff6b6b, #ee5a52);
  border-color: transparent;
  color: white;
}

.btn-icon {
  display: flex;
  align-items: center;
}

/* Loading Dots */
.loading-dots {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: currentColor;
  animation: loadingDots 1.4s infinite ease-in-out both;
  position: relative;
}

.loading-dots::before, .loading-dots::after {
  content: '';
  position: absolute;
  top: 0;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: currentColor;
  animation: loadingDots 1.4s infinite ease-in-out both;
}

.loading-dots::before {
  left: -15px;
  animation-delay: -0.32s;
}

.loading-dots::after {
  right: -15px;
  animation-delay: -0.16s;
}

@keyframes loadingDots {
  0%, 80%, 100% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Synopsis & Info Cards */
.synopsis-section {
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.synopsis-content {
  background: #f7fafc;
  padding: 2rem;
  border-radius: 20px;
  border-left: 5px solid #667eea;
}

.synopsis-text {
  font-size: 1.1rem;
  line-height: 1.6;
  color: #333;
}

.info-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
}

.info-card {
  flex: 1;
  min-width: 250px;
  background: #fff;
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
}

.info-card h3 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: .8rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  font-size: 1rem;
}

.genres-list {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
}

.genre-tag {
  background: #667eea;
  color: white;
  padding: .25rem .75rem;
  border-radius: 50px;
  font-size: .9rem;
}

/* Toast */
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #333;
  color: white;
  padding: 1rem 2rem;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, .2);
  opacity: 0.95;
  z-index: 9999;
}

.toast.success {
  background: linear-gradient(135deg, #667eea, #764ba2);
}

.toast.error {
  background: linear-gradient(135deg, #ff6b6b, #ee5a52);
}

.back-btn {
  position: absolute;
  top: 2rem;
  left: 2rem;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 50px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  z-index: 5;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;
}

.back-btn:hover {
  transform: translateY(-2px);
}
</style>
