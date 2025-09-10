<!--App.vue-->
<script setup lang="ts">
import {computed} from "vue"
import {useRoute} from "vue-router"
import Navbar from "@/components/Navbar.vue"

const route = useRoute()

const showNavbar = computed(() => !["/login", "/register"].includes(route.path))
</script>

<template>
  <div id="app">
    <!-- Navbar global -->
    <Navbar v-if="showNavbar"/>

    <!-- Container principal -->
    <main :class="{ 'with-navbar': showNavbar }">
      <router-view/>
    </main>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

#app {
  min-height: 100vh;
}

main {
  min-height: 100vh;
}

main.with-navbar {
  padding-top: 70px;
}

main:not(.with-navbar) {
  padding-top: 0;
}

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

.page-container {
  min-height: calc(100vh - 70px);
  padding: 2rem;
}

.full-page {
  min-height: 100vh;
  padding: 2rem;
}
</style>