<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const form = ref({ type: 'abonnement', nom: '', telephone: '', email_facturation: '', adresse: '', lat: '', lng: '', commentaire: '' })
const submitting = ref(false)
const submit = () => {
  submitting.value = true
  router.post('/portal/demandes', form.value, { onFinish: () => (submitting.value = false) })
}
</script>

<template>
  <div class="min-h-screen p-6">
    <h1 class="text-2xl font-semibold">Nouvelle demande</h1>
    <div class="grid gap-3 mt-4 max-w-xl">
      <div>
        <label class="block text-sm">Type</label>
        <select v-model="form.type" class="w-full border rounded p-2">
          <option value="abonnement">Abonnement</option>
          <option value="reabonnement">Réabonnement</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Nom</label>
        <input v-model="form.nom" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">Téléphone</label>
        <input v-model="form.telephone" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">E-mail de facturation (entreprise)</label>
        <input v-model="form.email_facturation" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">Adresse</label>
        <input v-model="form.adresse" class="w-full border rounded p-2" />
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm">Latitude</label>
          <input v-model="form.lat" class="w-full border rounded p-2" />
        </div>
        <div>
          <label class="block text-sm">Longitude</label>
          <input v-model="form.lng" class="w-full border rounded p-2" />
        </div>
      </div>
      <div>
        <label class="block text-sm">Commentaire</label>
        <textarea v-model="form.commentaire" class="w-full border rounded p-2"></textarea>
      </div>
      <button :disabled="submitting" @click="submit" class="px-4 py-2 bg-red-600 text-white rounded">Envoyer</button>
    </div>
  </div>
</template>

