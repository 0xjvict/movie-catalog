// src/api/http.ts
export const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080'

export const apiFetch = async (url: string, options: RequestInit = {}): Promise<Response> => {
    const token = localStorage.getItem('sanctum_token')

    // Obter o CSRF token dos cookies
    const getCsrfToken = (): string | null => {
        const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/)
        return match ? decodeURIComponent(match[1]) : null
    }

    const headers: HeadersInit = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers,
    }

    // Adicionar CSRF token para todas as requisições POST, PUT, PATCH, DELETE
    const method = options.method?.toUpperCase()
    if (method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
        const csrfToken = getCsrfToken()
        if (csrfToken) {
            headers['X-XSRF-TOKEN'] = csrfToken
        }
    }

    if (token) {
        headers['Authorization'] = `Bearer ${token}`
    }

    return await fetch(`${baseURL}${url}`, {
        ...options,
        headers,
        credentials: 'include',
    })
}