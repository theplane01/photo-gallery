<template>
  <div class="bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    <!-- <Navbar /> -->
    <main class="relative z-0">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
// import Navbar from './components/Navbar.vue'
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  scroll-behavior: smooth;
}

::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: rgba(17, 24, 39, 0.5);
  border-radius: 5px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #7c3aed, #3b82f6);
  border-radius: 5px;
  border: 2px solid rgba(17, 24, 39, 0.5);
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #8b5cf6, #4f46e5);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-up {
  animation: fadeInUp 1s ease-out;
}

.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
}

.floating {
  animation: float 3s ease-in-out infinite;
}

.gradient-text {
  background: linear-gradient(135deg, #8b5cf6, #3b82f6, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.text-glow {
  text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
}

.btn-glow {
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
  transition: box-shadow 0.3s ease;
}

.btn-glow:hover {
  box-shadow: 0 0 30px rgba(59, 130, 246, 0.6);
}

.photo-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.photo-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.stat-card {
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px) scale(1.02);
}

.hover-glow {
  transition: all 0.3s ease;
}

.hover-glow:hover {
  text-shadow: 0 0 15px rgba(59, 130, 246, 0.6);
}
</style>
