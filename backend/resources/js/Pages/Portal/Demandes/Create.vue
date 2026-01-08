<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineOptions({ layout: PublicLayout })

const steps = ['Informations', 'Localisation', 'Résumé']
const typeOptions = [
  { value: 'abonnement', label: 'Abonnement', description: 'Nouvelle installation ou premier raccordement.' },
  { value: 'reabonnement', label: 'Réabonnement', description: 'Redémarrer un service existant.' },
]

const currentStep = ref(0)
const showValidation = ref(false)
const locating = ref(false)
const locationMessage = ref('')

const form = ref({
  type: 'abonnement',
  nom: '',
  telephone: '',
  client_code: '',
  email_facturation: '',
  adresse: '',
  lat: '',
  lng: '',
  commentaire: '',
  password: '',
  password_confirmation: '',
})

const isReabonnement = computed(() => form.value.type === 'reabonnement')

const submitting = ref(false)

const requiredForStep = (step) => {
  if (step === 0) {
    const baseOk =
      form.value.nom.trim() &&
      form.value.telephone.trim() &&
      form.value.password.length >= 6 &&
      form.value.password === form.value.password_confirmation
    if (isReabonnement.value && !form.value.client_code.trim()) {
      return false
    }
    return baseOk
  }
  if (step === 1) {
    return form.value.adresse.trim() && form.value.lat && form.value.lng
  }
  return true
}

const canProceed = computed(() => requiredForStep(currentStep.value))

const nextStep = () => {
  showValidation.value = true
  if (!canProceed.value) return
  showValidation.value = false
  if (currentStep.value < steps.length - 1) currentStep.value += 1
}

const prevStep = () => {
  showValidation.value = false
  if (currentStep.value > 0) currentStep.value -= 1
}

const useMyLocation = () => {
  if (!navigator.geolocation) {
    locationMessage.value = 'La géolocalisation n’est pas disponible sur cet appareil.'
    return
  }
  locating.value = true
  locationMessage.value = 'Recherche de votre position...'
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      locating.value = false
      form.value.lat = pos.coords.latitude.toFixed(6)
      form.value.lng = pos.coords.longitude.toFixed(6)
      locationMessage.value = 'Position détectée. Vérifiez qu’elle correspond à votre adresse.'
    },
    () => {
      locating.value = false
      locationMessage.value = 'Impossible d’obtenir votre position. Merci de renseigner vos coordonnées manuellement.'
    },
    { enableHighAccuracy: true, timeout: 10000 },
  )
}

const summary = computed(() => {
  const base = [
    { label: 'Type', value: typeOptions.find((o) => o.value === form.value.type)?.label || form.value.type },
    { label: 'Nom', value: form.value.nom || '-' },
    { label: 'Téléphone', value: form.value.telephone || '-' },
  ]

  if (isReabonnement.value) {
    base.push({ label: 'Code client', value: form.value.client_code || '-' })
  }

  return [
    ...base,
    { label: 'E-mail facturation', value: form.value.email_facturation || '-' },
    { label: 'Adresse', value: form.value.adresse || '-' },
    { label: 'Latitude', value: form.value.lat || '-' },
    { label: 'Longitude', value: form.value.lng || '-' },
    { label: 'Commentaire', value: form.value.commentaire || '-' },
  ]
})

const submit = () => {
  showValidation.value = true
  if (!requiredForStep(2)) return
  submitting.value = true
  router.post('/portal/demandes', form.value, {
    onFinish: () => {
      submitting.value = false
      showValidation.value = false
    },
  })
}
</script>

