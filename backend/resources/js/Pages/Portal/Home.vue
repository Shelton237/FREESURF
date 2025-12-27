<script setup>
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center p-6">
    <h1 class="text-3xl font-bold text-red-600">CuWiP — Portail Client</h1>
    <p class="mt-2 text-gray-600">Soumettre une demande d’abonnement ou de réabonnement, consulter le statut et télécharger vos factures.</p>
    <div class="mt-6 grid gap-4 w-full max-w-xl">
      <a href="#" class="block rounded border p-4 hover:bg-gray-50">Demande d’abonnement</a>
      <a href="#" class="block rounded border p-4 hover:bg-gray-50">Demande de réabonnement</a>
      <a href="#" class="block rounded border p-4 hover:bg-gray-50">Mes factures</a>
    </div>
  </div>
</template>

