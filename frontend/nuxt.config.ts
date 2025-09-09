import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
    compatibilityDate: "2025-07-15",
    devtools: {enabled: true},
    vite: {
        plugins: [tailwindcss()],
    },
    css: ["~/assets/app.css"],
    modules: ["nuxt-auth-sanctum"],
    sanctum: {
        baseUrl: "http://localhost:8080",
        endpoints: {
            csrf: "/sanctum/csrf-cookie",
            login: "/login",
            logout: "/logout",
            user: "/api/user",
        },
        redirect: {
            onLogin: "/",
            onLogout: "/login",
        },
    },
    // Configuração para cookies e CSRF
    app: {
        head: {
            script: [
                {
                    src: 'https://unpkg.com/js-cookie@3.0.1/dist/js.cookie.min.js'
                }
            ]
        }
    }
});
