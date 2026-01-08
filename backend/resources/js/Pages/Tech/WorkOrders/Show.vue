<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import TechLayout from '@/Layouts/TechLayout.vue'

defineOptions({ layout: TechLayout })

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

const locating = ref(false)
const locationMessage = ref('')

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
      canvas.width = w
      canvas.height = h
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

const detectStartLocation = () => {
  if (!navigator.geolocation) {
    locationMessage.value = "Géolocalisation indisponible sur cet appareil."
    return
  }
  locating.value = true
  locationMessage.value = 'Recherche de votre position...'
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      locating.value = false
      startForm.value.lat = pos.coords.latitude.toFixed(6)
      startForm.value.lng = pos.coords.longitude.toFixed(6)
      locationMessage.value = 'Coordonnées renseignées automatiquement.'
    },
    () => {
      locating.value = false
      locationMessage.value = 'Impossible de détecter votre position.'
    },
    { enableHighAccuracy: true, timeout: 10000 },
  )
}

if (props.workOrder.survey_result) {
  completeForm.value.survey_result = props.workOrder.survey_result
  completeForm.value.survey_reason = props.workOrder.survey_reason || ''
  completeForm.value.survey_follow_up = Boolean(props.workOrder.survey_follow_up)
}
</script>

<template>
  <div class="px-4 py-8">
    <div class="max-w-5xl mx-auto space-y-6">
      <div class="flex items-center justify-between text-sm">
        <a href="/tech" class="text-rose-600 hover:underline">← Retour</a>
        <span class="text-gray-500">Statut actuel : <b class="text-gray-900">{{ workOrder.status }}</b></span>
      </div>

      <header class="bg-white rounded-2xl shadow-sm border p-6 space-y-2">
        <p class="text-xs uppercase text-gray-500">Intervention #{{ workOrder.id }}</p>
        <h1 class="text-3xl font-semibold text-gray-900">{{ workOrder.type.toUpperCase() }}</h1>
        <p class="text-sm text-gray-600">
          Client : {{ workOrder.client?.nom ?? '—' }} · BTS : {{ workOrder.bts?.code ?? '—' }}
        </p>
        <p class="text-xs text-gray-500">Planifiée le {{ workOrder.scheduled_at ?? 'non defini' }}</p>
      </header>

      <section class="bg-white border rounded-2xl p-5 grid gap-4 sm:grid-cols-2">
        <div>
          <p class="text-xs uppercase text-gray-500">Coordonnees client</p>
          <p class="font-semibold text-gray-900">{{ workOrder.client?.nom ?? '—' }}</p>
          <p class="text-sm text-gray-600">Tel : {{ workOrder.client?.telephone ?? '—' }}</p>
          <p class="text-sm text-gray-600">Code : {{ workOrder.client?.code ?? '—' }}</p>
          <p class="text-sm text-gray-600">Type : {{ workOrder.client?.type ?? 'Non renseigne' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase text-gray-500">Coordonnees GPS</p>
          <p class="text-sm text-gray-600">Lat : {{ workOrder.lat ?? '—' }}</p>
          <p class="text-sm text-gray-600">Lng : {{ workOrder.lng ?? '—' }}</p>
        </div>
      </section>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border rounded-2xl p-5 space-y-3">
          <h2 class="font-semibold text-gray-900">Demarrer la visite</h2>
          <p class="text-sm text-gray-500">Renseignez vos coordonnees GPS en arrivant sur site.</p>
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
          <div class="flex flex-wrap items-center gap-3">
            <button
              class="px-3 py-2 border rounded text-sm"
              :class="locating ? 'text-gray-400 border-gray-200' : 'text-gray-700 border-gray-400'"
              type="button"
              :disabled="locating"
              @click="detectStartLocation"
            >
              {{ locating ? 'Détection...' : 'Utiliser ma position' }}
            </button>
            <p class="text-xs text-gray-500">{{ locationMessage }}</p>
          </div>
          <button class="mt-3 px-3 py-2 bg-brand text-white rounded" @click="start">Demarrer</button>
        </div>

        <div class="bg-white border rounded-2xl p-5 space-y-3">
          <h2 class="font-semibold text-gray-900">Cloturer l’intervention</h2>
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
              <label class="block text-xs uppercase text-gray-500">Resultat du survey</label>
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

          <button class="mt-3 px-3 py-2 bg-brand text-white rounded" @click="complete">Marquer termine</button>
        </div>
      </div>

      <section class="bg-white border rounded-2xl p-5 space-y-3">
        <h2 class="font-semibold text-gray-900">Timeline intervention</h2>
        <ol class="space-y-3">
          <li v-for="event in (workOrder.events || [])" :key="event.id" class="flex gap-3 items-start text-sm">
            <span class="w-2 h-2 rounded-full bg-brand mt-2"></span>
            <div>
              <p class="font-semibold text-gray-900 uppercase text-xs">{{ event.type }}</p>
              <p class="text-gray-600">
                {{ event.payload?.message || JSON.stringify(event.payload) }}
              </p>
              <p class="text-xs text-gray-400">{{ event.created_at }}</p>
            </div>
          </li>
          <li v-if="!(workOrder.events || []).length" class="text-sm text-gray-500">Aucun evenement encore.</li>
        </ol>
      </section>

      <section class="bg-white border rounded-2xl p-5 space-y-3">
        <h2 class="font-semibold text-gray-900">Photos terrain</h2>
        <p class="text-sm text-gray-500">Ajoutez vos prises de vue terrain.</p>
        <input type="file" multiple accept="image/*" capture="environment" @change="onFiles" />
        <div v-if="uploading" class="text-sm text-gray-500 mt-1">Envoi en cours...</div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-3">
          <img
            v-for="a in (workOrder.attachments || [])"
            :key="a.id"
            :src="`/storage/${a.path}`"
            class="w-full h-32 object-cover rounded border"
          />
        </div>
      </section>
    </div>
  </div>
</template>
