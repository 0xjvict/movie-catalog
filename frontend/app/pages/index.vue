<template>
  <div class="mx-auto max-w-4xl p-6">
    <h1 class="mb-6 text-2xl font-bold">Movie Catalog</h1>

    <!-- Barra de busca -->
    <form @submit.prevent="searchMovies(query)" class="mb-6 flex gap-2">
      <input
          v-model="query"
          type="text"
          placeholder="Digite o título do filme..."
          class="input input-primary w-full"
          @input="error = null"
      />
      <button
          class="btn btn-primary"
          type="submit"
          :disabled="loading || !query.trim()"
      >
        <span v-if="loading" class="loading loading-spinner"></span>
        Buscar
      </button>
    </form>

    <!-- Mensagem de erro -->
    <p v-if="error" class="mb-4 text-red-500">{{ error }}</p>

    <!-- Resultados -->
    <div
        v-if="movies.length"
        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
    >
      <NuxtLink
          v-for="movie in movies"
          :key="movie.movieId"
          :to="`/movies/${movie.movieId}`"
          class="card bg-base-200 shadow-xl hover:shadow-2xl transition"
      >
        <figure class="h-72 w-full overflow-hidden">
          <img
              :src="movie.posterPath || '/placeholder.png'"
              :alt="movie.title"
              class="h-full w-full object-cover"
          />
        </figure>
        <div class="card-body">
          <h2 class="card-title">{{ movie.title }}</h2>
          <p class="text-sm text-gray-400">
            {{ formatDate(movie.releaseDate) }}
          </p>
          <p class="line-clamp-3 text-sm">{{ movie.overview }}</p>
        </div>
      </NuxtLink>
    </div>

    <!-- Nenhum resultado -->
    <p v-else-if="!loading && !error && query" class="text-gray-400">
      Nenhum filme encontrado para "{{ query }}"
    </p>
  </div>
</template>

<script setup>
import { useMovies } from "~/composables/useMovies"

const query = ref("")
const { movies, loading, error, searchMovies } = useMovies()

const formatDate = (dateStr) => {
  if (!dateStr) return "Data desconhecida"
  return new Date(dateStr).toLocaleDateString("pt-BR", {
    year: "numeric",
    month: "short",
    day: "numeric",
  })
}
</script>
