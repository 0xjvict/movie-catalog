<template>
  <div class="home">
    <!-- Header -->
    <div class="section-header">
      <h1 class="page-title">Descubra Filmes Incríveis</h1>
      <p class="page-subtitle">Pesquise e explore o mundo do cinema</p>
    </div>

    <!-- Barra de Pesquisa -->
    <div class="search-section">
      <div class="search-bar">
        <input
            type="text"
            placeholder="Digite o nome do filme..."
            v-model="query"
            @keyup.enter="handleSearch"
            class="search-input"
        >
        <button class="search-btn" @click="handleSearch" :disabled="!trimmedQuery">
          🔍
        </button>
      </div>
    </div>

    <!-- Erro -->
    <div v-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h3>Ops! Algo deu errado</h3>
      <p>{{ error }}</p>
      <button class="retry-btn" @click="handleSearch">Tentar Novamente</button>
    </div>

    <!-- Loading -->
    <div v-else-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Buscando filmes...</p>
    </div>

    <!-- Lista de Filmes -->
    <div v-else-if="movies.length > 0" class="movies-grid">
      <div
          v-for="movie in movies"
          :key="movie.movieId"
          class="movie-card"
          @click="goToMovieDetails(movie.movieId)"
      >
        <div class="movie-poster">
          <img
              :src="movie.posterPath !== 'N/A' ? movie.posterPath : getPlaceholderImage(movie.title)"
              :alt="movie.title"
              @error="handleImageError"
          />
          <div class="movie-overlay">
            <span class="view-details">Ver Detalhes</span>
          </div>
        </div>
        <div class="movie-info">
          <h3 class="movie-title">{{ movie.title }}</h3>
          <p class="movie-year">{{ movie.year }}</p>
          <p class="movie-type">{{ formatType(movie.Type) }}</p>
        </div>
      </div>
    </div>

    <!-- Estado Vazio -->
    <div v-else-if="trimmedQuery && !loading" class="empty-state">
      <div class="empty-icon">🎬</div>
      <h3>Nenhum filme encontrado</h3>
      <p>Tente pesquisar com outros termos: "{{ trimmedQuery }}"</p>
    </div>

    <!-- Estado Inicial -->
    <div v-else class="initial-state">
      <div class="initial-icon">🍿</div>
      <h3>Comece sua busca</h3>
      <p>Digite o nome de um filme para começar a explorar</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useMovies } from '@/composables/useMovies'

const router = useRouter()

const query = ref<string>('')
const { movies, loading, error, searchMovies } = useMovies()

const trimmedQuery = computed(() => query.value.trim())

let searchTimeout: number | null = null

watch(trimmedQuery, (newValue) => {
  if (newValue) {
    if (searchTimeout !== null) {
      clearTimeout(searchTimeout)
    }

    searchTimeout = setTimeout(() => {
      searchMovies(newValue)
      searchTimeout = null
    }, 500)
  }
})

const handleSearch = (): void => {
  if (trimmedQuery.value) {
    if (searchTimeout !== null) {
      clearTimeout(searchTimeout)
      searchTimeout = null
    }
    searchMovies(trimmedQuery.value)
  }
}

const goToMovieDetails = (movieId: string): void => {
  router.push(`/movie/${movieId}`)
}

const formatType = (type: string): string => {
  const typeMap: Record<string, string> = {
    'movie': 'Filme',
    'series': 'Série',
    'episode': 'Episódio'
  }
  return typeMap[type?.toLowerCase()] || type
}

const getPlaceholderImage = (title: string): string => {
  const colors = ['#667eea', '#764ba2', '#4ecdc4', '#44a08d', '#ff6b6b', '#ee5a52']
  const colorIndex = title.length % colors.length
  const color = colors[colorIndex].replace('#', '')
  return `https://placehold.co/300x450/${color}/white?text=${encodeURIComponent(title.substring(0, 15))}`
}

const handleImageError = (event: Event): void => {
  const target = event.target as HTMLImageElement
  const movieTitle = target.alt || 'Sem Poster'
  target.src = getPlaceholderImage(movieTitle)
}

const formatDate = (dateStr: string): string => {
  if (!dateStr) return 'Data desconhecida'
  return new Date(dateStr).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>

<style scoped>
.home {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.page-title {
  font-size: 3rem;
  font-weight: 800;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, .1);
}

.page-subtitle {
  font-size: 1.2rem;
  color: #666;
  font-weight: 400;
}

.search-section {
  display: flex;
  justify-content: center;
  margin-bottom: 3rem;
}

.search-bar {
  display: flex;
  width: 100%;
  max-width: 600px;
  position: relative;
}

.search-input {
  flex: 1;
  padding: 1.2rem 1.5rem;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  background: white;
  box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
  transition: all .3s ease;
  outline: none;
}

.search-input:focus {
  box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
  transform: translateY(-2px);
}

.search-btn {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  padding: 0.8rem;
  border: none;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border-radius: 50px;
  cursor: pointer;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .3s ease;
  font-size: 1.2rem;
}

.search-btn:hover:not(:disabled) {
  transform: translateY(-50%) scale(1.1);
}

.search-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Loading */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  gap: 1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Erro */
.error-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #e74c3c;
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #e74c3c;
}

.error-state p {
  font-size: 1.1rem;
  margin-bottom: 2rem;
  color: #666;
}

.retry-btn {
  padding: 1rem 2rem;
  border: none;
  border-radius: 50px;
  background: linear-gradient(135deg, #e74c3c, #c0392b);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all .3s ease;
  box-shadow: 0 5px 15px rgba(0, 0, 0, .2);
}

.retry-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, .3);
}

/* Grid de filmes */
.movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.movie-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
  transition: all .3s ease;
  cursor: pointer;
}

.movie-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
}

.movie-poster {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.movie-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .3s ease;
}

.movie-card:hover .movie-poster img {
  transform: scale(1.05);
}

.movie-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, .7);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity .3s ease;
}

.movie-card:hover .movie-overlay {
  opacity: 1;
}

.view-details {
  color: white;
  font-size: 1.1rem;
  font-weight: 600;
  padding: 1rem 2rem;
  border: 2px solid white;
  border-radius: 50px;
  transition: all .3s ease;
}

.view-details:hover {
  background: white;
  color: #333;
}

.movie-info {
  padding: 1.5rem;
}

.movie-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.movie-year {
  color: #666;
  font-size: 1rem;
  margin-bottom: 0.3rem;
}

.movie-type {
  color: #667eea;
  font-weight: 600;
  font-size: 0.9rem;
}

/* Estados vazios */
.empty-state, .initial-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
}

.empty-icon, .initial-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3, .initial-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #333;
}

.empty-state p, .initial-state p {
  font-size: 1.1rem;
}

/* Responsividade */
@media (max-width: 768px) {
  .home {
    padding: 1rem;
  }

  .page-title {
    font-size: 2.2rem;
  }

  .page-subtitle {
    font-size: 1rem;
  }

  .search-input {
    padding: 1rem 1.2rem;
    font-size: 1rem;
  }

  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
  }

  .movie-poster {
    height: 350px;
  }
}

@media (max-width: 480px) {
  .movies-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>