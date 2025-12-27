<script setup>
defineProps({ items: Object })
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Clients</h1>
      <a href="/backoffice/clients/create" class="px-3 py-2 bg-red-600 text-white rounded">Nouveau Client</a>
    </div>
    <table class="min-w-full text-left border">
      <thead>
        <tr class="bg-gray-50">
          <th class="p-2 border">Code</th>
          <th class="p-2 border">Nom</th>
          <th class="p-2 border">Téléphone</th>
          <th class="p-2 border">BTS</th>
          <th class="p-2 border">Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="c in items.data" :key="c.id" class="border-b">
          <td class="p-2 border">{{ c.code }}</td>
          <td class="p-2 border">{{ c.nom }}</td>
          <td class="p-2 border">{{ c.telephone }}</td>
          <td class="p-2 border">{{ c.bts?.code ?? '-' }}</td>
          <td class="p-2 border">{{ c.statut }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

