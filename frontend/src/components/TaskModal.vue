<script setup>
import { ref, onMounted } from 'vue'
import { useTaskStore } from '../stores/task'

const props = defineProps({
  task: { type: Object, default: null },
})

const emit = defineEmits(['close', 'saved'])

const taskStore = useTaskStore()
const users = ref([])
const loading = ref(false)
const error = ref('')

const form = ref({
  title: props.task?.title || '',
  description: props.task?.description || '',
  status: props.task?.status || 'pending',
  priority: props.task?.priority || 'medium',
  deadline: props.task?.deadline?.slice(0, 10) || '',
  assigned_to: props.task?.assigned_to || '',
})

onMounted(async () => {
  try {
    users.value = await taskStore.fetchUsers()
  } catch (e) {
    // ignore
  }
})

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    const data = { ...form.value }
    if (!data.assigned_to) data.assigned_to = null
    if (!data.deadline) data.deadline = null

    if (props.task) {
      await taskStore.updateTask(props.task.id, data)
    } else {
      await taskStore.createTask(data)
    }
    emit('saved')
  } catch (e) {
    error.value = e.response?.data?.message || 'Something went wrong'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4" @click.self="emit('close')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-100">
        <div>
          <h2 class="text-xl font-bold text-gray-800">{{ task ? 'Edit Task' : 'Create New Task' }}</h2>
          <p class="text-sm text-gray-400 mt-0.5">{{ task ? 'Update task details' : 'Fill in the details below' }}</p>
        </div>
        <button @click="emit('close')" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 cursor-pointer transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="p-6">
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl mb-5 text-sm">
          {{ error }}
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Title *</label>
            <input v-model="form.title" type="text" required
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
              placeholder="What needs to be done?" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
            <textarea v-model="form.description" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition resize-none"
              placeholder="Add more details..."></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
              <select v-model="form.status"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition appearance-none bg-white">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority</label>
              <select v-model="form.priority"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition appearance-none bg-white">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Deadline</label>
            <input v-model="form.deadline" type="date"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign to</label>
            <select v-model="form.assigned_to"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition appearance-none bg-white">
              <option value="">Unassigned</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>

          <div class="flex gap-3 pt-2">
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl hover:bg-indigo-700 disabled:opacity-50 cursor-pointer font-medium transition shadow-lg shadow-indigo-200"
            >
              <span v-if="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Saving...
              </span>
              <span v-else>{{ task ? 'Update Task' : 'Create Task' }}</span>
            </button>
            <button
              type="button"
              @click="emit('close')"
              class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 cursor-pointer font-medium transition"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
