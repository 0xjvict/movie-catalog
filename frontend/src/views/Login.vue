<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin" class="login-form">
      <h2>Entrar</h2>

      <div class="form-group">
        <label for="email">E-mail</label>
        <input
            type="email"
            id="email"
            v-model="formData.email"
            required
            placeholder="Seu e-mail"
        >
      </div>

      <div class="form-group">
        <label for="password">Senha</label>
        <input
            type="password"
            id="password"
            v-model="formData.password"
            required
            placeholder="Sua senha"
        >
      </div>

      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" v-model="formData.remember">
          Lembrar-me
        </label>
      </div>

      <button type="submit" :disabled="loading" class="login-btn">
        {{ loading ? 'Entrando...' : 'Entrar' }}
      </button>

      <p v-if="error" class="error-message">{{ error }}</p>

      <p class="register-link">
        Não tem uma conta? <router-link to="/register">Registrar-se</router-link>
      </p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import type { LoginData } from '@/composables/useAuth'

const { login, loading, error } = useAuth()
const router = useRouter()

// Defina o formData corretamente
const formData = reactive<LoginData>({
  email: '',
  password: '',
  remember: false
})

const handleLogin = async (): Promise<void> => {
  // Use formData em vez de credentials
  const success = await login(formData)
  if (success) {
    router.push('/')
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea, #764ba2);
  padding: 2rem;
}

.login-form {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px;
}

h2 {
  text-align: center;
  margin-bottom: 2rem;
  color: #333;
}

.form-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
}

input[type="email"]:focus,
input[type="password"]:focus {
  outline: none;
  border-color: #667eea;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.login-btn {
  width: 100%;
  padding: 0.75rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: opacity 0.3s;
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-btn:hover:not(:disabled) {
  opacity: 0.9;
}

.error-message {
  color: #e74c3c;
  text-align: center;
  margin-top: 1rem;
}

.register-link {
  text-align: center;
  margin-top: 1.5rem;
  color: #666;
}

.register-link a {
  color: #667eea;
  text-decoration: none;
}

.register-link a:hover {
  text-decoration: underline;
}
</style>