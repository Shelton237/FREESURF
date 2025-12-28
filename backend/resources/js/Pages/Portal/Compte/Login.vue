<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineOptions({ layout: PublicLayout })

const form = ref({ telephone: '', password: '' })
const submitting = ref(false)

const submit = () => {
  submitting.value = true
  router.post('/portal/compte/login', form.value, {
    onFinish: () => (submitting.value = false),
  })
}
</script>

<template>
  <div class="min-h-[70vh] flex items-center justify-center bg-gradient-to-b from-rose-50 via-white to-white px-4">
    <div class="bg-white rounded-2xl shadow-sm p-8 w-full max-w-lg space-y-6">
      <div class="text-center space-y-2">
        <p class="text-sm uppercase text-rose-500 font-semibold">Suivi de demande</p>
        <h1 class="text-2xl font-bold text-gray-900">Connectez-vous à votre espace client</h1>
        <p class="text-sm text-gray-600">Entrez le numéro de téléphone utilisé lors de la demande. L’adresse e-mail est optionnelle.</p>
      </div>

      <div class="space-y-4">
        <label class="block">
          <span class="text-sm text-gray-600">Téléphone *</span>
          <input v-model="form.telephone" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: 699 00 00 00" />
        </label>
        <label class="block">
          <span class="text-sm text-gray-600">Mot de passe *</span>
          <input v-model="form.password" type="password" class="w-full border rounded-lg p-3 mt-1" placeholder="••••••••" />
        </label>
      </div>

      <button
        type="button"
        class="w-full px-4 py-3 rounded-lg text-white text-sm font-semibold bg-rose-600 hover:bg-rose-700 disabled:bg-rose-300"
        :disabled="submitting"
        @click="submit"
      >
        {{ submitting ? 'Connexion…' : 'Accéder à mon espace' }}
      </button>
      <p class="text-xs text-gray-500 text-center">Besoin d’aide ? support@freesurf.com · WhatsApp 670 00 00 00</p>
    </div>
  </div>
</template>
