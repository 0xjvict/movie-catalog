<template>
  <div class="favorites-page">
    <!-- Header -->
    <section class="page-header">
      <div class="header-content">
        <div class="header-icon">❤️</div>
        <div class="header-text">
          <h1>Meus Favoritos</h1>
          <p v-if="favoritesCount > 0">{{ favoritesCount }} {{ favoritesCount === 1 ? 'filme favorito' : 'filmes favoritos' }}</p>
          <p v-else>Nenhum filme favorito ainda</p>
        </div>
      </div>
    </section>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Carregando seus favoritos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h3>Erro ao carregar favoritos</h3>
      <p>{{ error }}</p>
      <button @click="getFavorites" class="retry-btn">Tentar novamente</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="favorites.length === 0" class="empty-state">
      <div class="empty-icon">🎬</div>
      <h2>Sua lista está vazia</h2>
      <p>Que tal começar adicionando alguns filmes aos seus favoritos?</p>
      <router-link to="/movies" class="explore-btn">
        Explorar filmes
      </router-link>
    </div>

    <!-- Favorites Grid -->
    <div v-else class="favorites-container">
      <div class="favorites-grid">
        <div
            v-for="favorite in favorites"
            :key="favorite.id"
            class="movie-card"
            @click="goToMovie(favorite.id || favorite.movie_id)"
        >
          <div class="card-poster">
            <img
                :src="correctPosterUrl(favorite.posterPath) || '/placeholder-poster.jpg'"
                :alt="favorite.title || 'Filme'"
                class="poster-image"
                @error="handleImageError"
            />
            <div class="card-overlay">
              <div class="overlay-content">
                <button
                    @click.stop="removeFavorite(favorite.id || favorite.movie_id)"
                    class="remove-btn"
                    title="Remover dos favoritos"
                    :disabled="removingId === (favorite.id || favorite.movie_id)"
                >
                  <span v-if="removingId === (favorite.id || favorite.movie_id)">⏳</span>
                  <span v-else>❌</span>
                </button>
                <div class="movie-rating" v-if="favorite.voteAverage">
                  ⭐ {{ Math.round(favorite.voteAverage * 10) / 10 }}
                </div>
              </div>
            </div>
          </div>

          <div class="card-info">
            <h3 class="movie-title">{{ favorite.title || 'Título indisponível' }}</h3>
            <p class="movie-year" v-if="favorite.releaseDate">
              {{ getReleaseYear(favorite.releaseDate) }}
            </p>
            <p class="movie-genres" v-if="favorite.genres && favorite.genres.length">
              {{ getGenresText(favorite.genres) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Floating Action Button para ir para busca -->
    <button class="fab" @click="goToSearch" title="Buscar mais filmes">
      <span class="fab-icon">🔍</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFavorites } from '~/composables/useFavorites'

// Composables
const router = useRouter()
const {
  favorites,
  loading,
  error,
  favoritesCount,
  getFavorites,
  removeFromFavorites,
  clearError
} = useFavorites()

// Local state
const removingId = ref<number | null>(null)

// Methods
const removeFavorite = async (movieId: number): Promise<void> => {
  if (!movieId) return

  const confirmRemove = confirm('Tem certeza que deseja remover este filme dos favoritos?')
  if (!confirmRemove) return

  removingId.value = movieId

  try {
    const success = await removeFromFavorites(movieId)
    if (success) {
      console.log('Filme removido dos favoritos com sucesso!')
    } else {
      throw new Error('Falha ao remover favorito')
    }
  } catch (err) {
    console.error('Erro ao remover favorito:', err)
  } finally {
    removingId.value = null
  }
}

const goToMovie = (movieId: number): void => {
  if (movieId) {
    router.push(`/movies/${movieId}`)
  }
}

const goToSearch = (): void => {
  router.push('/movies')
}

const getReleaseYear = (releaseDate: string): string => {
  if (!releaseDate) return ''
  return new Date(releaseDate).getFullYear().toString()
}

const getGenresText = (genres: any[]): string => {
  if (!genres || genres.length === 0) return ''
  return genres.slice(0, 2).map(genre => genre.name).join(', ')
}

const correctPosterUrl = (url: string): string => {
  if (!url) return ''

  // Corrige URLs com paths repetidos (como no exemplo fornecido)
  if (url.includes('https://image.tmdb.org/t/p/w500/https://image.tmdb.org')) {
    return url.replace('https://image.tmdb.org/t/p/w500/https://image.tmdb.org', 'https://image.tmdb.org')
  }

  return url
}

const handleImageError = (event: Event): void => {
  const target = event.target as HTMLImageElement
  target.src = '/placeholder-poster.jpg'
}

// Lifecycle
onMounted(() => {
  getFavorites()
})
</script>

<style scoped>
.favorites-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding-bottom: 80px;
}

/* Header */
.page-header {
  padding: 2rem 0;
  background: rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(10px);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.header-icon {
  font-size: 3rem;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.header-text h1 {
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.header-text p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0.5rem 0 0;
}

/* Loading, Error, Empty States */
.loading-container, .error-container, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
  text-align: center;
  padding: 2rem;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top: 4px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon, .empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.retry-btn, .explore-btn {
  margin-top: 1rem;
  padding: 0.75rem 2rem;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50px;
  color: white;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-block;
}

.retry-btn:hover, .explore-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

/* Favorites Container */
.favorites-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.favorites-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

/* Movie Cards */
.movie-card {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
  cursor: pointer;
}

.movie-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.card-poster {
  position: relative;
  aspect-ratio: 2/3;
  overflow: hidden;
}

.poster-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.movie-card:hover .poster-image {
  transform: scale(1.05);
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
      to bottom,
      transparent 0%,
      transparent 50%,
      rgba(0, 0, 0, 0.8) 100%
  );
  opacity: 0;
  transition: opacity 0.3s ease;
}

.movie-card:hover .card-overlay {
  opacity: 1;
}

.overlay-content {
  position: absolute;
  top: 1rem;
  right: 1rem;
  bottom: 1rem;
  left: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.remove-btn {
  align-self: flex-end;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1.2rem;
}

.remove-btn:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.remove-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.movie-rating {
  align-self: flex-start;
  background: rgba(0, 0, 0, 0.7);
  padding: 0.5rem;
  border-radius: 10px;
  font-size: 0.9rem;
  font-weight: 600;
}

.card-info {
  padding: 1.5rem;
}

.movie-title {
  font-size: 1.2rem;
  font-weight: 700;
  margin: 0 0 0.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.movie-year, .movie-genres {
  margin: 0.25rem 0;
  opacity: 0.8;
  font-size: 0.9rem;
}

/* Floating Action Button */
.fab {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 1000;
}

.fab:hover {
  transform: scale(1.1);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
}

.fab-icon {
  font-size: 1.5rem;
  color: white;
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    text-align: center;
    padding: 0 1rem;
  }

  .header-text h1 {
    font-size: 2rem;
  }

  .favorites-container {
    padding: 1rem;
  }

  .favorites-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
  }

  .fab {
    bottom: 1rem;
    right: 1rem;
  }
}

@media (max-width: 480px) {
  .favorites-grid {
    grid-template-columns: 1fr;
  }
}
</style>