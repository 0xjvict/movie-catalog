<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo / Home -->
      <router-link to="/" class="navbar-logo">
        🎬 MovieCatalog
      </router-link>

      <!-- Links -->
      <div class="navbar-links">
        <router-link to="/" class="nav-link" v-if="isAuthenticated">Início</router-link>
        <router-link to="/favorites" class="nav-link" v-if="isAuthenticated">Favoritos</router-link>
        <button @click="handleLogout" class="logout-btn" v-if="isAuthenticated">Sair</button>
        <router-link to="/login" class="nav-link" v-if="!isAuthenticated">Entrar</router-link>
        <router-link to="/register" class="nav-link" v-if="!isAuthenticated">Registrar</router-link>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import {computed, watch} from 'vue'
import {useRouter} from 'vue-router'
import {useAuth} from '@/composables/useAuth'

const {logout, user} = useAuth()
const router = useRouter()

// Use computed para reatividade automática
const isAuthenticated = computed(() => !!user.value)

// Debug: observe mudanças no user
watch(user, (newUser) => {
  console.log('User changed in navbar:', newUser)
  console.log('isAuthenticated:', isAuthenticated.value)
})

const handleLogout = async (): Promise<void> => {
  const success = await logout()
  if (success) {
    router.push('/login')
  } else {
    console.error('Falha no logout')
    router.push('/login')
  }
}
</script>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 70px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  backdrop-filter: blur(10px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, .3);
  display: flex;
  align-items: center;
  z-index: 1000;
  margin: 0;
  padding: 0;
  width: 100%;
  box-sizing: border-box;
}

.navbar-container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
}

.navbar-logo {
  font-size: 1.5rem;
  font-weight: 800;
  color: white;
  text-decoration: none;
  text-shadow: 1px 1px 4px rgba(0, 0, 0, .4);
  transition: transform .3s ease;
}

.navbar-logo:hover {
  transform: scale(1.05);
}

.navbar-links {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.nav-link {
  color: white;
  font-weight: 500;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 30px;
  transition: all .3s ease;
}

.nav-link:hover {
  background: rgba(255, 255, 255, .2);
  transform: translateY(-2px);
}

.logout-btn {
  padding: 0.5rem 1.2rem;
  border: none;
  border-radius: 50px;
  background: linear-gradient(135deg, #ff6b6b, #ee5a52);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all .3s ease;
  box-shadow: 0 5px 15px rgba(0, 0, 0, .2);
}

.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, .3);
}
</style>