<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  compte: Object,
})

const form = useForm({
  nom: props.compte?.nom || '',
  email: props.compte?.email || '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/portal/compte/profil')
}
</script>

<template>
  <div class="bg-gray-50 min-h-[70vh] px-4 py-10">
    <div class="max-w-3xl mx-auto space-y-6 bg-white rounded-2xl shadow-sm p-6">
      <div>
        <p class="text-sm uppercase text-rose-500 font-semibold">Profil</p>
        <h1 class="text-2xl font-bold text-gray-900">Informations du compte</h1>
        <p class="text-sm text-gray-500">Modifiez vos coordonnées et, si nécessaire, votre mot de passe.</p>
      </div>
      <div class="grid gap-4">
        <label class="block text-sm text-gray-600">
          Nom complet
          <input v-model="form.nom" class="w-full border rounded-lg p-3 mt-1" />
        </label>
        <label class="block text-sm text-gray-600">
          E-mail
          <input v-model="form.email" class="w-full border rounded-lg p-3 mt-1" placeholder="optionnel" />
        </label>
        <label class="block text-sm text-gray-600">
          Nouveau mot de passe
          <input v-model="form.password" type="password" class="w-full border rounded-lg p-3 mt-1" placeholder="laisser vide pour conserver l’actuel" />
        </label>
        <label class="block text-sm text-gray-600">
          Confirmation du mot de passe
          <input v-model="form.password_confirmation" type="password" class="w-full border rounded-lg p-3 mt-1" />
        </label>
      </div>
      <div class="flex items-center gap-3">
        <a href="/portal/compte" class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">Retour</a>
        <button
          class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 disabled:bg-rose-300"
          type="button"
          :disabled="form.processing"
          @click="submit"
        >
          {{ form.processing ? 'Enregistrement...' : 'Mettre à jour' }}
        </button>
      </div>
    </div>
  </div>
</template>
