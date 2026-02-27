<script setup>
import { onMounted, ref } from 'vue'
import { useTaskStore } from '../stores/task'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const taskStore = useTaskStore()
const authStore = useAuthStore()
const router = useRouter()
const loading = ref(true)

onMounted(async () => {
  await taskStore.fetchDashboard()
  loading.value = false
})

const statCards = [
  { key: 'total', label: 'Total Tasks', icon: 'clipboard', gradient: 'from-blue-500 to-blue-600' },
  { key: 'pending', label: 'Pending', icon: 'clock', gradient: 'from-amber-500 to-orange-500' },
  { key: 'in_progress', label: 'In Progress', icon: 'refresh', gradient: 'from-indigo-500 to-purple-500' },
  { key: 'completed', label: 'Completed', icon: 'check', gradient: 'from-emerald-500 to-green-500' },
]

function statusBadge(status) {
  const map = {
    pending: 'bg-amber-100 text-amber-700 border border-amber-200',
    in_progress: 'bg-indigo-100 text-indigo-700 border border-indigo-200',
    completed: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
  }
  return map[status] || 'bg-gray-100 text-gray-600'
}

function priorityBadge(priority) {
  const map = {
    low: 'bg-slate-100 text-slate-600 border border-slate-200',
    medium: 'bg-blue-100 text-blue-700 border border-blue-200',
    high: 'bg-red-100 text-red-700 border border-red-200',
  }
  return map[priority] || 'bg-gray-100 text-gray-600'
}

function getPercentage(key) {
  const total = taskStore.dashboard.stats.total || 0
  if (total === 0) return 0
  return Math.round((taskStore.dashboard.stats[key] / total) * 100)
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">
          Welcome back, <span class="text-indigo-600">{{ authStore.user?.name?.split(' ')[0] }}</span>
        </h1>
        <p class="text-gray-500 mt-1">Here's what's happening with your tasks</p>
      </div>
      <button
        @click="router.push('/tasks')"
        class="mt-4 sm:mt-0 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 cursor-pointer font-medium transition shadow-lg shadow-indigo-200 text-sm"
      >
        + New Task
      </button>
    </div>

    <div v-if="loading" class="flex items-center justify-center py-20">
      <svg class="animate-spin h-10 w-10 text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <template v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div
          v-for="stat in statCards"
          :key="stat.key"
          :class="'bg-gradient-to-br ' + stat.gradient"
          class="rounded-2xl p-5 sm:p-6 text-white shadow-lg hover:shadow-xl transition-shadow"
        >
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-white/80">{{ stat.label }}</p>
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
              <svg v-if="stat.icon === 'clipboard'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              <svg v-if="stat.icon === 'clock'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <svg v-if="stat.icon === 'refresh'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              <svg v-if="stat.icon === 'check'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
          <p class="text-3xl sm:text-4xl font-bold">{{ taskStore.dashboard.stats[stat.key] || 0 }}</p>
          <div class="mt-3 bg-white/20 rounded-full h-1.5">
            <div class="bg-white rounded-full h-1.5 transition-all duration-500" :style="{ width: getPercentage(stat.key) + '%' }"></div>
          </div>
          <p class="text-xs text-white/70 mt-1">{{ getPercentage(stat.key) }}% of total</p>
        </div>
      </div>

      <!-- Recent Tasks -->
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
          <h2 class="text-lg font-bold text-gray-800">Recent Tasks</h2>
          <router-link to="/tasks" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
            View all →
          </router-link>
        </div>

        <div v-if="taskStore.dashboard.recent_tasks.length === 0" class="p-12 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <p class="text-gray-500 font-medium">No tasks yet</p>
          <p class="text-gray-400 text-sm mt-1">Create your first task to get started!</p>
          <button @click="router.push('/tasks')" class="mt-4 bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-700 cursor-pointer text-sm font-medium">
            Create Task
          </button>
        </div>

        <div v-else class="divide-y divide-gray-50">
          <div
            v-for="task in taskStore.dashboard.recent_tasks"
            :key="task.id"
            class="flex flex-col sm:flex-row sm:items-center justify-between p-5 hover:bg-gray-50 transition-colors"
          >
            <div class="mb-2 sm:mb-0">
              <p class="font-semibold text-gray-800">{{ task.title }}</p>
              <div class="flex items-center gap-2 mt-1.5 text-sm text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ task.deadline ? new Date(task.deadline).toLocaleDateString() : 'No deadline' }}
              </div>
            </div>
            <div class="flex gap-2">
              <span :class="statusBadge(task.status)" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                {{ task.status.replace('_', ' ') }}
              </span>
              <span :class="priorityBadge(task.priority)" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                {{ task.priority }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
