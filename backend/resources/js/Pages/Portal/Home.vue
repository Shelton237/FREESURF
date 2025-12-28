<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineOptions({ layout: PublicLayout })
</script>

<template>
  <div class="min-h-[70vh] bg-gradient-to-b from-rose-50 via-white to-white">
    <div class="max-w-5xl mx-auto px-4 py-12 space-y-8 text-center">
      <div class="space-y-3">
        <p class="text-sm uppercase tracking-wide text-rose-500 font-semibold">CuWiP · Portail client</p>
        <h1 class="text-4xl font-bold text-gray-900">Demander un nouvel abonnement ou suivre votre dossier</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          En quelques clics, soumettez votre demande, partagez la localisation du site et suivez votre dossier dans un espace dédié.
        </p>
      </div>
      <div class="grid md:grid-cols-2 gap-6">
        <a href="/portal/demandes/nouvelle" class="bg-white rounded-2xl shadow-sm p-6 text-left border hover:border-rose-400 transition">
          <p class="text-xs uppercase text-gray-500 font-semibold">Nouvelle demande</p>
          <h2 class="text-xl font-bold text-gray-900 mt-1">Créer une demande d’abonnement</h2>
          <p class="text-sm text-gray-600 mt-2">Guidage en 3 étapes, géolocalisation et suivi par nos équipes.</p>
        </a>
        <a href="/portal/compte/login" class="bg-white rounded-2xl shadow-sm p-6 text-left border hover:border-rose-400 transition">
          <p class="text-xs uppercase text-gray-500 font-semibold">Suivre mon dossier</p>
          <h2 class="text-xl font-bold text-gray-900 mt-1">Accéder à mon espace client</h2>
          <p class="text-sm text-gray-600 mt-2">Consultez l’avancement, mettez à jour vos coordonnées et contactez le support.</p>
        </a>
      </div>
    </div>
  </div>
</template>
