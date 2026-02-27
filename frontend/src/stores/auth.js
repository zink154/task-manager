import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user')))
  const token = ref(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)

  async function register(data) {
    const response = await api.post('/register', data)
    setAuth(response.data)
    return response.data
  }

  async function login(data) {
    const response = await api.post('/login', data)
    setAuth(response.data)
    return response.data
  }

  async function logout() {
    await api.post('/logout')
    clearAuth()
  }

  async function fetchProfile() {
    const response = await api.get('/profile')
    user.value = response.data
    localStorage.setItem('user', JSON.stringify(response.data))
    return response.data
  }

  async function updateProfile(data) {
    const response = await api.put('/profile', data)
    user.value = response.data
    localStorage.setItem('user', JSON.stringify(response.data))
    return response.data
  }

  function setAuth(data) {
    user.value = data.user
    token.value = data.token
    localStorage.setItem('user', JSON.stringify(data.user))
    localStorage.setItem('token', data.token)
  }

  function clearAuth() {
    user.value = null
    token.value = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  return {
    user, token, isAuthenticated,
    register, login, logout, fetchProfile, updateProfile,
  }
})
