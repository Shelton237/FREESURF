<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const form = ref({ ville: '', lat: '', lng: '', composants: [] })
const submitting = ref(false)
const submit = () => {
  submitting.value = true
  router.post('/backoffice/bts', form.value, { onFinish: () => (submitting.value = false) })
}
</script>

<template>
  <div class="p-6 max-w-xl">
    <h1 class="text-2xl font-semibold mb-4">Nouvelle BTS</h1>
    <div class="space-y-3">
      <div>
        <label class="block text-sm">Ville</label>
        <input v-model="form.ville" class="w-full border rounded p-2" />
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
      <button :disabled="submitting" @click="submit" class="px-4 py-2 bg-red-600 text-white rounded">Enregistrer</button>
    </div>
  </div>
  
</template>

