<script setup>
import { useAuthStore } from './stores/auth'
import Navbar from './components/Navbar.vue'

const authStore = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar v-if="authStore.isAuthenticated" />
    <router-view v-slot="{ Component }">
      <Transition name="page" mode="out-in">
        <component :is="Component" />
      </Transition>
    </router-view>
  </div>
</template>
