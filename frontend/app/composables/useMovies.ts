export const useMovies = () => {
    const movies = ref([])
    const movie = ref(null)
    const loading = ref(false)
    const error = ref(null)

    // Acessando a configuração do runtime
    const config = useRuntimeConfig()
    const baseUrl = `${config.public.sanctum.baseUrl}/api`

    /**
     * Busca filmes pelo título
     */
    const searchMovies = async (query) => {
        if (!query || !query.trim()) return

        loading.value = true
        error.value = null
        movies.value = []

        try {
            const response = await $fetch(`${baseUrl}/movies/search`, {
                query: { q: query },
                credentials: 'include', // <--- importante para enviar cookies do Sanctum
            })

            if (response.success) {
                movies.value = response.data
            } else {
                error.value = response.error || "Erro ao buscar filmes."
            }
        } catch (err) {
            error.value = "Falha de conexão. Tente novamente."
            console.error("Erro em searchMovies:", err)
        } finally {
            loading.value = false
        }
    }

    /**
     * Busca detalhes de um filme pelo ID
     */
    const getMovie = async (id) => {
        if (!id) return

        loading.value = true
        error.value = null
        movie.value = null

        try {
            const response = await $fetch(`${baseUrl}/movies/${id}`, {
                credentials: 'include', // <--- importante para enviar cookies do Sanctum
            })

            if (response.success) {
                movie.value = response.data
            } else {
                error.value = response.error || "Erro ao buscar detalhes do filme."
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
