<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: BackofficeLayout })

const props = defineProps({
  demandes: Object,
  filters: Object,
  options: Object,
})

const filterState = reactive({
  statut: props.filters?.statut || '',
  type: props.filters?.type || '',
  search: props.filters?.search || '',
})

const applyFilters = () => {
  router.get('/backoffice/demandes', filterState, {
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterState.statut = ''
  filterState.type = ''
  filterState.search = ''
  applyFilters()
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold">Demandes clients</h1>
        <p class="text-gray-600">Suivi des demandes d’abonnement et de réabonnement soumises par le portail ou le terrain.</p>
      </div>
      <Link href="/backoffice" class="text-sm text-blue-600">← Dashboard</Link>
    </div>

    <div class="border rounded bg-white p-4 space-y-4">
      <div class="grid md:grid-cols-4 gap-3 text-sm">
        <label class="flex flex-col gap-1">
          <span class="text-xs uppercase text-gray-500">Statut</span>
          <select v-model="filterState.statut" class="border rounded px-2 py-1">
            <option value="">Tous</option>
            <option v-for="statut in props.options?.statuts || []" :key="statut" :value="statut">{{ statut }}</option>
          </select>
        </label>
        <label class="flex flex-col gap-1">
          <span class="text-xs uppercase text-gray-500">Type</span>
          <select v-model="filterState.type" class="border rounded px-2 py-1">
            <option value="">Tous</option>
            <option v-for="type in props.options?.types || []" :key="type" :value="type">{{ type }}</option>
          </select>
        </label>
        <label class="md:col-span-2 flex flex-col gap-1">
          <span class="text-xs uppercase text-gray-500">Recherche (nom, tel, email)</span>
          <input v-model="filterState.search" class="border rounded px-2 py-1" placeholder="Nom, téléphone..." />
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
              <th class="py-2 text-left">Demande</th>
              <th class="py-2 text-left">Client</th>
              <th class="py-2 text-left">Coordonnées</th>
              <th class="py-2 text-left">Statut</th>
              <th class="py-2 text-left">Créée le</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="demande in demandes.data" :key="demande.id" class="border-b last:border-0">
              <td class="py-2">
                <div class="font-semibold">#{{ demande.id }} · {{ demande.type }}</div>
                <div class="text-xs text-gray-500">{{ demande.email_facturation || '—' }}</div>
              </td>
              <td class="py-2">
                <div class="font-semibold">{{ demande.nom }}</div>
                <div class="text-xs text-gray-500">{{ demande.client?.code || 'Prospect' }}</div>
              </td>
              <td class="py-2 text-xs text-gray-500">
                {{ demande.telephone }}<br />
                {{ demande.adresse || '—' }}
              </td>
              <td class="py-2">
                <span class="px-2 py-1 rounded bg-gray-100 text-xs capitalize">{{ demande.statut }}</span>
              </td>
              <td class="py-2 text-xs text-gray-500">{{ demande.created_at ?? '—' }}</td>
            </tr>
            <tr v-if="!demandes.data?.length">
              <td colspan="5" class="py-4 text-center text-gray-500 text-sm">Aucune demande.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex justify-end gap-2 text-sm" v-if="demandes.links?.length">
        <template v-for="link in demandes.links" :key="link.url ?? link.label">
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
