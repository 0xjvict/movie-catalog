// src/composables/useMovies.ts
import {ref} from 'vue'
import type {Ref} from 'vue'
import {apiFetch} from '@/api/http'

export interface Movie {
    movieId: number
    title: string
    overview: string
    posterPath: string | null
    releaseDate: string
}

interface ApiResponse<T> {
    success: boolean
    data?: T
    error?: string
}

export const useMovies = () => {
    const movies: Ref<Movie[]> = ref([])
    const movie: Ref<Movie | null> = ref(null)
    const loading: Ref<boolean> = ref(false)
    const error: Ref<string | null> = ref(null)

    const searchMovies = async (query: string): Promise<void> => {
        if (!query || !query.trim()) return

        loading.value = true
        error.value = null
        movies.value = []

        try {
            const response = await apiFetch(`/api/movies/search?q=${encodeURIComponent(query)}`)

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: ApiResponse<Movie[]> = await response.json()

            if (data.success && data.data) {
                movies.value = data.data
            } else {
                error.value = data.error || "Erro ao buscar filmes."
            }
        } catch (err) {
            error.value = "Falha de conexão. Tente novamente."
            console.error("Erro em searchMovies:", err)
        } finally {
            loading.value = false
        }
    }

    const getMovie = async (id: number | string): Promise<void> => {
        if (!id) return

        loading.value = true
        error.value = null
        movie.value = null

        try {
            const response = await apiFetch(`/api/movies/${id}`)

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: ApiResponse<Movie> = await response.json()

            if (data.success && data.data) {
                movie.value = data.data
            } else {
                error.value = data.error || "Erro ao buscar detalhes do filme."
            }
        } catch (err) {
            error.value = "Falha de conexão. Tente novamente."
            console.error("Erro em getMovie:", err)
        } finally {
            loading.value = false
        }
    }

    return {
        movies,
        movie,
        loading,
        error,
        searchMovies,
        getMovie,
    }
}