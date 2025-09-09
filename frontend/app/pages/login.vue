<template>
  <div>
    <form @submit.prevent="handleLogin" class="mx-auto mt-10 w-1/2 min-w-100 p-4">
      <h1 class="mb-6 text-2xl font-bold">Login</h1>

      <!-- Erro genérico -->
      <p v-if="errors.general" class="mb-2 text-sm text-red-400">
        {{ errors.general }}
      </p>

      <!-- Campo email -->
      <div class="mb-4">
        <input
            v-model="formData.email"
            autocomplete="email"
            type="email"
            placeholder="Email"
            class="input input-primary w-full"
            @input="clearError('email')"
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-400">
          {{ errors.email }}
        </p>
      </div>

      <!-- Campo senha -->
      <div class="mb-4">
        <input
            v-model="formData.password"
            autocomplete="current-password"
            type="password"
            placeholder="Password"
            class="input input-primary w-full"
            @input="clearError('password')"
        />
        <p v-if="errors.password" class="mt-1 text-sm text-red-400">
          {{ errors.password }}
        </p>
      </div>

      <button
          class="btn btn-primary w-full flex items-center justify-center"
          :disabled="loading"
      >
        <span v-if="loading" class="loading loading-spinner mr-2"></span>
        {{ loading ? "Entrando..." : "Login" }}
      </button>
    </form>
  </div>
</template>

<script setup>
const { login } = useSanctumAuth()

const formData = ref({
  email: "",
  password: "",
})

const errors = ref({})
const loading = ref(false)

definePageMeta({
  layout: "auth",
  middleware: "sanctum:guest",
})

useHead({
  title: "Login",
})

/**
 * Limpa o erro de um campo específico
 */
const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

const handleLogin = async () => {
  loading.value = true
  errors.value = {}

  try {
    await login(formData.value)
    // redireciona automático pelo sanctum:redirect
  } catch (err) {
    if (err?.response?._data?.errors) {
      // Erros de validação vindos do backend (422)
      errors.value = err.response._data.errors
    } else if (err?.response?.status === 401) {
      // Credenciais inválidas
      errors.value.general = "Credenciais inválidas. Verifique seu email e senha."
    } else {
      // Erro inesperado
      errors.value.general = "Ocorreu um erro inesperado. Tente novamente mais tarde."
      console.error("Erro inesperado no login:", err)
    }
  } finally {
    loading.value = false
  }
}
</script>
