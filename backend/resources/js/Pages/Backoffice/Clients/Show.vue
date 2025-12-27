<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ client: Object })

const eligForm = ref({ resultat: 'eligible', commentaire: '' })
const instForm = ref({ date: new Date().toISOString().slice(0,10), commentaire: '' })

const submitElig = () => {
  router.post(`/backoffice/clients/${props.client.id}/eligibilites`, eligForm.value)
}
const completeInst = () => {
  router.post(`/backoffice/clients/${props.client.id}/installation/complete`, instForm.value)
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div>
      <a href="/backoffice/clients" class="text-sm text-blue-600">← Retour</a>
      <h1 class="text-2xl font-semibold mt-2">Client {{ client.code }}</h1>
      <p class="text-gray-600">{{ client.nom }} — {{ client.telephone }} — Statut: <b>{{ client.statut }}</b></p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Éligibilité</h2>
        <div class="flex gap-3 items-center mb-3">
          <label class="flex items-center gap-2"><input type="radio" value="eligible" v-model="eligForm.resultat"/> Éligible</label>
          <label class="flex items-center gap-2"><input type="radio" value="non_eligible" v-model="eligForm.resultat"/> Non éligible</label>
        </div>
        <textarea v-model="eligForm.commentaire" class="w-full border rounded p-2" placeholder="Commentaire"></textarea>
        <button class="mt-3 px-3 py-2 bg-red-600 text-white rounded" @click="submitElig">Enregistrer</button>
        <div class="mt-3 text-sm text-gray-600">
          <div v-for="e in client.eligibilites" :key="e.id">{{ e.created_at }} — {{ e.resultat }} — {{ e.commentaire }}</div>
        </div>
      </div>

      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Installation</h2>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm">Date</label>
            <input v-model="instForm.date" type="date" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm">Commentaire</label>
            <input v-model="instForm.commentaire" class="w-full border rounded p-2" />
          </div>
        </div>
        <button class="mt-3 px-3 py-2 bg-red-600 text-white rounded" @click="completeInst">Marquer terminée</button>
        <div v-if="client.installation" class="mt-3 text-sm text-gray-600">
          <div>Terminé: {{ client.installation.terminee ? 'oui' : 'non' }} — {{ client.installation.date ?? '-' }}</div>
          <div>Commentaire: {{ client.installation.commentaire ?? '-' }}</div>
        </div>
      </div>
    </div>

    <div class="border rounded p-4">
      <h2 class="font-semibold mb-2">Créer une intervention</h2>
      <a :href="`/backoffice/work-orders/create?client=${client.id}`" class="px-3 py-2 bg-gray-100 rounded border inline-block">Nouvelle intervention (assigner un technicien)</a>
    </div>
  </div>
</template>
