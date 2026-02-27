import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api/axios'

export const useTaskStore = defineStore('task', () => {
  const tasks = ref([])
  const pagination = ref({ current_page: 1, last_page: 1, total: 0 })
  const dashboard = ref({ stats: {}, recent_tasks: [] })

  async function fetchTasks(filters = {}, page = 1) {
    const params = new URLSearchParams()
    if (filters.status) params.append('status', filters.status)
    if (filters.priority) params.append('priority', filters.priority)
    if (filters.search) params.append('search', filters.search)
    params.append('page', page)

    const response = await api.get('/tasks?' + params.toString())
    tasks.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
    }
    return response.data
  }

  async function fetchDashboard() {
    const response = await api.get('/dashboard')
    dashboard.value = response.data
    return response.data
  }

  async function createTask(data) {
    const response = await api.post('/tasks', data)
    tasks.value.unshift(response.data.data)
    return response.data.data
  }

  async function updateTask(id, data) {
    const response = await api.put(`/tasks/${id}`, data)
    const task = response.data.data
    const index = tasks.value.findIndex(t => t.id === id)
    if (index !== -1) tasks.value[index] = task
    return task
  }

  async function deleteTask(id) {
    await api.delete(`/tasks/${id}`)
    tasks.value = tasks.value.filter(t => t.id !== id)
  }

  async function fetchUsers() {
    const response = await api.get('/users')
    return response.data
  }

  return {
    tasks, pagination, dashboard,
    fetchTasks, fetchDashboard, createTask, updateTask, deleteTask, fetchUsers,
  }
})
