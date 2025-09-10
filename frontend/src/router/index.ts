// src/router/index.ts
import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import Home from '@/views/Home.vue'
import Login from '@/views/Login.vue'
import Register from '@/views/Register.vue'
import MovieDetails from '@/views/MovieDetails.vue'
import NotFound from '@/views/NotFound.vue'
import Favorites from '@/views/Favorites.vue'
import { useAuth } from '@/composables/useAuth'

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'Home',
        component: Home,
        meta: { requiresAuth: true }
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/register',
        name: 'Register',
        component: Register
    },
    {
        path: '/logout',
        name: 'Logout',
        beforeEnter: async (to, from, next) => {
            const { logout } = useAuth()
            await logout()
            next('/login')
        }
    },
    {
        path: '/movie/:id',
        name: 'MovieDetails',
        component: MovieDetails,
        props: true,
        meta: { requiresAuth: true }
    },
    {
        path: '/favorites',
        name: 'Favorites',
        component: Favorites,
        meta: { requiresAuth: true }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async (to, from, next) => {
    const { checkAuth } = useAuth()

    if (to.meta.requiresAuth) {
        const isAuthenticated = await checkAuth()
        if (!isAuthenticated) {
            next('/login')
        } else {
            next()
        }
    } else {
        next()
    }
})

export default router