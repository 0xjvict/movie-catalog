<template>
  <div class="favorites">
    <!-- Header -->
    <div class="section-header">
      <h1 class="page-title">Meus Filmes Favoritos</h1>
      <p class="page-subtitle">Todos os filmes que você adorou</p>
    </div>

    <!-- Filtros -->
    <div v-if="favorites.length > 0 && !loading && !error" class="filters-section">
      <div class="filters-header">
        <h3>Filtrar por Gênero</h3>
        <button
            v-if="selectedGenre"
            class="clear-filter-btn"
            @click="clearFilter"
        >
          Limpar Filtro
        </button>
      </div>

      <div class="genres-filter">
        <button
            v-for="genre in availableGenres"
            :key="genre.id"
            class="genre-chip"
            :class="{ active: selectedGenre === genre.id }"
            @click="toggleGenreFilter(genre.id)"
        >
          {{ genre.name }}
        </button>
      </div>

      <div v-if="selectedGenre" class="filter-info">
        <p>
          Mostrando <strong>{{ filteredFavorites.length }}</strong> de
          <strong>{{ favorites.length }}</strong> filmes
          <span v-if="selectedGenreName">no gênero <strong>{{ selectedGenreName }}</strong></span>
        </p>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Carregando favoritos...</p>
    </div>

    <!-- Erro -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h3>Ops! Algo deu errado</h3>
      <p>{{ error }}</p>
      <button class="retry-btn" @click="loadFavorites">Tentar Novamente</button>
    </div>

    <!-- Lista de Favoritos Vazia -->
    <div v-else-if="favorites.length === 0" class="empty-state">
      <div class="empty-icon">❤️</div>
      <h3>Nenhum filme favorito</h3>
      <p>Você ainda não adicionou nenhum filme aos favoritos</p>
      <router-link to="/" class="explore-btn">
        Explorar Filmes
      </router-link>
    </div>

    <!-- Lista de Favoritos -->
    <div v-else class="movies-grid">
      <div
          v-for="favorite in filteredFavorites"
          :key="favorite.id"
          class="movie-card"
          @click="goToMovieDetails(favorite.id)"
      >
        <div class="movie-poster">
          <img
              :src="favorite.posterPath || getPlaceholderImage(favorite.title)"
              :alt="favorite.title"
              @error="handleImageError"
          />
          <div class="movie-overlay">
            <span class="view-details">Ver Detalhes</span>
          </div>
        </div>
        <div class="movie-info">
          <h3 class="movie-title">{{ favorite.title }}</h3>
          <p class="movie-year">{{ formatDate(favorite.releaseDate) }}</p>
          <div class="movie-genres">
            <span
                v-for="genre in favorite.genres.slice(0, 2)"
                :key="genre.id"
                class="genre-tag"
            >
              {{ genre.name }}
            </span>
            <span v-if="favorite.genres.length > 2" class="genre-more">
              +{{ favorite.genres.length - 2 }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Nenhum resultado para o filtro -->
    <div v-if="selectedGenre && filteredFavorites.length === 0 && !loading" class="no-results">
      <div class="no-results-icon">🔍</div>
      <h3>Nenhum filme encontrado</h3>
      <p>Não há filmes no gênero selecionado</p>
      <button class="clear-filter-btn large" @click="clearFilter">
        Limpar Filtro
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFavorites, type Favorite } from '@/composables/useFavorites'

const router = useRouter()
const { favorites, loading, error, getFavorites } = useFavorites()
const selectedGenre = ref<number | null>(null)

// Carregar favoritos quando o componente montar
onMounted(async () => {
  await loadFavorites()
})

const loadFavorites = async (): Promise<void> => {
  await getFavorites()
}

// Extrair todos os gêneros disponíveis dos filmes favoritos
const availableGenres = computed(() => {
  const genresMap = new Map<number, { id: number; name: string }>()

  favorites.value.forEach((favorite: Favorite) => {
    favorite.genres?.forEach((genre: { id: number; name: string }) => {
      if (!genresMap.has(genre.id)) {
        genresMap.set(genre.id, genre)
      }
    })
  })

  return Array.from(genresMap.values()).sort((a, b) => a.name.localeCompare(b.name))
})

// Filtrar filmes baseado no gênero selecionado
const filteredFavorites = computed(() => {
  if (!selectedGenre.value) {
    return favorites.value
  }

  return favorites.value.filter((favorite: Favorite) =>
      favorite.genres?.some((genre: { id: number }) => genre.id === selectedGenre.value)
  )
})

// Nome do gênero selecionado
const selectedGenreName = computed(() => {
  if (!selectedGenre.value) return null
  const genre = availableGenres.value.find(g => g.id === selectedGenre.value)
  return genre ? genre.name : null
})

// Toggle do filtro de gênero
const toggleGenreFilter = (genreId: number) => {
  if (selectedGenre.value === genreId) {
    selectedGenre.value = null
  } else {
    selectedGenre.value = genreId
  }
}

// Limpar filtro
const clearFilter = () => {
  selectedGenre.value = null
}

const goToMovieDetails = (movieId: number): void => {
  router.push(`/movie/${movieId}`)
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
.favorites {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  min-height: 80vh;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.page-title {
  font-size: 3rem;
  font-weight: 800;
  background: linear-gradient(135deg, #ff6b6b, #ee5a52);
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

/* Filtros */
.filters-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.filters-header h3 {
  font-size: 1.2rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.clear-filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #e74c3c;
  border-radius: 20px;
  background: transparent;
  color: #e74c3c;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.clear-filter-btn:hover {
  background: #e74c3c;
  color: white;
}

.clear-filter-btn.large {
  padding: 1rem 2rem;
  margin-top: 1rem;
}

.genres-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
  margin-bottom: 1rem;
}

.genre-chip {
  padding: 0.6rem 1.2rem;
  border: 2px solid #667eea;
  border-radius: 25px;
  background: transparent;
  color: #667eea;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.genre-chip:hover {
  background: #667eea;
  color: white;
  transform: translateY(-1px);
}

.genre-chip.active {
  background: #667eea;
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.filter-info {
  text-align: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 12px;
  color: #666;
}

.filter-info p {
  margin: 0;
  font-size: 0.95rem;
}

/* Gêneros nos cards */
.movie-genres {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  margin-top: 0.5rem;
}

.genre-tag {
  padding: 0.3rem 0.6rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.genre-more {
  padding: 0.3rem 0.6rem;
  background: #e9ecef;
  color: #666;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Nenhum resultado */
.no-results {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
}

.no-results-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.no-results h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #333;
}

.no-results p {
  font-size: 1.1rem;
  margin-bottom: 2rem;
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
  border-top: 4px solid #ff6b6b;
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

/* Estado Vazio */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #333;
}

.empty-state p {
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.explore-btn {
  display: inline-block;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  text-decoration: none;
  border-radius: 50px;
  font-weight: 600;
  transition: all .3s ease;
  box-shadow: 0 5px 15px rgba(0, 0, 0, .2);
}

.explore-btn:hover {
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
  position: relative;
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

.favorite-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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
  font-size: 0.9rem;
  margin-bottom: 0.8rem;
}

.movie-overview {
  color: #666;
  font-size: 0.9rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Responsividade */
@media (max-width: 768px) {
  .favorites {
    padding: 1rem;
  }

  .page-title {
    font-size: 2.2rem;
  }

  .page-subtitle {
    font-size: 1rem;
  }

  .filters-section {
    padding: 1rem;
  }

  .genres-filter {
    gap: 0.6rem;
  }

  .genre-chip {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
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

  .filters-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .clear-filter-btn {
    align-self: center;
  }
}
</style>