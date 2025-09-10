// src/composables/useAuth.ts
import type {Ref} from 'vue'
import {ref} from 'vue'
import {apiFetch, baseURL} from '@/api/http'

export interface User {
    id: number
    name: string
    email: string
    email_verified_at?: string
    created_at: string
    updated_at: string
}

export interface LoginData {
    email: string
    password: string
    remember?: boolean
}

export interface RegisterData {
    name: string
    email: string
    password: string
    password_confirmation: string
}

let authInstance: ReturnType<typeof createAuth> | null = null

const createAuth = () => {
    const user: Ref<User | null> = ref(null)
    const loading: Ref<boolean> = ref(false)
    const error: Ref<string | null> = ref(null)

    // Função para obter CSRF token
    const getCsrfToken = async (): Promise<boolean> => {
        try {
            const response = await fetch(`${baseURL}/sanctum/csrf-cookie`, {
                method: 'GET',
                credentials: 'include',
            })
            return response.ok
        } catch {
            return false
        }
    }

    // Verificar se usuário está autenticado
    const checkAuth = async (): Promise<boolean> => {
        try {
            const token = localStorage.getItem('sanctum_token')
            if (!token) return false

            const response = await apiFetch('/api/user')

            if (response.ok) {
                user.value = await response.json()
                console.log('User authenticated:', user.value)
                return true
            }

            return false
        } catch {
            return false
        }
    }

    // Login
    const login = async (credentials: LoginData): Promise<boolean> => {
        loading.value = true
        error.value = null

        try {
            // Obter CSRF token primeiro
            const csrfSuccess = await getCsrfToken()
            if (!csrfSuccess) {
                error.value = 'Falha ao obter token de segurança'
                return false
            }

            // Aguardar um pouco para garantir que o cookie seja definido
            await new Promise(resolve => setTimeout(resolve, 100))

            const response = await apiFetch('/api/login', {
                method: 'POST',
                body: JSON.stringify(credentials),
            })

            if (response.ok) {
                const data = await response.json()
                localStorage.setItem('sanctum_token', data.token)
                user.value = data.user
                console.log('User after login:', user.value)
                return true
            } else {
                const errorData = await response.json()
                error.value = errorData.message || 'Erro no login'
                return false
            }
        } catch (err) {
            console.error('Login error:', err)
            error.value = 'Falha de conexão. Tente novamente.'
            return false
        } finally {
            loading.value = false
        }
    }

    // Registro
    const register = async (userData: RegisterData): Promise<boolean> => {
        loading.value = true
        error.value = null

        try {
            // Obter CSRF token primeiro
            const csrfSuccess = await getCsrfToken()
            if (!csrfSuccess) {
                error.value = 'Falha ao obter token de segurança'
                return false
            }

            // Aguardar um pouco para garantir que o cookie seja definido
            await new Promise(resolve => setTimeout(resolve, 100))

            const response = await apiFetch('/api/register', {
                method: 'POST',
                body: JSON.stringify(userData),
            })

            if (response.ok) {
                const data = await response.json()
                localStorage.setItem('sanctum_token', data.token)
                user.value = data.user
                console.log('User after register:', user.value)
                return true
            } else {
                const errorData = await response.json()
                error.value = errorData.message || 'Erro no registro'
                return false
            }
        } catch (err) {
            console.error('Register error:', err)
            error.value = 'Falha de conexão. Tente novamente.'
            return false
        } finally {
            loading.value = false
        }
    }

    // Logout
    const logout = async (): Promise<boolean> => {
        try {
            await apiFetch('/api/logout', {
                method: 'POST',
            })
            return true
        } catch (err) {
            console.error('Erro no logout:', err)
            return false
        } finally {
            localStorage.removeItem('sanctum_token')
            user.value = null
            console.log('User after logout:', user.value)
        }
    }

    return {
        user,
        loading,
        error,
        checkAuth,
        login,
        register,
        logout,
    }
}

export const useAuth = () => {
    if (!authInstance) {
        authInstance = createAuth()
    }
    return authInstance
}