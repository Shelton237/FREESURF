<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'

defineOptions({ layout: BackofficeLayout })

const props = defineProps({
  tickets: Object,
  filters: Object,
  options: Object,
  assignees: Array,
})

const filterState = reactive({
  status: props.filters?.status || '',
  priority: props.filters?.priority || '',
  search: props.filters?.search || '',
})

const createForm = ref({
  client_id: '',
  type: 'incident',
  channel: 'phone',
  priority: 'normal',
  subject: '',
  description: '',
  assigned_to: '',
})

const applyFilters = () => {
  router.get('/backoffice/sav', filterState, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterState.status = ''
  filterState.priority = ''
  filterState.search = ''
  applyFilters()
}

const submitCreate = () => {
  router.post('/backoffice/sav', createForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      createForm.value = {
        client_id: '',
        type: 'incident',
        channel: 'phone',
        priority: 'normal',
        subject: '',
        description: '',
        assigned_to: '',
      }
    },
  })
}

const statuses = computed(() => props.options?.statuses || [])
const priorities = computed(() => props.options?.priorities || [])

const updateTicket = (ticket) => {
  router.patch(`/backoffice/sav/${ticket.id}`, {
    status: ticket.status,
    priority: ticket.priority,
    assigned_to: ticket.assigned_to,
    resolution_notes: ticket.resolution_notes || '',
    follow_up_at: ticket.follow_up_at || '',
  }, { preserveScroll: true })
}

const statusLabel = (status) => {
  const map = {
    open: 'Ouvert',
    in_progress: 'En cours',
    pending_client: 'En attente client',
    resolved: 'Résolu',
    closed: 'Clos',
  }
  return map[status] || status
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">SAV / Service client</h1>
        <p class="text-gray-600">Suivi des tickets incidents, assistance et réclamations.</p>
      </div>
      <Link href="/backoffice" class="text-sm text-blue-600">← Dashboard</Link>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      <div class="border rounded bg-white p-4 lg:col-span-2">
        <h2 class="font-semibold mb-3">Tickets récents</h2>

        <div class="grid md:grid-cols-4 gap-3 mb-4 text-sm">
          <label class="flex flex-col gap-1">
            <span class="text-xs uppercase text-gray-500">Statut</span>
            <select v-model="filterState.status" class="border rounded px-2 py-1">
              <option value="">Tous</option>
              <option v-for="status in statuses" :key="status" :value="status">{{ statusLabel(status) }}</option>
            </select>
          </label>
          <label class="flex flex-col gap-1">
            <span class="text-xs uppercase text-gray-500">Priorité</span>
            <select v-model="filterState.priority" class="border rounded px-2 py-1">
              <option value="">Toutes</option>
              <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
            </select>
          </label>
          <label class="md:col-span-2 flex flex-col gap-1">
            <span class="text-xs uppercase text-gray-500">Recherche client/code</span>
            <input v-model="filterState.search" class="border rounded px-2 py-1" placeholder="Code ou nom client" />
          </label>
        </div>
        <div class="flex gap-2 mb-4">
          <button class="px-3 py-1 bg-gray-900 text-white text-xs rounded" @click="applyFilters">Filtrer</button>
          <button class="px-3 py-1 border text-xs rounded" @click="resetFilters">Réinitialiser</button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-gray-500 border-b">
              <tr>
                <th class="py-2 text-left">Ticket</th>
                <th class="py-2 text-left">Client</th>
                <th class="py-2 text-left">Statut</th>
                <th class="py-2 text-left">Assigné</th>
                <th class="py-2 text-left">Priorité</th>
                <th class="py-2 text-left">Mise à jour</th>
                <th class="py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ticket in tickets.data" :key="ticket.id" class="border-b last:border-0">
                <td class="py-2">
                  <div class="font-semibold">#{{ ticket.id }} · {{ ticket.subject }}</div>
                  <div class="text-xs text-gray-500">{{ ticket.type }} · {{ ticket.channel }}</div>
                </td>
                <td class="py-2">
                  <div class="font-semibold">{{ ticket.client?.nom }}</div>
                  <div class="text-xs text-gray-500">{{ ticket.client?.code }}</div>
                </td>
                <td class="py-2">
                  <select v-model="ticket.status" class="border rounded px-2 py-1 text-xs">
                    <option v-for="status in statuses" :key="status" :value="status">{{ statusLabel(status) }}</option>
                  </select>
                </td>
                <td class="py-2">
                  <select v-model="ticket.assigned_to" class="border rounded px-2 py-1 text-xs">
                    <option value="">—</option>
                    <option v-for="user in assignees" :key="user.id" :value="user.id">{{ user.name }}</option>
                  </select>
                </td>
                <td class="py-2">
                  <select v-model="ticket.priority" class="border rounded px-2 py-1 text-xs">
                    <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                  </select>
                </td>
                <td class="py-2 text-xs text-gray-500">
                  {{ ticket.updated_at ?? '—' }}
                </td>
                <td class="py-2 text-right">
                  <button class="px-3 py-1 bg-brand text-white rounded text-xs" @click="updateTicket(ticket)">
                    Mettre à jour
                  </button>
                </td>
              </tr>
              <tr v-if="!tickets.data?.length">
                <td colspan="7" class="py-4 text-center text-gray-500 text-sm">Aucun ticket.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 flex justify-end gap-2 text-sm" v-if="tickets.links?.length">
          <template v-for="link in tickets.links" :key="link.url ?? link.label">
            <Link
              v-if="link.url"
              :href="link.url"
              class="px-3 py-1 border rounded"
              :class="link.active ? 'bg-brand text-white border-brand' : ''"
              v-html="link.label"
            />
          </template>
        </div>
      </div>

      <div class="border rounded bg-white p-4">
        <h2 class="font-semibold mb-3">Nouveau ticket SAV</h2>
        <div class="space-y-3">
          <div>
            <label class="block text-sm text-gray-600">ID client</label>
            <input v-model="createForm.client_id" class="w-full border rounded p-2" placeholder="Ex: 42" />
          </div>
          <div>
            <label class="block text-sm text-gray-600">Sujet</label>
            <input v-model="createForm.subject" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm text-gray-600">Description</label>
            <textarea v-model="createForm.description" class="w-full border rounded p-2" rows="3"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3 text-sm">
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Type</span>
              <select v-model="createForm.type" class="border rounded px-2 py-1">
                <option v-for="type in props.options?.types || []" :key="type" :value="type">{{ type }}</option>
              </select>
            </label>
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Canal</span>
              <select v-model="createForm.channel" class="border rounded px-2 py-1">
                <option v-for="channel in props.options?.channels || []" :key="channel" :value="channel">{{ channel }}</option>
              </select>
            </label>
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Priorité</span>
              <select v-model="createForm.priority" class="border rounded px-2 py-1">
                <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
              </select>
            </label>
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Assigné à</span>
              <select v-model="createForm.assigned_to" class="border rounded px-2 py-1">
                <option value="">—</option>
                <option v-for="user in assignees" :key="user.id" :value="user.id">{{ user.name }}</option>
              </select>
            </label>
          </div>
          <button class="w-full px-4 py-2 bg-brand text-white rounded" @click="submitCreate">Créer</button>
        </div>
      </div>
    </div>
  </div>
</template>
