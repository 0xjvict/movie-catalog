export const useFavorites = () => {
    const favorites = ref([])
    const loading = ref(false)
    const error = ref(null)

    const config = useRuntimeConfig()
    const baseUrl = `${config.public.sanctum.baseUrl}/api`

    /**
     * Função para obter o token CSRF do cookie
     */
    const getCSRFToken = (): string | null => {
        if (process.client) {
            const name = 'XSRF-TOKEN='
            const decodedCookie = decodeURIComponent(document.cookie)
            const cookieArray = decodedCookie.split(';')

            for (let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i].trim()
                if (cookie.indexOf(name) === 0) {
                    return cookie.substring(name.length, cookie.length)
                }
            }
        }
        return null
    }

    /**
     * Busca todos os filmes favoritos do usuário
     */
    const getFavorites = async () => {
        loading.value = true
        error.value = null

        try {
            const response = await $fetch(`${baseUrl}/favorites`, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                }
            })

            if (response.success) {
                favorites.value = response.data
            } else {
                error.value = response.error || "Erro ao buscar favoritos."
            }
        } catch (err) {
            error.value = "Falha de conexão. Tente novamente."
            console.error("Erro em getFavorites:", err)
        } finally {
            loading.value = false
        }
    }

    /**
     * Adiciona um filme aos favoritos
     */
    const addToFavorites = async (movieId) => {
        if (!movieId) return { success: false, error: "ID do filme inválido" }

        loading.value = true
        error.value = null

        try {
            // Primeiro obtém o token CSRF
            await $fetch('/sanctum/csrf-cookie', {
                baseURL: config.public.sanctum.baseUrl,
                credentials: 'include'
            })

            // Obtém o token CSRF do cookie
            const csrfToken = getCSRFToken()

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }

            // Adiciona o token CSRF ao header se estiver disponível
            if (csrfToken) {
                headers['X-XSRF-TOKEN'] = csrfToken
            }

            const response = await $fetch(`${baseUrl}/favorites`, {
                method: 'POST',
                body: { movie_id: movieId },
                credentials: 'include',
                headers
            })

            // Se a resposta foi recebida (status 200), mesmo com success: false, não é um erro HTTP
            if (response.success) {
                if (response.data) {
                    favorites.value.push(response.data)
                }
                return { success: true, message: "Filme adicionado aos favoritos!" }
            } else {
                // Backend retornou success: false mas com uma mensagem (como "filme já favoritado")
                return {
                    success: false,
                    error: response.error || "Erro ao adicionar aos favoritos.",
                    isAlreadyFavorited: response.error?.includes("já favoritado")
                }
            }
        } catch (err) {
            // Erro de rede ou HTTP (como 400, 500, etc.)
            if (err.data?.success === false) {
                // O backend retornou uma resposta com success: false
                return {
                    success: false,
                    error: err.data.error || "Erro ao adicionar aos favoritos.",
                    isAlreadyFavorited: err.data.error?.includes("já favoritado")
                }
            }

            // Erro de rede ou outro erro HTTP
            const errorMsg = err.statusCode === 400
                ? "Requisição inválida. Verifique os dados enviados."
                : "Falha de conexão. Tente novamente."

            return { success: false, error: errorMsg }
        } finally {
            loading.value = false
        }
    }

    /**
     * Remove um filme dos favoritos
     */
    const removeFromFavorites = async (movieId) => {
        if (!movieId) return { success: false, error: "ID do filme inválido" }

        loading.value = true
        error.value = null

        try {
            // Primeiro obtém o token CSRF
            await $fetch('/sanctum/csrf-cookie', {
                baseURL: config.public.sanctum.baseUrl,
                credentials: 'include'
            })

            // Obtém o token CSRF do cookie
            const csrfToken = getCSRFToken()

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }

            // Adiciona o token CSRF ao header se estiver disponível
            if (csrfToken) {
                headers['X-XSRF-TOKEN'] = csrfToken
            }

            const response = await $fetch(`${baseUrl}/favorites/${movieId}`, {
                method: 'DELETE',
                credentials: 'include',
                headers
            })

            if (response.success) {
                favorites.value = favorites.value.filter(fav => fav.id !== movieId && fav.movie_id !== movieId)
                return { success: true, message: "Filme removido dos favoritos!" }
            } else {
                return {
                    success: false,
                    error: response.error || "Erro ao remover dos favoritos."
                }
            }
        } catch (err) {
            if (err.data?.success === false) {
                return {
                    success: false,
                    error: err.data.error || "Erro ao remover dos favoritos."
                }
            }

            const errorMsg = err.statusCode === 400
                ? "Requisição inválida. Verifique os dados enviados."
                : "Falha de conexão. Tente novamente."

            return { success: false, error: errorMsg }
        } finally {
            loading.value = false
        }
    }

    /**
     * Toggle favorito - adiciona se não estiver, remove se estiver
     */
    const toggleFavorite = async (movieId) => {
        const isFavorited = isFavorite(movieId)

        if (isFavorited) {
            const result = await removeFromFavorites(movieId)
            return result
        } else {
            const result = await addToFavorites(movieId)

            // Se tentou adicionar mas o filme já está favoritado, então remove
            if (!result.success && result.isAlreadyFavorited) {
                console.log("Filme já estava favoritado, removendo...")
                return await removeFromFavorites(movieId)
            }

            return result
        }
    }

    /**
     * Verifica se um filme está nos favoritos
     */
    const isFavorite = (movieId) => {
        if (!movieId || !favorites.value.length) return false
        return favorites.value.some(fav => fav.id === movieId || fav.movie_id === movieId)
    }

    /**
     * Conta total de favoritos
     */
    const favoritesCount = computed(() => {
        return favorites.value.length
    })

    /**
     * Limpa erros
     */
    const clearError = () => {
        error.value = null
    }

    return {
        // Estado
        favorites: readonly(favorites),
        loading: readonly(loading),
        error: readonly(error),
        favoritesCount,

        // Métodos
        getFavorites,
        addToFavorites,
        removeFromFavorites,
        toggleFavorite,
        isFavorite,
        clearError,
    }
}