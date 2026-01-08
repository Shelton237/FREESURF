<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import TechLayout from '@/Layouts/TechLayout.vue'
import { computed } from 'vue'

defineOptions({ layout: TechLayout })

const props = defineProps({
  tickets: Object,
  filters: Object,
  statusOptions: Array,
})

const filterForm = useForm({
  statut: props.filters?.statut || '',
})

const page = usePage()
const userName = computed(() => page.props.auth?.user?.name || '')

const applyFilters = () => {
  router.get('/tech/sav', filterForm.data(), {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="px-4 py-8">
    <div class="max-w-6xl mx-auto space-y-6">
      <header class="bg-white border rounded-2xl shadow-sm p-6 space-y-3">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Support terrain</p>
            <h1 class="text-2xl font-semibold text-gray-900">Tickets SAV assignés</h1>
            <p class="text-sm text-gray-500">Technicien : {{ userName }}</p>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-3xl font-bold text-brand">{{ tickets.total }}</p>
          </div>
        </div>
        <div class="flex flex-wrap gap-3 items-end">
          <label class="text-sm text-gray-600">
            Statut
            <select v-model="filterForm.statut" class="mt-1 border rounded-lg px-3 py-2" @change="applyFilters">
              <option value="">Tous</option>
              <option v-for="status in statusOptions" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </label>
        </div>
      </header>

      <section class="bg-white border rounded-2xl shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 uppercase text-xs border-b">
                <th class="px-4 py-3">Ticket</th>
                <th class="px-4 py-3">Client</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Priorité</th>
                <th class="px-4 py-3">Canal</th>
                <th class="px-4 py-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="ticket in tickets.data"
                :key="ticket.id"
                class="border-b last:border-0 hover:bg-gray-50 transition"
              >
                <td class="px-4 py-3">
                  <p class="font-semibold text-gray-900">#{{ ticket.id }} · {{ ticket.subject }}</p>
                  <p class="text-xs text-gray-500">{{ ticket.type }}</p>
                </td>
                <td class="px-4 py-3">
                  <p class="font-medium text-gray-900">{{ ticket.client?.nom ?? '—' }}</p>
                  <p class="text-xs text-gray-500">{{ ticket.client?.telephone ?? '—' }}</p>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 rounded-full text-xs uppercase bg-slate-100 text-slate-700">
                    {{ ticket.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ ticket.priority }}</td>
                <td class="px-4 py-3 text-gray-700">{{ ticket.channel }}</td>
                <td class="px-4 py-3 text-right">
                  <a
                    :href="`/tech/sav/${ticket.id}`"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border text-xs font-semibold text-brand hover:bg-rose-50"
                  >
                    Ouvrir →
                  </a>
                </td>
              </tr>
              <tr v-if="!tickets.data.length">
                <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="6">
                  Aucun ticket pour le moment.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="px-4 py-3 flex items-center justify-between text-sm text-gray-500 border-t">
          <span>Page {{ tickets.current_page }} / {{ tickets.last_page }}</span>
          <span>{{ tickets.from }} - {{ tickets.to }} sur {{ tickets.total }}</span>
        </div>
      </section>
    </div>
  </div>
</template>

