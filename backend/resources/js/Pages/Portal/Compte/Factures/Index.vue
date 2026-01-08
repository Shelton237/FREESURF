<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { router, useForm } from '@inertiajs/vue3'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  factures: Object,
  filters: Object,
  statuts: Array,
})

const filterForm = useForm({
  statut: props.filters?.statut || '',
})

const applyFilters = () => {
  router.get('/portal/compte/factures', filterForm.data(), {
    preserveState: true,
    preserveScroll: true,
  })
}

const goToPage = (url) => {
  if (!url) return
  router.get(url, filterForm.data(), {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="bg-gray-50 min-h-[70vh] px-4 py-10">
    <div class="max-w-6xl mx-auto space-y-6">
      <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <p class="text-sm uppercase text-rose-500 font-semibold">Factures</p>
          <h1 class="text-3xl font-bold text-gray-900">Historique de facturation</h1>
          <p class="text-sm text-gray-600">Consultez vos factures, téléchargez les PDF et vérifiez les paiements.</p>
        </div>
        <a href="/portal/compte" class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">Retour au tableau de bord</a>
      </header>

      <section class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
          <div class="w-full sm:w-64">
            <label class="block text-sm text-gray-600">
              Statut
              <select v-model="filterForm.statut" class="w-full border rounded-lg p-3 mt-1" @change="applyFilters">
                <option value="">Tous les statuts</option>
                <option v-for="statut in statuts" :key="statut" :value="statut">
                  {{ statut }}
                </option>
              </select>
            </label>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 uppercase text-xs">
                <th class="py-2 pr-4">Numéro</th>
                <th class="py-2 pr-4">Client</th>
                <th class="py-2 pr-4 text-right">Montant</th>
                <th class="py-2 pr-4">Période</th>
                <th class="py-2 pr-4">Echéance</th>
                <th class="py-2 pr-4">Statut</th>
                <th class="py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="facture in factures.data" :key="facture.id" class="border-t text-gray-700">
                <td class="py-3 pr-4 font-semibold text-gray-900">{{ facture.numero }}</td>
                <td class="py-3 pr-4">
                  <div class="font-medium">{{ facture.client.nom }}</div>
                  <div class="text-xs text-gray-500">#{{ facture.client.code }}</div>
                </td>
                <td class="py-3 pr-4 text-right font-semibold">
                  {{ Number(facture.montant).toLocaleString('fr-FR') }} XAF
                </td>
                <td class="py-3 pr-4">{{ facture.periode }}</td>
                <td class="py-3 pr-4">{{ facture.due_date || 'NC' }}</td>
                <td class="py-3 pr-4">
                  <span class="px-2 py-1 rounded-full text-xs uppercase" :class="facture.statut === 'payee' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                    {{ facture.statut }}
                  </span>
                </td>
                <td class="py-3">
                  <div class="flex gap-2 text-xs">
                    <a
                      class="px-3 py-1 rounded-lg border text-gray-700 hover:bg-gray-50"
                      :href="`/portal/compte/factures/${facture.id}`"
                    >
                      Détails
                    </a>
                    <a
                      class="px-3 py-1 rounded-lg border text-rose-600 hover:bg-rose-50"
                      :href="`/portal/compte/factures/${facture.id}/download`"
                    >
                      PDF
                    </a>
                  </div>
                </td>
              </tr>
              <tr v-if="!factures.data.length">
                <td class="py-6 text-center text-sm text-gray-500" colspan="7">Aucune facture trouvée.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <nav class="flex flex-wrap items-center justify-between gap-2 pt-4">
          <button
            class="px-3 py-1 rounded border text-sm"
            :class="factures.prev_page_url ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400 border-gray-200 cursor-not-allowed'"
            type="button"
            :disabled="!factures.prev_page_url"
            @click="goToPage(factures.prev_page_url)"
          >
            Précédent
          </button>
          <div class="text-sm text-gray-500">
            Page {{ factures.current_page }} / {{ factures.last_page }}
          </div>
          <button
            class="px-3 py-1 rounded border text-sm"
            :class="factures.next_page_url ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400 border-gray-200 cursor-not-allowed'"
            type="button"
            :disabled="!factures.next_page_url"
            @click="goToPage(factures.next_page_url)"
          >
            Suivant
          </button>
        </nav>
      </section>
    </div>
  </div>
</template>

