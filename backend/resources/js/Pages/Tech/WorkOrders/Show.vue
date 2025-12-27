<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
const props = defineProps({ workOrder: Object })
const startForm = ref({ lat: '', lng: '' })
const completeForm = ref({ date: new Date().toISOString().slice(0,10), commentaire: '' })
const start = () => router.post(`/tech/work-orders/${props.workOrder.id}/start`, startForm.value)
const complete = () => router.post(`/tech/work-orders/${props.workOrder.id}/complete`, completeForm.value)
</script>

<template>
  <div class="p-6 max-w-3xl">
    <a href="/tech" class="text-sm text-blue-600">← Retour</a>
    <h1 class="text-2xl font-semibold mt-2">Intervention #{{ workOrder.id }} — {{ workOrder.type.toUpperCase() }}</h1>
    <p class="text-gray-600">Client: {{ workOrder.client?.nom ?? '-' }} — Statut: <b>{{ workOrder.status }}</b></p>

    <div class="grid md:grid-cols-2 gap-6 mt-4">
      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Démarrer</h2>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-sm">Lat</label><input v-model="startForm.lat" class="w-full border rounded p-2" /></div>
          <div><label class="block text-sm">Lng</label><input v-model="startForm.lng" class="w-full border rounded p-2" /></div>
        </div>
        <button class="mt-3 px-3 py-2 bg-gray-800 text-white rounded" @click="start">Démarrer</button>
      </div>
      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Terminer</h2>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-sm">Date</label><input type="date" v-model="completeForm.date" class="w-full border rounded p-2" /></div>
          <div><label class="block text-sm">Commentaire</label><input v-model="completeForm.commentaire" class="w-full border rounded p-2" /></div>
        </div>
        <button class="mt-3 px-3 py-2 bg-red-600 text-white rounded" @click="complete">Marquer terminé</button>
      </div>
    </div>
  </div>
</template>

