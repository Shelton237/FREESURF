<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import GeoMap from '@/Components/GeoMap.vue'
import { computed, ref } from 'vue'

defineOptions({ layout: BackofficeLayout })

const props = defineProps({
  stats: Object,
  clientsByBts: Array,
  series: Object,
  map: Object,
  filters: Object,
})

const chartMax = computed(() => Math.max(1, ...(props.series?.clients || [])))

const selectedVille = ref('')
const selectedStatut = ref('')
const mapFullscreen = ref(false)

const filteredBts = computed(() => {
  const ville = selectedVille.value
  return (props.map?.bts || []).filter((bts) => !ville || bts.ville === ville)
})

const filteredMapClients = computed(() => {
  const statut = selectedStatut.value
  return (props.map?.clients || []).filter((client) => !statut || client.statut === statut)
})

const filteredClientsByBts = computed(() => {
  const ville = selectedVille.value
  return (props.clientsByBts || []).filter((row) => !ville || row.ville === ville)
})

const latestClients = computed(() => filteredMapClients.value.slice(0, 5))

const toggleMapFullscreen = () => {
  mapFullscreen.value = !mapFullscreen.value
}

const closeFullscreen = () => {
  mapFullscreen.value = false
}

const formatCoord = (value) => (Number.isFinite(value) ? value.toFixed(4) : '-')
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">Backoffice — Dashboard</h1>
        <p class="mt-1 text-gray-600">Vue d’ensemble des clients, BTS, interventions et demandes.</p>
      </div>
      <div class="flex gap-2">
        <a href="/backoffice/bts" class="px-3 py-2 bg-gray-100 rounded border text-sm">BTS</a>
        <a href="/backoffice/clients" class="px-3 py-2 bg-gray-100 rounded border text-sm">Clients</a>
        <a href="/backoffice/admin/users" class="px-3 py-2 bg-gray-100 rounded border text-sm">Utilisateurs</a>
      </div>
    </div>

    <div class="grid md:grid-cols-5 gap-4">
      <div class="p-4 border rounded bg-white">
        <div class="text-xs uppercase text-gray-500">Clients</div>
        <div class="text-3xl font-bold">{{ props.stats?.clients_total ?? 0 }}</div>
        <div class="text-xs text-gray-500">{{ props.stats?.clients_actifs ?? 0 }} actifs</div>
      </div>
      <div class="p-4 border rounded bg-white">
        <div class="text-xs uppercase text-gray-500">BTS</div>
        <div class="text-3xl font-bold">{{ props.stats?.bts ?? 0 }}</div>
      </div>
      <div class="p-4 border rounded bg-white">
        <div class="text-xs uppercase text-gray-500">Demandes en attente</div>
        <div class="text-3xl font-bold text-brand">{{ props.stats?.demandes_en_attente ?? 0 }}</div>
      </div>
      <div class="p-4 border rounded bg-white">
        <div class="text-xs uppercase text-gray-500">Interventions en cours</div>
        <div class="text-3xl font-bold">{{ props.stats?.interventions_en_cours ?? 0 }}</div>
      </div>
      <div class="p-4 border rounded bg-white">
        <div class="text-xs uppercase text-gray-500">Portail</div>
        <div class="text-sm text-gray-600">Suivre les demandes</div>
        <a href="/portal" class="text-brand text-sm">Ouvrir le portail</a>
      </div>
    </div>

    <div v-if="mapFullscreen" class="fixed inset-0 bg-black/40 z-40" @click="closeFullscreen"></div>

    <div class="grid lg:grid-cols-5 gap-6">
      <div
        :class="[
          'lg:col-span-3 border rounded bg-white transition-all',
          mapFullscreen ? 'fixed inset-4 lg:inset-8 z-50 shadow-2xl p-4' : 'p-4',
        ]"
      >
        <div class="flex flex-col gap-3 mb-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="font-semibold">Carte terrain</h2>
            <p class="text-sm text-gray-500">BTS et clients géolocalisés</p>
          </div>
          <div class="text-xs text-gray-500 text-right">
            {{ filteredBts.length }} BTS • {{ filteredMapClients.length }} clients
          </div>
        </div>
        <div class="flex flex-col gap-2 mb-3 md:flex-row md:items-center md:justify-between">
          <a href="/backoffice/work-orders/surveys" class="text-xs text-brand underline">
            Voir l'analyse des site surveys
          </a>
          <button
            class="px-3 py-1 text-xs border rounded hover:bg-gray-100"
            @click="toggleMapFullscreen"
          >
            {{ mapFullscreen ? 'Fermer le plein écran' : 'Afficher en plein écran' }}
          </button>
        </div>
        <div class="grid md:grid-cols-2 gap-3 mb-3 text-sm">
          <label class="flex flex-col gap-1">
            <span class="text-xs uppercase text-gray-500">Filtrer par ville (BTS)</span>
            <select v-model="selectedVille" class="border rounded px-2 py-1">
              <option value="">Toutes les villes</option>
              <option v-for="ville in props.filters?.villes || []" :key="ville" :value="ville">
                {{ ville }}
              </option>
            </select>
          </label>
          <label class="flex flex-col gap-1">
            <span class="text-xs uppercase text-gray-500">Filtrer par statut client</span>
            <select v-model="selectedStatut" class="border rounded px-2 py-1">
              <option value="">Tous les statuts</option>
              <option v-for="statut in props.filters?.statuts || []" :key="statut" :value="statut">
                {{ statut }}
              </option>
            </select>
          </label>
        </div>
        <GeoMap
          :center="props.map?.center"
          :bts="filteredBts"
          :clients="filteredMapClients"
          :bounds="props.map?.bounds"
          :fullscreen="mapFullscreen"
          :height="mapFullscreen ? '70vh' : '20rem'"
          :zoom="11"
        />
        <p class="mt-3 text-xs text-gray-500">
          Zoomer/déplacer la carte et cliquer sur les marqueurs pour ouvrir les détails terrain.
        </p>
      </div>
      <div class="lg:col-span-2 p-4 border rounded bg-white space-y-3">
        <div>
          <h3 class="font-semibold">Clients récemment géolocalisés</h3>
          <p class="text-sm text-gray-500">Top 5 des derniers enregistrements GPS</p>
        </div>
        <div v-if="latestClients.length" class="space-y-2">
          <div v-for="client in latestClients" :key="client.id" class="border rounded p-2">
            <div class="text-sm font-semibold">{{ client.nom }}</div>
            <div class="text-xs text-gray-500">
              {{ client.code }} · {{ client.statut }}
            </div>
            <div v-if="client.bts" class="text-xs text-gray-500">
              BTS {{ client.bts }}
            </div>
            <div class="text-xs text-gray-400">
              Lat {{ formatCoord(client.lat) }} / Lng {{ formatCoord(client.lng) }}
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500">Aucun client avec coordonnées GPS récentes.</p>
        <p class="text-xs text-gray-500">
          La carte récupère jusqu'à 100 clients disposant de coordonnées valides pour faciliter l'analyse terrain.
        </p>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="p-4 border rounded bg-white">
        <h2 class="font-semibold mb-3">Nouveaux clients (6 derniers mois)</h2>
        <div class="space-y-2">
          <div v-for="(label, idx) in props.series?.labels || []" :key="label" class="flex items-center gap-4">
            <div class="w-16 text-sm text-gray-500">{{ label }}</div>
            <div class="flex-1 h-2 bg-gray-100 rounded">
              <div class="h-2 bg-brand rounded" :style="{ width: ((props.series.clients[idx] || 0)/chartMax)*100 + '%' }"></div>
            </div>
            <div class="w-8 text-right text-sm">{{ props.series.clients[idx] || 0 }}</div>
          </div>
        </div>
      </div>
      <div class="p-4 border rounded bg-white">
        <h2 class="font-semibold mb-3">Top BTS par clients</h2>
        <table class="min-w-full text-left text-sm">
          <thead>
            <tr class="text-gray-500 uppercase text-xs">
              <th class="pb-2">BTS</th>
              <th class="pb-2">Ville</th>
              <th class="pb-2 text-right">Clients</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in filteredClientsByBts" :key="row.bts" class="border-t">
              <td class="py-2">{{ row.bts }}</td>
              <td class="py-2 text-gray-500">{{ row.ville }}</td>
              <td class="py-2 text-right font-semibold">{{ row.total }}</td>
            </tr>
            <tr v-if="!filteredClientsByBts?.length">
              <td colspan="3" class="py-4 text-center text-gray-500 text-sm">Aucune donnée</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
