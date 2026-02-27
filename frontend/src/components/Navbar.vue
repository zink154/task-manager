<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter, useRoute } from 'vue-router'
import { ref, computed } from 'vue'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const mobileMenuOpen = ref(false)

const initials = computed(() => {
  const name = authStore.user?.name || ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const navLinks = [
  { to: '/', label: 'Dashboard', icon: 'dashboard' },
  { to: '/tasks', label: 'Tasks', icon: 'tasks' },
  { to: '/profile', label: 'Profile', icon: 'profile' },
]

function isActive(path) {
  return route.path === path
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span class="text-gray-800 text-lg font-bold">TaskManager</span>
          </router-link>

          <!-- Desktop Nav -->
          <div class="hidden md:flex ml-10 space-x-1">
            <router-link
              v-for="link in navLinks"
              :key="link.to"
              :to="link.to"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition',
                isActive(link.to)
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ link.label }}
            </router-link>
          </div>
        </div>

        <!-- Right Side -->
        <div class="hidden md:flex items-center gap-3">
          <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50">
            <div class="w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
              {{ initials }}
            </div>
            <span class="text-gray-700 text-sm font-medium">{{ authStore.user?.name }}</span>
          </div>
          <button
            @click="handleLogout"
            class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 cursor-pointer transition"
            title="Logout"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          </button>
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center">
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-gray-700 p-2 cursor-pointer">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-100 bg-white shadow-lg">
      <div class="px-4 py-3 space-y-1">
        <router-link
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          @click="mobileMenuOpen = false"
          :class="[
            'block px-4 py-2.5 rounded-lg text-sm font-medium transition',
            isActive(link.to)
              ? 'bg-indigo-50 text-indigo-700'
              : 'text-gray-600 hover:bg-gray-50'
          ]"
        >
          {{ link.label }}
        </router-link>
      </div>
      <div class="border-t border-gray-100 px-4 py-3">
        <div class="flex items-center gap-3 px-4 py-2">
          <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">{{ initials }}</div>
          <div>
            <p class="text-sm font-medium text-gray-700">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-400">{{ authStore.user?.email }}</p>
          </div>
        </div>
        <button
          @click="handleLogout"
          class="w-full mt-2 text-left px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium cursor-pointer"
        >
          Logout
        </button>
      </div>
    </div>
  </nav>
</template>
