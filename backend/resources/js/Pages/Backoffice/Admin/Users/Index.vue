<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { Link, router } from '@inertiajs/vue3'
defineProps({ users: Object })
defineOptions({ layout: BackofficeLayout })

const destroyUser = (id) => {
  if (confirm('Supprimer cet utilisateur ?')) {
    router.delete(`/backoffice/admin/users/${id}`)
  }
}
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Utilisateurs</h1>
      <Link href="/backoffice/admin/users/create" class="px-3 py-2 bg-brand text-white rounded">Ajouter</Link>
    </div>
    <table class="min-w-full text-left border">
      <thead>
        <tr class="bg-gray-50">
          <th class="p-2 border">Nom</th>
          <th class="p-2 border">Email</th>
          <th class="p-2 border">Rôle</th>
          <th class="p-2 border"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users.data" :key="u.id" class="border-b">
          <td class="p-2 border">{{ u.name }}</td>
          <td class="p-2 border">{{ u.email }}</td>
          <td class="p-2 border uppercase">{{ u.role }}</td>
          <td class="p-2 border text-sm">
            <Link :href="`/backoffice/admin/users/${u.id}/edit`" class="text-blue-600 mr-2">Modifier</Link>
            <button class="text-red-600" @click="destroyUser(u.id)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
