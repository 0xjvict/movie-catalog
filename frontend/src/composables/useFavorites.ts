// src/composables/useFavorites.ts
import { ref, computed } from 'vue'
import type { Ref, ComputedRef } from 'vue'
import { apiFetch } from '@/api/http'

export interface Favorite {
    id: number
    movieId: number
    userId: number
    title: string
    overview: string
    posterPath: string | null
    releaseDate: string
    createdAt: string
    updatedAt: string
}

interface ApiResponse<T> {
    success: boolean
    data?: T
    error?: string
}

export const useFavorites = () => {
    const favorites: Ref<Favorite[]> = ref([])
    const loading: Ref<boolean> = ref(false)
    const error: Ref<string | null> = ref(null)

    const favoritesCount: ComputedRef<number> = computed(() => favorites.value.length)

    // Buscar todos os favoritos
    const getFavorites = async (): Promise<void> => {
        loading.value = true
        error.value = null

        try {
            const response = await apiFetch('/api/favorites')
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)

            const data: ApiResponse<Favorite[]> = await response.json()
            if (data.success && data.data) {
                favorites.value = data.data
            } else {
                error.value = data.error || "Erro ao buscar favoritos."
            }
        } catch (err) {
            error.value = "Falha de conexão. Tente novamente."
            console.error("Erro em getFavorites:", err)
        } finally {
            loading.value = false
        }
    }

    // Adicionar favorito
    const addFavorite = async (movieId: number): Promise<{ success: boolean; error?: string }> => {
        if (!movieId) {
            return { success: false, error: "ID do filme inválido" }
        }

        loading.value = true
        error.value = null

        try {
            const payload = {
                movie_id: movieId // Correto: apenas o ID numérico
            }
            console.log("Payload enviado:", JSON.stringify(payload))

            const response = await apiFetch('/api/favorites', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            })

            const data: ApiResponse<Favorite> = await response.json()

            if (data.success && data.data) {
                favorites.value.push(data.data)
                return { success: true }
            } else {
                const errorMsg = data.error || "Erro ao adicionar aos favoritos."
                error.value = errorMsg
                return {
                    success: false,
                    error: errorMsg,
                    isAlreadyFavorited: errorMsg.includes("já favoritado")
                }
            }
        } catch (err: any) {
            const errorMsg = "Falha de conexão. Tente novamente."
            error.value = errorMsg
            console.error("Erro em addFavorite:", err)
            return { success: false, error: errorMsg }
        } finally {
            loading.value = false
        }
    }

    // Remover favorito
    const removeFavorite = async (movieId: number): Promise<{ success: boolean; error?: string }> => {
        loading.value = true
        error.value = null

        try {
            const response = await apiFetch(`/api/favorites/${movieId}`, { method: 'DELETE' })
            const data: ApiResponse<void> = await response.json()

            if (data.success) {
                favorites.value = favorites.value.filter(fav => fav.movieId !== movieId)
                return { success: true }
            } else {
                const errorMsg = data.error || "Erro ao remover dos favoritos."
                return { success: false, error: errorMsg }
            }
        } catch (err) {
            const errorMsg = "Falha de conexão. Tente novamente."
            console.error("Erro em removeFavorite:", err)
            return { success: false, error: errorMsg }
        } finally {
            loading.value = false
        }
    }

    const toggleFavorite = async (movieId: number): Promise<{ success: boolean; error?: string; isAlreadyFavorited?: boolean }> => {
        try {
            const result = await addFavorite(movieId)

            if (result.success) {
                return { success: true }
            }
            // ✅ Se falhou porque já está favoritado, então REMOVE
            else if (result.isAlreadyFavorited) {
                console.log('📌 Removendo favorito existente...')
                const removeResult = await removeFavorite(movieId)
                return {
                    success: removeResult.success,
                    error: removeResult.error
                }
            }
            else {
                return result
            }
        } catch (error) {
            console.error('❌ Erro no toggleFavorite:', error)
            return { success: false, error: 'Erro interno' }
        }
    }

    // Verificar se é favorito
    const isFavorite = (movieId: number): boolean => {
        if (!favorites.value || favorites.value.length === 0) return false

        // ✅ Verifica de múltiplas formas para evitar dessincronização
        return favorites.value.some(fav =>
            fav.movieId === movieId ||
            fav.id === movieId ||
            fav.movie_id === movieId
        )
    }

    const clearError = () => { error.value = null }

    const loadFavorites = async () => {
        if (favorites.value.length === 0) await getFavorites()
    }

    return {
        favorites,
        loading,
        error,
        favoritesCount,
        getFavorites,
        addFavorite,
        removeFavorite,
        toggleFavorite,
        isFavorite,
        clearError,
        loadFavorites,
    }
}
