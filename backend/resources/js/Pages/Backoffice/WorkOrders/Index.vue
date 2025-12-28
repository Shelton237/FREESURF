<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: BackofficeLayout })

const props = defineProps({
  workOrders: Object,
  filters: Object,
  options: Object,
})

const filterState = reactive({
  status: props.filters?.status || '',
  type: props.filters?.type || '',
  search: props.filters?.search || '',
})

const applyFilters = () => {
  router.get('/backoffice/work-orders', filterState, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterState.status = ''
  filterState.type = ''
  filterState.search = ''
  applyFilters()
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">Interventions</h1>
        <p class="text-gray-600">Planning et suivi des work orders (survey, installation, maintenance).</p>
      </div>
      <Link href="/backoffice/work-orders/create" class="px-3 py-2 bg-brand text-white rounded text-sm">
        + Nouvelle intervention
      </Link>
    </div>

    <div class="border rounded bg-white p-4 space-y-4">
      <div class="grid md:grid-cols-4 gap-3 text-sm">
        <label class="flex flex-col gap-1">
          <span class="text-xs uppercase text-gray-500">Statut</span>
          <select v-model="filterState.status" class="border rounded px-2 py-1">
            <option value="">Tous</option>
            <option v-for="status in props.options?.statuses || []" :key="status" :value="status">
              {{ status }}
            </option>
          </select>
        </label>
        <label class="flex flex-col gap-1">
          <span class="text-xs uppercase text-gray-500">Type</span>
          <select v-model="filterState.type" class="border rounded px-2 py-1">
            <option value="">Tous</option>
            <option v-for="type in props.options?.types || []" :key="type" :value="type">
              {{ type }}
            </option>
          </select>
        </label>
        <label class="md:col-span-2 flex flex-col gap-1">
          <span class="text-xs uppercase text-gray-500">Recherche client/BTS</span>
          <input v-model="filterState.search" class="border rounded px-2 py-1" placeholder="Code client, nom..." />
        </label>
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-1 bg-gray-900 text-white text-xs rounded" @click="applyFilters">Filtrer</button>
        <button class="px-3 py-1 border text-xs rounded" @click="resetFilters">Réinitialiser</button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-xs uppercase text-gray-500 border-b">
            <tr>
              <th class="py-2 text-left">Intervention</th>
              <th class="py-2 text-left">Client</th>
              <th class="py-2 text-left">BTS</th>
              <th class="py-2 text-left">Statut</th>
              <th class="py-2 text-left">Technicien</th>
              <th class="py-2 text-left">Planifiée</th>
              <th class="py-2 text-left">Complétée</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in workOrders.data" :key="order.id" class="border-b last:border-0">
              <td class="py-2">
                <div class="font-semibold">#{{ order.id }} · {{ order.type }}</div>
                <div class="text-xs text-gray-500">{{ order.notes || '—' }}</div>
              </td>
              <td class="py-2">
                <div class="font-semibold">{{ order.client?.nom ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ order.client?.code }}</div>
              </td>
              <td class="py-2">
                <div class="font-semibold">{{ order.bts?.code ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ order.bts?.ville }}</div>
              </td>
              <td class="py-2 text-xs">
                <span class="px-2 py-1 rounded bg-gray-100 capitalize">{{ order.status }}</span>
              </td>
              <td class="py-2 text-xs text-gray-600">{{ order.technician?.name ?? '—' }}</td>
              <td class="py-2 text-xs text-gray-500">{{ order.scheduled_at ?? '—' }}</td>
              <td class="py-2 text-xs text-gray-500">{{ order.completed_at ?? '—' }}</td>
            </tr>
            <tr v-if="!workOrders.data?.length">
              <td colspan="7" class="py-4 text-center text-gray-500 text-sm">Aucune intervention.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex justify-end gap-2 text-sm" v-if="workOrders.links?.length">
        <template v-for="link in workOrders.links" :key="link.url ?? link.label">
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
  </div>
</template>
