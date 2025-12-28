<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: BackofficeLayout })
const props = defineProps({
  client: Object,
  savTickets: Array,
  savOptions: Object,
})

const eligForm = ref({ resultat: 'eligible', commentaire: '' })
const instForm = ref({ date: new Date().toISOString().slice(0, 10), commentaire: '' })
const savForm = ref({
  client_id: props.client.id,
  type: 'incident',
  channel: 'phone',
  priority: 'normal',
  subject: '',
  description: '',
  assigned_to: '',
})

const submitElig = () => {
  router.post(`/backoffice/clients/${props.client.id}/eligibilites`, eligForm.value)
}
const completeInst = () => {
  router.post(`/backoffice/clients/${props.client.id}/installation/complete`, instForm.value)
}
const submitSav = () => {
  router.post('/backoffice/sav', savForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      savForm.value.subject = ''
      savForm.value.description = ''
    },
  })
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div>
      <a href="/backoffice/clients" class="text-sm text-blue-600">← Retour</a>
      <h1 class="text-2xl font-semibold mt-2">Client {{ client.code }}</h1>
      <p class="text-gray-600">
        {{ client.nom }} – {{ client.telephone }} – Statut:
        <b>{{ client.statut }}</b>
      </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Éligibilités</h2>
        <div class="flex gap-3 items-center mb-3">
          <label class="flex items-center gap-2">
            <input type="radio" value="eligible" v-model="eligForm.resultat" /> Eligible
          </label>
          <label class="flex items-center gap-2">
            <input type="radio" value="non_eligible" v-model="eligForm.resultat" /> Non éligible
          </label>
        </div>
        <textarea v-model="eligForm.commentaire" class="w-full border rounded p-2" placeholder="Commentaire"></textarea>
        <button class="mt-3 px-3 py-2 bg-red-600 text-white rounded" @click="submitElig">Enregistrer</button>
        <div class="mt-3 text-sm text-gray-600 space-y-1">
          <div v-for="e in client.eligibilites" :key="e.id" class="border-b pb-1 last:border-0">
            <span class="font-semibold">{{ e.created_at }}</span> — {{ e.resultat }} ·
            <span>{{ e.commentaire || '—' }}</span>
          </div>
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
        <div v-if="client.installation" class="mt-3 text-sm text-gray-600 space-y-1">
          <div>Terminée: {{ client.installation.terminee ? 'oui' : 'non' }} — {{ client.installation.date ?? '-' }}</div>
          <div>Commentaire: {{ client.installation.commentaire ?? '-' }}</div>
        </div>
      </div>
    </div>

    <div class="border rounded p-4">
      <h2 class="font-semibold mb-2">Créer une intervention</h2>
      <a :href="`/backoffice/work-orders/create?client=${client.id}`" class="px-3 py-2 bg-gray-100 rounded border inline-block">
        Nouvelle intervention (assigner un technicien)
      </a>
    </div>

    <div class="border rounded p-4">
      <h2 class="font-semibold mb-2">Service après-vente</h2>
      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-3">
          <div>
            <label class="block text-sm text-gray-600">Sujet</label>
            <input v-model="savForm.subject" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm text-gray-600">Description</label>
            <textarea v-model="savForm.description" class="w-full border rounded p-2" rows="3"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3 text-sm">
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Type</span>
              <select v-model="savForm.type" class="border rounded px-2 py-1">
                <option v-for="type in savOptions?.types || []" :key="type" :value="type">{{ type }}</option>
              </select>
            </label>
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Canal</span>
              <select v-model="savForm.channel" class="border rounded px-2 py-1">
                <option v-for="channel in savOptions?.channels || []" :key="channel" :value="channel">{{ channel }}</option>
              </select>
            </label>
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Priorité</span>
              <select v-model="savForm.priority" class="border rounded px-2 py-1">
                <option v-for="priority in savOptions?.priorities || []" :key="priority" :value="priority">{{ priority }}</option>
              </select>
            </label>
            <label class="flex flex-col gap-1">
              <span class="text-xs uppercase text-gray-500">Assigné à</span>
              <select v-model="savForm.assigned_to" class="border rounded px-2 py-1">
                <option value="">—</option>
                <option v-for="user in savOptions?.assignees || []" :key="user.id" :value="user.id">{{ user.name }}</option>
              </select>
            </label>
          </div>
          <button class="px-4 py-2 bg-red-600 text-white rounded" @click="submitSav">Créer un ticket SAV</button>
        </div>
        <div class="space-y-3 text-sm">
          <div
            v-for="ticket in savTickets"
            :key="ticket.id"
            class="border rounded p-3"
          >
            <div class="flex items-center justify-between">
              <div>
                <div class="font-semibold">#{{ ticket.id }} · {{ ticket.subject }}</div>
                <div class="text-xs text-gray-500">{{ ticket.type }} · {{ ticket.channel }}</div>
              </div>
              <span class="text-xs px-2 py-1 rounded bg-gray-100">{{ ticket.status }}</span>
            </div>
            <div class="text-xs text-gray-500 mt-1">
              Priorité: {{ ticket.priority }} · Assigné à: {{ ticket.assigned_to || '—' }}
            </div>
            <div class="text-xs text-gray-500">
              Créé le {{ ticket.created_at || '-' }} · Résolu: {{ ticket.resolved_at || '—' }}
            </div>
          </div>
          <p v-if="!savTickets?.length" class="text-gray-500">Aucun ticket SAV pour ce client.</p>
        </div>
      </div>
    </div>
  </div>
</template>
