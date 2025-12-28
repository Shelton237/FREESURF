<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: BackofficeLayout })

const props = defineProps({
  bts: Object,
  clients: Object,
})

const formatCoord = (value) => (Number.isFinite(value) ? Number(value).toFixed(6) : '—')
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <p class="text-sm uppercase tracking-wide text-gray-500">BTS</p>
        <h1 class="text-3xl font-bold">{{ props.bts?.code }}</h1>
        <p class="text-gray-600">Ville : {{ props.bts?.ville || '—' }}</p>
      </div>
      <div class="text-right text-sm text-gray-500 space-y-1">
        <div>
          <span class="font-semibold text-gray-900">{{ props.bts?.clients_total ?? 0 }}</span> clients
          dont <span class="font-semibold text-brand">{{ props.bts?.clients_actifs ?? 0 }}</span> actifs
        </div>
        <Link href="/backoffice/bts" class="text-brand underline text-sm">← Retour liste BTS</Link>
      </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
      <div class="p-4 border rounded bg-white space-y-1">
        <p class="text-xs uppercase text-gray-500">Code</p>
        <p class="text-lg font-semibold">{{ props.bts?.code }}</p>
      </div>
      <div class="p-4 border rounded bg-white space-y-1">
        <p class="text-xs uppercase text-gray-500">Latitude</p>
        <p class="text-lg font-semibold">{{ formatCoord(props.bts?.lat) }}</p>
      </div>
      <div class="p-4 border rounded bg-white space-y-1">
        <p class="text-xs uppercase text-gray-500">Longitude</p>
        <p class="text-lg font-semibold">{{ formatCoord(props.bts?.lng) }}</p>
      </div>
    </div>

    <div class="p-4 border rounded bg-white">
      <h2 class="text-xl font-semibold mb-4">Clients reliés</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500 uppercase text-xs border-b">
            <tr>
              <th class="py-2 text-left">Client</th>
              <th class="py-2 text-left">Statut</th>
              <th class="py-2 text-left">Téléphone</th>
              <th class="py-2 text-left">Coordonnées</th>
              <th class="py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in props.clients?.data || []" :key="client.id" class="border-b last:border-0">
              <td class="py-2">
                <div class="font-semibold">{{ client.nom }}</div>
                <div class="text-xs text-gray-500">{{ client.code }}</div>
              </td>
              <td class="py-2 text-gray-600 capitalize">{{ client.statut }}</td>
              <td class="py-2 text-gray-600">{{ client.telephone || '—' }}</td>
              <td class="py-2 text-gray-500 text-xs">
                Lat {{ formatCoord(client.lat) }} / Lng {{ formatCoord(client.lng) }}
              </td>
              <td class="py-2 text-right">
                <Link :href="`/backoffice/clients/${client.id}`" class="text-brand text-sm underline">Voir</Link>
              </td>
            </tr>
            <tr v-if="!props.clients?.data?.length">
              <td colspan="5" class="py-4 text-center text-gray-500 text-sm">Aucun client relié à cette BTS.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="props.clients?.links?.length" class="flex flex-wrap gap-2 mt-4 text-sm">
        <template v-for="link in props.clients.links" :key="link.label">
          <span v-if="!link.url" class="px-3 py-1 border rounded text-gray-400" v-html="link.label" />
          <Link
            v-else
            :href="link.url"
            class="px-3 py-1 border rounded"
            :class="[{ 'bg-brand text-white border-brand': link.active }]"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </div>
</template>
