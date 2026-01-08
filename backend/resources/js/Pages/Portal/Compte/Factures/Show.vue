<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  facture: Object,
  paiements: Array,
})
</script>

<template>
  <div class="bg-gray-50 min-h-[70vh] px-4 py-10">
    <div class="max-w-4xl mx-auto space-y-6">
      <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <p class="text-sm uppercase text-rose-500 font-semibold">Facture</p>
          <h1 class="text-3xl font-bold text-gray-900">{{ facture.numero }}</h1>
          <p class="text-sm text-gray-600">Client {{ facture.client.nom }} (#{{ facture.client.code }})</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <a
            :href="`/portal/compte/factures/${facture.id}/download`"
            class="px-4 py-2 rounded-lg border text-sm text-rose-600 hover:bg-rose-50"
          >
            Télécharger le PDF
          </a>
          <a href="/portal/compte/factures" class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">Retour</a>
        </div>
      </header>

      <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div class="grid sm:grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-gray-500 uppercase text-xs">Période</p>
            <p class="text-gray-900 font-semibold">{{ facture.periode }}</p>
          </div>
          <div>
            <p class="text-gray-500 uppercase text-xs">Échéance</p>
            <p class="text-gray-900 font-semibold">{{ facture.due_date || 'NC' }}</p>
          </div>
          <div>
            <p class="text-gray-500 uppercase text-xs">Montant</p>
            <p class="text-gray-900 font-semibold">
              {{ Number(facture.montant).toLocaleString('fr-FR') }} XAF
            </p>
          </div>
          <div>
            <p class="text-gray-500 uppercase text-xs">Statut</p>
            <span
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
              :class="facture.statut === 'payee' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
            >
              {{ facture.statut }}
            </span>
          </div>
          <div>
            <p class="text-gray-500 uppercase text-xs">Emission</p>
            <p class="text-gray-900 font-semibold">{{ facture.issued_at || 'NC' }}</p>
          </div>
          <div>
            <p class="text-gray-500 uppercase text-xs">Paiement</p>
            <p class="text-gray-900 font-semibold">{{ facture.paid_at || 'En attente' }}</p>
          </div>
        </div>

        <div class="border rounded-xl p-4 text-sm text-gray-600">
          <p>
            Contact facturation : <span class="font-semibold">{{ facture.client.nom }}</span> •
            {{ facture.client.telephone }}
          </p>
          <p>Montant dû pour la période {{ facture.periode }}. Nous restons disponibles pour toute question.</p>
        </div>
      </section>

      <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Paiements enregistrés</h2>
            <p class="text-sm text-gray-500">Historique des paiements reçus pour cette facture.</p>
          </div>
        </div>
        <div class="space-y-3">
          <article
            v-for="paiement in paiements"
            :key="paiement.id"
            class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
          >
            <div>
              <p class="text-sm font-semibold text-gray-900">{{ Number(paiement.montant).toLocaleString('fr-FR') }} XAF</p>
              <p class="text-xs text-gray-500">Mode : {{ paiement.mode }} • Réf : {{ paiement.reference || '-' }}</p>
            </div>
            <p class="text-sm text-gray-500">Reçu le {{ paiement.paid_at || 'NC' }}</p>
          </article>
          <p v-if="!paiements.length" class="text-sm text-gray-500">Aucun paiement enregistré pour le moment.</p>
        </div>
      </section>
    </div>
  </div>
</template>

