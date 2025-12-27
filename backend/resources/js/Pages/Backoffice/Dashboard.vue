<script setup>
</script>

<template>
  <div class="min-h-screen p-6">
    <h1 class="text-3xl font-bold">Backoffice — Dashboard</h1>
    <p class="mt-2 text-gray-600">Accès restreint. Vue d’ensemble: clients, BTS, factures, paiements.</p>
    <ul class="mt-4 list-disc pl-6 text-gray-700">
      <li>Clients actifs / par BTS</li>
      <li>Factures générées / payées / impayées</li>
      <li>Notifications envoyées</li>
    </ul>
    <div class="mt-6 flex gap-3">
      <a href="/backoffice/bts" class="px-3 py-2 bg-gray-100 rounded border">Gérer les BTS</a>
      <a href="/backoffice/clients" class="px-3 py-2 bg-gray-100 rounded border">Gérer les clients</a>
    </div>
  </div>
</template>
