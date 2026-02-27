<script setup>
import { onMounted, ref, watch } from 'vue'
import { useTaskStore } from '../stores/task'
import TaskModal from '../components/TaskModal.vue'

const taskStore = useTaskStore()
const loading = ref(true)
const showModal = ref(false)
const editingTask = ref(null)

const filters = ref({ status: '', priority: '', search: '' })
const currentPage = ref(1)

onMounted(async () => {
  await taskStore.fetchTasks()
  loading.value = false
})

let searchTimeout = null
watch(filters, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    taskStore.fetchTasks(filters.value, 1)
  }, 300)
}, { deep: true })

function goToPage(page) {
  currentPage.value = page
  taskStore.fetchTasks(filters.value, page)
}

function openCreate() {
  editingTask.value = null
  showModal.value = true
}

function openEdit(task) {
  editingTask.value = { ...task }
  showModal.value = true
}

async function handleDelete(id) {
  if (confirm('Are you sure you want to delete this task?')) {
    await taskStore.deleteTask(id)
  }
}

async function quickStatusChange(task, newStatus) {
  await taskStore.updateTask(task.id, { status: newStatus })
  taskStore.fetchTasks(filters.value, currentPage.value)
}

function onSaved() {
  showModal.value = false
  taskStore.fetchTasks(filters.value, currentPage.value)
}

function statusBadge(status) {
  const map = {
    pending: 'bg-amber-100 text-amber-700 border border-amber-200',
    in_progress: 'bg-indigo-100 text-indigo-700 border border-indigo-200',
    completed: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

function priorityBadge(priority) {
  const map = {
    low: 'bg-slate-100 text-slate-600 border border-slate-200',
    medium: 'bg-blue-100 text-blue-700 border border-blue-200',
    high: 'bg-red-100 text-red-700 border border-red-200',
  }
  return map[priority] || 'bg-gray-100 text-gray-600'
}

function priorityDot(priority) {
  const map = { low: 'bg-slate-400', medium: 'bg-blue-500', high: 'bg-red-500' }
  return map[priority] || 'bg-gray-400'
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Tasks</h1>
        <p class="text-gray-500 text-sm mt-1">{{ taskStore.pagination.total }} task(s) found</p>
      </div>
      <button
        @click="openCreate"
        class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 cursor-pointer font-medium transition shadow-lg shadow-indigo-200 text-sm flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Task
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search tasks..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
          />
        </div>
        <select
          v-model="filters.status"
          class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition appearance-none bg-white"
        >
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
        <select
          v-model="filters.priority"
          class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition appearance-none bg-white"
        >
          <option value="">All Priority</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <svg class="animate-spin h-10 w-10 text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Empty State -->
    <div v-else-if="taskStore.tasks.length === 0" class="bg-white rounded-2xl shadow-md border border-gray-100 p-12 text-center">
      <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      </div>
      <p class="text-gray-600 font-semibold text-lg">No tasks found</p>
      <p class="text-gray-400 text-sm mt-1">Try adjusting your filters or create a new task</p>
      <button @click="openCreate" class="mt-5 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 cursor-pointer text-sm font-medium">
        Create your first task
      </button>
    </div>

    <!-- Task List -->
    <div v-else class="space-y-4">
      <div
        v-for="task in taskStore.tasks"
        :key="task.id"
        class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 sm:p-6 hover:shadow-lg transition-all group"
      >
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-start gap-3">
              <div :class="priorityDot(task.priority)" class="w-2.5 h-2.5 rounded-full mt-2 shrink-0"></div>
              <div class="min-w-0">
                <h3 class="font-semibold text-gray-800 text-lg truncate">{{ task.title }}</h3>
                <p v-if="task.description" class="text-gray-400 text-sm mt-1 line-clamp-2">{{ task.description }}</p>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 ml-5">
              <span :class="statusBadge(task.status)" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                {{ task.status.replace('_', ' ') }}
              </span>
              <span :class="priorityBadge(task.priority)" class="px-2.5 py-1 rounded-lg text-xs font-semibold">
                {{ task.priority }}
              </span>
              <span v-if="task.deadline" class="bg-gray-50 text-gray-500 border border-gray-200 px-2.5 py-1 rounded-lg text-xs font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ new Date(task.deadline).toLocaleDateString() }}
              </span>
              <span v-if="task.assignee" class="bg-purple-50 text-purple-600 border border-purple-200 px-2.5 py-1 rounded-lg text-xs font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ task.assignee.name }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 shrink-0 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
            <button
              v-if="task.status === 'pending'"
              @click="quickStatusChange(task, 'in_progress')"
              class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-lg cursor-pointer" title="Start">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <button
              v-if="task.status === 'in_progress'"
              @click="quickStatusChange(task, 'completed')"
              class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg cursor-pointer" title="Complete">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <button @click="openEdit(task)" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg cursor-pointer" title="Edit">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button @click="handleDelete(task.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg cursor-pointer" title="Delete">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="taskStore.pagination.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
      <button
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-2 rounded-lg border border-gray-200 text-sm font-medium disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 cursor-pointer transition"
      >
        Previous
      </button>
      <template v-for="page in taskStore.pagination.last_page" :key="page">
        <button
          v-if="page === 1 || page === taskStore.pagination.last_page || (page >= currentPage - 1 && page <= currentPage + 1)"
          @click="goToPage(page)"
          :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50'"
          class="w-10 h-10 rounded-lg border text-sm font-medium cursor-pointer transition"
        >
          {{ page }}
        </button>
        <span
          v-else-if="page === currentPage - 2 || page === currentPage + 2"
          class="text-gray-400"
        >...</span>
      </template>
      <button
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage === taskStore.pagination.last_page"
        class="px-3 py-2 rounded-lg border border-gray-200 text-sm font-medium disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 cursor-pointer transition"
      >
        Next
      </button>
    </div>

    <TaskModal v-if="showModal" :task="editingTask" @close="showModal = false" @saved="onSaved" />
  </div>
</template>
