<script setup>
defineProps({ items: Object })
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">BTS</h1>
      <a href="/backoffice/bts/create" class="px-3 py-2 bg-red-600 text-white rounded">Nouvelle BTS</a>
    </div>
    <table class="min-w-full text-left border">
      <thead>
        <tr class="bg-gray-50">
          <th class="p-2 border">Code</th>
          <th class="p-2 border">Ville</th>
          <th class="p-2 border">GPS</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="b in items.data" :key="b.id" class="border-b">
          <td class="p-2 border">{{ b.code }}</td>
          <td class="p-2 border">{{ b.ville }}</td>
          <td class="p-2 border">{{ b.lat ?? '-' }}, {{ b.lng ?? '-' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

