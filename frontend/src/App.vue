<!--App.vue-->
<script setup lang="ts">
import { computed } from "vue"
import { useRoute } from "vue-router"
import Navbar from "@/components/Navbar.vue"

const route = useRoute()

// Esconde navbar em páginas específicas (ex: login e register)
const showNavbar = computed(() => !["/login", "/register"].includes(route.path))
</script>

<template>
  <div id="app">
    <!-- Navbar global -->
    <Navbar v-if="showNavbar" />

    <!-- Container principal -->
    <main :class="{ 'with-navbar': showNavbar }">
      <router-view />
    </main>

    <!-- Opcional: footer global -->
    <!-- <Footer /> -->
  </div>
</template>

<style>
/* Reset global */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Estilos do body */
body {
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

/* Estilos globais do app */
#app {
  min-height: 100vh;
  /* REMOVIDO: background: #0f0f0f; - Esta era a causa do fundo preto! */
  /* REMOVIDO: color: #fff; - Não precisamos forçar texto branco */
  /* REMOVIDO: padding-top: 70px; - Movido para o main */
}

/* Container principal */
main {
  min-height: 100vh;
}

/* Adiciona padding-top apenas quando a navbar está visível */
main.with-navbar {
  padding-top: 70px;
}

/* Estilos para páginas de login/register sem navbar */
main:not(.with-navbar) {
  padding-top: 0;
}

/* Se quiser manter seus estilos dos logos, mas adaptados */
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}

.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}

.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}

/* Garantir que as páginas tenham fundo adequado */
.page-container {
  min-height: calc(100vh - 70px);
  padding: 2rem;
}

/* Para páginas sem navbar */
.full-page {
  min-height: 100vh;
  padding: 2rem;
}
</style>