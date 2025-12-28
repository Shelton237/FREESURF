<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ workOrder: Object })

const startForm = ref({ lat: '', lng: '' })
const isSurvey = computed(() => props.workOrder.type === 'survey')
const completeForm = ref({
  date: new Date().toISOString().slice(0, 10),
  commentaire: '',
  survey_result: 'available',
  survey_reason: '',
  survey_follow_up: false,
})

const start = () => router.post(`/tech/work-orders/${props.workOrder.id}/start`, startForm.value)
const complete = () => router.post(`/tech/work-orders/${props.workOrder.id}/complete`, completeForm.value)

const uploading = ref(false)
async function compressImage(file, maxW = 1600, quality = 0.8) {
  return new Promise((resolve) => {
    const img = new Image()
    img.onload = () => {
      const scale = Math.min(1, maxW / img.width)
      const w = Math.round(img.width * scale)
      const h = Math.round(img.height * scale)
      const canvas = document.createElement('canvas')
      canvas.width = w; canvas.height = h
      const ctx = canvas.getContext('2d')
      ctx.drawImage(img, 0, 0, w, h)
      canvas.toBlob((blob) => {
        const out = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' })
        resolve(out)
      }, 'image/jpeg', quality)
    }
    img.src = URL.createObjectURL(file)
  })
}
async function onFiles(ev) {
  const files = Array.from(ev.target.files || [])
  for (const f of files) {
    uploading.value = true
    const compressed = await compressImage(f).catch(() => f)
    const fd = new FormData()
    fd.append('file', compressed)
    await router.post(`/tech/work-orders/${props.workOrder.id}/attachments`, fd, {
      forceFormData: true,
      onFinish: () => (uploading.value = false),
      preserveScroll: true,
    })
  }
  ev.target.value = ''
}

if (props.workOrder.survey_result) {
  completeForm.value.survey_result = props.workOrder.survey_result
  completeForm.value.survey_reason = props.workOrder.survey_reason || ''
  completeForm.value.survey_follow_up = Boolean(props.workOrder.survey_follow_up)
}
</script>

<template>
  <div class="p-6 max-w-3xl">
    <a href="/tech" class="text-sm text-blue-600">← Retour</a>
    <h1 class="text-2xl font-semibold mt-2">Intervention #{{ workOrder.id }} – {{ workOrder.type.toUpperCase() }}</h1>
    <p class="text-gray-600">
      Client: {{ workOrder.client?.nom ?? '-' }} – Statut: <b>{{ workOrder.status }}</b>
    </p>

    <div class="grid md:grid-cols-2 gap-6 mt-4">
      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Démarrer</h2>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm">Lat</label>
            <input v-model="startForm.lat" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm">Lng</label>
            <input v-model="startForm.lng" class="w-full border rounded p-2" />
          </div>
        </div>
        <button class="mt-3 px-3 py-2 bg-brand text-white rounded" @click="start">Démarrer</button>
      </div>

      <div class="border rounded p-4">
        <h2 class="font-semibold mb-2">Terminer</h2>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm">Date</label>
            <input type="date" v-model="completeForm.date" class="w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm">Commentaire</label>
            <input v-model="completeForm.commentaire" class="w-full border rounded p-2" />
          </div>
        </div>

        <div v-if="isSurvey" class="mt-4 space-y-3 text-sm">
          <div>
            <label class="block text-xs uppercase text-gray-500">Résultat du survey</label>
            <select v-model="completeForm.survey_result" class="w-full border rounded p-2 mt-1">
              <option value="available">Signal disponible</option>
              <option value="not_available">Indisponible</option>
            </select>
          </div>
          <div>
            <label class="block text-xs uppercase text-gray-500">Motif / Notes</label>
            <textarea
              v-model="completeForm.survey_reason"
              class="w-full border rounded p-2 mt-1"
              placeholder="Ex: obstacles, puissance insuffisante, ligne de vue"
            ></textarea>
          </div>
          <label class="flex items-center gap-2 text-xs uppercase text-gray-600">
            <input type="checkbox" v-model="completeForm.survey_follow_up" />
            Demander un service de suivi
          </label>
        </div>

        <button class="mt-3 px-3 py-2 bg-brand text-white rounded" @click="complete">Marquer terminé</button>
      </div>
    </div>

    <div class="mt-6 border rounded p-4">
      <h2 class="font-semibold mb-2">Photos</h2>
      <input type="file" multiple accept="image/*" capture="environment" @change="onFiles" />
      <div v-if="uploading" class="text-sm text-gray-500 mt-1">Envoi en cours...</div>
      <div class="grid grid-cols-3 gap-2 mt-3">
        <img v-for="a in (workOrder.attachments || [])" :key="a.id" :src="`/storage/${a.path}`" class="w-full h-28 object-cover rounded border" />
      </div>
    </div>
  </div>
</template>
