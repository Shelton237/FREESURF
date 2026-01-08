<script setup>
import TechLayout from '@/Layouts/TechLayout.vue'
import { computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

defineOptions({ layout: TechLayout })

const props = defineProps({
  items: Object,
  filters: Object,
  stats: Object,
  statusOptions: Array,
})

const filterForm = useForm({
  statut: props.filters?.statut || '',
})

const ctas = computed(() => [
  { label: 'A faire', value: props.stats?.pending || 0 },
  { label: 'En cours', value: props.stats?.on_site || 0 },
  { label: 'Terminees', value: props.stats?.completed || 0 },
])

const applyFilters = () => {
  router.get('/tech', filterForm.data(), {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="px-4 py-8">
    <div class="max-w-6xl mx-auto space-y-6">
      <header class="bg-white rounded-2xl shadow-sm border p-6 space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Feuille de route</p>
            <h1 class="text-2xl font-semibold text-gray-900">Interventions assignées</h1>
            <p class="text-sm text-gray-500">Consultez vos visites planifiées et accédez aux fiches détaillées.</p>
          </div>
          <div class="flex flex-col items-end gap-3 sm:flex-row sm:items-center sm:gap-4">
            <div class="text-right">
              <p class="text-sm text-gray-500">Total</p>
              <p class="text-3xl font-bold text-brand">{{ items.total }}</p>
            </div>
            <a
              href="/tech/work-orders/create"
              class="px-4 py-2 rounded-lg border border-brand text-brand text-sm font-semibold hover:bg-rose-50"
            >
              Nouvelle etude
            </a>
          </div>
        </div>
        <div class="flex flex-wrap gap-3">
          <article
            v-for="cta in ctas"
            :key="cta.label"
            class="px-4 py-3 rounded-xl border bg-gray-50 flex-1 min-w-[120px]"
          >
            <p class="text-xs uppercase text-gray-500">{{ cta.label }}</p>
            <p class="text-2xl font-semibold text-gray-900">{{ cta.value }}</p>
          </article>
        </div>
        <div class="flex flex-wrap gap-4 items-end">
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

      <section class="bg-white rounded-2xl shadow-sm border">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 uppercase text-xs border-b">
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Client</th>
                <th class="px-4 py-3">BTS</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Planifiée</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="w in items.data"
                :key="w.id"
                class="border-b last:border-0 hover:bg-gray-50 transition"
              >
                <td class="px-4 py-3 font-semibold uppercase">{{ w.type }}</td>
                <td class="px-4 py-3">
                  <p class="font-medium text-gray-900">{{ w.client?.nom ?? '—' }}</p>
                  <p class="text-xs text-gray-500">{{ w.client?.telephone ?? '—' }}</p>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ w.bts?.code ?? '—' }}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 rounded-full text-xs uppercase bg-slate-100 text-slate-700">
                    {{ w.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ w.scheduled_at ?? 'Non défini' }}</td>
                <td class="px-4 py-3 text-right">
                  <a
                    :href="`/tech/work-orders/${w.id}`"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border text-xs font-semibold text-brand hover:bg-rose-50"
                  >
                    Ouvrir
                    <span aria-hidden="true">→</span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="px-4 py-3 flex items-center justify-between text-sm text-gray-500 border-t">
          <span>Page {{ items.current_page }} / {{ items.last_page }}</span>
          <span>{{ items.from }} - {{ items.to }} sur {{ items.total }}</span>
        </div>
      </section>
    </div>
  </div>
</template>
