<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const form = ref({ name: '', email: '', password: '', role: 'backoffice' })
const submitting = ref(false)
const submit = () => {
  submitting.value = true
  router.post('/backoffice/admin/users', form.value, { onFinish: () => (submitting.value = false) })
}
</script>

<template>
  <div class="p-6 max-w-lg">
    <h1 class="text-2xl font-semibold mb-4">Nouvel utilisateur</h1>
    <div class="space-y-3">
      <div>
        <label class="block text-sm">Nom</label>
        <input v-model="form.name" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">E-mail</label>
        <input v-model="form.email" type="email" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">Mot de passe</label>
        <input v-model="form.password" type="password" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">Rôle</label>
        <select v-model="form.role" class="w-full border rounded p-2">
          <option value="backoffice">Backoffice</option>
          <option value="technicien">Technicien</option>
        </select>
      </div>
      <button :disabled="submitting" @click="submit" class="px-4 py-2 bg-brand text-white rounded">Créer</button>
    </div>
  </div>
</template>

