<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { computed } from 'vue'

defineOptions({ layout: BackofficeLayout })

const props = defineProps({
  summary: Array,
  recent: Array,
})

const hotspots = computed(() => (props.summary || []).filter((row) => row.blocked >= 2))
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">Analyse des Site Surveys</h1>
        <p class="text-gray-600">Repérez rapidement les zones bloquées ou les surveys à suivre.</p>
      </div>
      <a href="/backoffice" class="text-sm text-blue-600">← Dashboard</a>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
      <div class="border rounded bg-white p-4">
        <h2 class="font-semibold mb-3">Hotspots (≥ 2 surveys indisponibles)</h2>
        <div v-if="hotspots.length" class="space-y-3">
          <div
            v-for="row in hotspots"
            :key="row.bts"
            class="border rounded p-3"
          >
            <div class="flex items-center justify-between">
              <div>
                <div class="font-semibold">{{ row.bts }}</div>
                <div class="text-xs text-gray-500">{{ row.ville }}</div>
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-red-600">{{ row.blocked }}</div>
                <div class="text-xs text-gray-500">surveys bloqués / {{ row.total }}</div>
              </div>
            </div>
            <div class="text-xs text-gray-500 mt-1">
              {{ row.ok }} surveys disponibles
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500">Aucun hotspot détecté.</p>
      </div>

      <div class="border rounded bg-white p-4">
        <h2 class="font-semibold mb-3">Synthèse globale</h2>
        <table class="min-w-full text-sm">
          <thead class="text-xs uppercase text-gray-500 border-b">
            <tr>
              <th class="py-2 text-left">BTS</th>
              <th class="py-2 text-left">Ville</th>
              <th class="py-2 text-right">Total</th>
              <th class="py-2 text-right text-red-600">Bloqués</th>
              <th class="py-2 text-right text-green-600">Ok</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in summary" :key="row.bts" class="border-b last:border-0">
              <td class="py-2 font-semibold">{{ row.bts }}</td>
              <td class="py-2 text-gray-500">{{ row.ville }}</td>
              <td class="py-2 text-right">{{ row.total }}</td>
              <td class="py-2 text-right text-red-600 font-semibold">{{ row.blocked }}</td>
              <td class="py-2 text-right text-green-600">{{ row.ok }}</td>
            </tr>
            <tr v-if="!summary?.length">
              <td colspan="5" class="py-4 text-center text-gray-500 text-sm">Aucune donnée survey.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="border rounded bg-white p-4">
      <h2 class="font-semibold mb-3">Derniers surveys</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-xs uppercase text-gray-500 border-b">
            <tr>
              <th class="py-2 text-left">ID</th>
              <th class="py-2 text-left">Client</th>
              <th class="py-2 text-left">BTS</th>
              <th class="py-2 text-left">Résultat</th>
              <th class="py-2 text-left">Motif</th>
              <th class="py-2 text-left">Suivi</th>
              <th class="py-2 text-left">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in recent" :key="row.id" class="border-b last:border-0">
              <td class="py-2 text-gray-500">#{{ row.id }}</td>
              <td class="py-2">
                <div v-if="row.client">
                  <div class="font-semibold">{{ row.client.nom }}</div>
                  <div class="text-xs text-gray-500">{{ row.client.statut }}</div>
                </div>
                <div v-else class="text-xs text-gray-500">N/A</div>
              </td>
              <td class="py-2">
                <div class="font-semibold">{{ row.bts ?? 'N/A' }}</div>
                <div class="text-xs text-gray-500">{{ row.ville }}</div>
              </td>
              <td class="py-2">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    row.result === 'available' ? 'bg-green-100 text-green-700' :
                    row.result === 'not_available' ? 'bg-red-100 text-red-700' :
                    'bg-gray-100 text-gray-700',
                  ]"
                >
                  {{
                    row.result === 'available'
                      ? 'Disponible'
                      : row.result === 'not_available'
                        ? 'Indisponible'
                        : 'En attente'
                  }}
                </span>
              </td>
              <td class="py-2 text-gray-600">{{ row.reason || '—' }}</td>
              <td class="py-2">
                <span v-if="row.follow_up" class="text-xs text-orange-600 font-semibold">Suivi demandé</span>
                <span v-else class="text-xs text-gray-500">—</span>
              </td>
              <td class="py-2 text-gray-500">{{ row.completed_at ?? '—' }}</td>
            </tr>
            <tr v-if="!recent?.length">
              <td colspan="7" class="py-4 text-center text-gray-500 text-sm">Aucun survey récent.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
