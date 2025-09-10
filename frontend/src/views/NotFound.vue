<template>
  <div class="notfound-container">
    <div class="notfound-content">
      <h1>404</h1>
      <h2>Página não encontrada</h2>
      <p>Desculpe, a página que você está procurando não existe.</p>
      <router-link to="/" class="home-btn">Voltar para a página inicial</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
// Não precisa de lógica especial
</script>

<style scoped>
.notfound-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea, #764ba2);
  padding: 2rem;
  color: white;
  text-align: center;
}

.notfound-content {
  background: white;
  padding: 3rem 2rem;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
}

.notfound-content h1 {
  font-size: 6rem;
  margin: 0;
  color: #667eea;
}

.notfound-content h2 {
  font-size: 2rem;
  margin: 1rem 0;
  color: #333;
}

.notfound-content p {
  font-size: 1rem;
  color: #666;
  margin-bottom: 2rem;
}

.home-btn {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border-radius: 5px;
  text-decoration: none;
  font-weight: 500;
  transition: opacity 0.3s;
}

.home-btn:hover {
  opacity: 0.9;
}
</style>
