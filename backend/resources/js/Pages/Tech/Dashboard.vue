<script setup>
defineProps({ items: Object })
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Interventions assignées</h1>
    <table class="min-w-full text-left border">
      <thead>
        <tr class="bg-gray-50">
          <th class="p-2 border">Type</th>
          <th class="p-2 border">Client</th>
          <th class="p-2 border">Statut</th>
          <th class="p-2 border">Planifiée</th>
          <th class="p-2 border"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="w in items.data" :key="w.id" class="border-b">
          <td class="p-2 border uppercase">{{ w.type }}</td>
          <td class="p-2 border">{{ w.client?.nom ?? '-' }}</td>
          <td class="p-2 border">{{ w.status }}</td>
          <td class="p-2 border">{{ w.scheduled_at ?? '-' }}</td>
          <td class="p-2 border"><a :href="`/tech/work-orders/${w.id}`" class="text-blue-600">Ouvrir</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