<template>
  <div class="bg-gradient-to-b from-rose-50 via-white to-white">
    <section class="max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
      <header class="space-y-3 text-center lg:text-left">
        <p class="text-sm uppercase tracking-wide text-rose-500 font-semibold">Demande d’abonnement</p>
        <h1 class="text-4xl font-bold text-gray-900">Lancez votre dossier en trois étapes guidées</h1>
        <p class="text-gray-600 max-w-3xl mx-auto lg:mx-0">
          Nous planifions un technicien dès que nous disposons de vos coordonnées et de la localisation précise du site.
          Temps estimé : 2 minutes.
        </p>
      </header>

      <div class="flex flex-col gap-4 lg:gap-3 lg:flex-row lg:items-center">
        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
          <div
            class="h-2 bg-rose-500 rounded-full transition-all"
            :style="{ width: ((currentStep + 1) / steps.length) * 100 + '%' }"
          />
        </div>
        <div class="flex flex-wrap justify-between gap-3 text-sm font-medium text-gray-600">
          <div v-for="(step, index) in steps" :key="step" class="flex items-center gap-2">
            <span
              class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
              :class="index <= currentStep ? 'bg-rose-500 text-white' : 'bg-gray-200 text-gray-500'"
            >
              {{ index + 1 }}
            </span>
            <span :class="index === currentStep ? 'text-gray-900' : ''">{{ step }}</span>
          </div>
        </div>
      </div>

      <div class="grid lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-2 space-y-6">
          <section v-if="currentStep === 0" class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
            <div>
              <h2 class="text-xl font-semibold">Vos informations</h2>
              <p class="text-sm text-gray-500 mt-1">Nous vous contactons uniquement pour confirmer l’éligibilité.</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
              <button
                v-for="option in typeOptions"
                :key="option.value"
                type="button"
                class="border rounded-xl p-4 text-left transition"
                :class="form.type === option.value ? 'border-rose-500 bg-rose-50' : 'border-gray-200'"
                @click="form.type = option.value"
              >
                <div class="text-sm uppercase tracking-wide text-gray-500">{{ option.label }}</div>
                <p class="mt-2 text-gray-700 text-sm leading-relaxed">{{ option.description }}</p>
              </button>
            </div>
            <div class="grid gap-4">
              <label class="block">
                <span class="text-sm text-gray-600">Nom complet *</span>
                <input v-model="form.nom" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: Denise Mballa" />
              </label>
              <label class="block">
                <span class="text-sm text-gray-600">Téléphone *</span>
                <input v-model="form.telephone" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: 699 00 00 00" />
              </label>
              <div v-if="isReabonnement" class="p-3 rounded-xl bg-rose-50 text-sm text-rose-800">
                Indiquez le code client reçu lors de l’installation initiale pour accélérer la réactivation.
              </div>
              <label v-if="isReabonnement" class="block">
                <span class="text-sm text-gray-600">Code client *</span>
                <input v-model="form.client_code" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: CLI-000123" />
              </label>
              <label class="block">
                <span class="text-sm text-gray-600">E-mail de facturation (entreprise)</span>
                <input v-model="form.email_facturation" class="w-full border rounded-lg p-3 mt-1" placeholder="optionnel" />
              </label>
              <label class="block">
                <span class="text-sm text-gray-600">Créer un mot de passe *</span>
                <input v-model="form.password" type="password" class="w-full border rounded-lg p-3 mt-1" placeholder="Au moins 6 caractères" />
              </label>
              <label class="block">
                <span class="text-sm text-gray-600">Confirmer le mot de passe *</span>
                <input v-model="form.password_confirmation" type="password" class="w-full border rounded-lg p-3 mt-1" placeholder="Retapez le mot de passe" />
              </label>
            </div>
          </section>

          <section v-if="currentStep === 1" class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
            <div>
              <h2 class="text-xl font-semibold">Adresse et localisation</h2>
              <p class="text-sm text-gray-500 mt-1">Positionnez l’endroit exact où l’équipement sera installé.</p>
            </div>
            <div class="grid gap-4">
              <label class="block">
                <span class="text-sm text-gray-600">Adresse / point de repère *</span>
                <input
                  v-model="form.adresse"
                  class="w-full border rounded-lg p-3 mt-1"
                  placeholder="Ex: Rue 123, quartier Bonapriso"
                />
              </label>
              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="text-sm text-gray-600">Latitude *</span>
                  <input v-model="form.lat" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: 4.051245" />
                </label>
                <label class="block">
                  <span class="text-sm text-gray-600">Longitude *</span>
                  <input v-model="form.lng" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: 9.705612" />
                </label>
              </div>
              <div class="flex flex-wrap items-center gap-3">
                <button
                  type="button"
                  class="px-4 py-2 rounded-lg border"
                  :class="locating ? 'border-gray-300 text-gray-400' : 'border-gray-600 text-gray-600'"
                  :disabled="locating"
                  @click="useMyLocation"
                >
                  {{ locating ? 'Détection en cours...' : 'Utiliser ma position' }}
                </button>
                <p class="text-sm text-gray-500">{{ locationMessage }}</p>
              </div>
              <label class="block">
                <span class="text-sm text-gray-600">Commentaire (optionnel)</span>
                <textarea
                  v-model="form.commentaire"
                  class="w-full border rounded-lg p-3 mt-1 h-28"
                  placeholder="Décrivez le site, les contraintes, les disponibilités…"
                />
              </label>
            </div>
          </section>

          <section v-if="currentStep === 2" class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
            <div>
              <h2 class="text-xl font-semibold">Résumé</h2>
              <p class="text-sm text-gray-500 mt-1">Un conseiller vous contactera sur la base de ces informations.</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
              <article v-for="item in summary" :key="item.label" class="border rounded-xl p-3">
                <div class="text-xs uppercase text-gray-500">{{ item.label }}</div>
                <div class="mt-1 text-gray-900">{{ item.value || '—' }}</div>
              </article>
            </div>
          </section>

          <div class="flex flex-wrap items-center gap-3">
            <button
              type="button"
              class="px-4 py-2 text-sm rounded-lg border"
              :class="currentStep === 0 ? 'border-gray-200 text-gray-400' : 'border-gray-600 text-gray-700'"
              :disabled="currentStep === 0"
              @click="prevStep"
            >
              Retour
            </button>
            <button
              v-if="currentStep < steps.length - 1"
              type="button"
              class="px-5 py-2 text-sm rounded-lg text-white"
              :class="canProceed ? 'bg-rose-600 hover:bg-rose-700' : 'bg-rose-300 cursor-not-allowed'"
              @click="nextStep"
            >
              Continuer
            </button>
            <button
              v-else
              type="button"
              class="px-6 py-3 text-sm rounded-lg text-white bg-rose-600 hover:bg-rose-700 disabled:bg-rose-300"
              :disabled="submitting"
              @click="submit"
            >
              {{ submitting ? 'Envoi en cours...' : 'Envoyer ma demande' }}
            </button>
            <p v-if="showValidation && !canProceed" class="text-sm text-red-600">
              Merci de compléter les champs obligatoires avant de continuer.
            </p>
          </div>
        </div>

        <aside class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
          <div>
            <p class="text-sm uppercase text-gray-500">Besoin d’aide</p>
            <h3 class="text-xl font-semibold mt-1">Nos conseillers sont disponibles</h3>
          </div>
          <ol class="space-y-4 text-sm text-gray-600">
            <li class="flex gap-3">
              <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-semibold">1</span>
              Renseignez vos coordonnées pour que nous puissions vous rappeler.
            </li>
            <li class="flex gap-3">
              <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-semibold">2</span>
              Positionnez votre site pour planifier un technicien sans aller-retour.
            </li>
            <li class="flex gap-3">
              <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-semibold">3</span>
              Recevez un numéro de dossier et suivez l’avancement dans le portail.
            </li>
          </ol>
          <div class="border-t pt-4 text-sm text-gray-600">
            <p>Support client</p>
            <p class="font-semibold text-gray-900">support@freesurf.com · WhatsApp 670 00 00 00</p>
          </div>
        </aside>
      </div>
    </section>
  </div>
</template>
