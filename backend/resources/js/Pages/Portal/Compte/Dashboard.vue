<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  compte: Object,
  demandes: Array,
})

const statusLabels = {
  soumise: 'Soumise',
  en_etude: 'En étude',
  planification: 'Planification',
  validee: 'Validée',
  installee: 'Installée',
  close: 'Clôturée',
}

const logout = () => {
  router.post('/portal/compte/logout')
}
</script>

<template>
  <div class="bg-gray-50 min-h-[70vh] px-4 py-10">
    <div class="max-w-5xl mx-auto space-y-8">
      <header class="bg-white rounded-2xl shadow-sm p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <p class="text-sm text-gray-500">Bienvenue</p>
          <h1 class="text-2xl font-bold text-gray-900">{{ compte.nom }}</h1>
          <p class="text-sm text-gray-600">Téléphone: {{ compte.telephone }} <span v-if="compte.email">· {{ compte.email }}</span></p>
        </div>
        <button class="px-4 py-2 rounded-lg border text-sm text-gray-700 hover:bg-gray-100" type="button" @click="logout">
          Déconnexion
        </button>
      </header>

      <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Mes demandes</h2>
            <p class="text-sm text-gray-500">Suivez l’avancement de vos demandes d’abonnement.</p>
          </div>
          <a href="/portal/demandes/nouvelle" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm">Nouvelle demande</a>
        </div>
        <div class="space-y-3">
          <article
            v-for="demande in demandes"
            :key="demande.id"
            class="border rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4"
          >
            <div>
              <p class="text-xs uppercase text-gray-500">#{{ demande.id }} · {{ demande.type }}</p>
              <p class="text-base font-semibold text-gray-900">{{ demande.adresse || 'Adresse non renseignée' }}</p>
              <p class="text-xs text-gray-500">Créée le {{ demande.created_at }}</p>
            </div>
            <div class="text-sm">
              <p class="text-gray-500">Statut</p>
              <p class="text-lg font-bold text-rose-600">
                {{ statusLabels[demande.statut] || demande.statut }}
              </p>
            </div>
          </article>
          <p v-if="!demandes.length" class="text-sm text-gray-500">Aucune demande pour le moment.</p>
        </div>
      </section>
    </div>
  </div>
</template>
