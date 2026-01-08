<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  demande: Object,
  timeline: Array,
  canCancel: Boolean,
})

const cancelForm = useForm({ motif: '' })

const submitCancel = () => {
  cancelForm.post(`/portal/compte/demandes/${props.demande.id}/cancel`)
}
</script>

<template>
  <div class="bg-gray-50 min-h-[70vh] px-4 py-10">
    <div class="max-w-4xl mx-auto space-y-6">
      <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <p class="text-sm text-gray-500 uppercase">Demande #{{ demande.id }}</p>
          <h1 class="text-2xl font-bold text-gray-900">{{ demande.type }}</h1>
          <p class="text-sm text-gray-500">{{ demande.adresse || 'Adresse non renseignée' }}</p>
        </div>
        <a href="/portal/compte" class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">Retour</a>
      </header>

      <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">Suivi</h2>
        <ol class="space-y-4">
          <li
            v-for="step in timeline"
            :key="step.key"
            class="flex gap-4 items-start"
          >
            <span
              class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold"
              :class="demande.statut === step.key || timeline.findIndex(x => x.key === demande.statut) > timeline.findIndex(x => x.key === step.key)
                ? 'bg-rose-600 text-white'
                : 'bg-gray-200 text-gray-500'"
            >
              {{ timeline.findIndex(x => x.key === step.key) + 1 }}
            </span>
            <div>
              <p class="font-semibold">{{ step.label }}</p>
              <p class="text-sm text-gray-500">{{ step.description }}</p>
            </div>
          </li>
        </ol>
        <div v-if="demande.motif_annulation" class="p-3 rounded-lg bg-red-50 text-red-700 text-sm">
          Demande annulée : {{ demande.motif_annulation }}
        </div>
      </section>

      <section v-if="canCancel && !demande.motif_annulation" class="bg-white rounded-2xl shadow-sm p-6 space-y-3">
        <h2 class="text-lg font-semibold text-gray-900">Annuler la demande</h2>
        <p class="text-sm text-gray-500">Expliquez-nous la raison pour laquelle vous souhaitez annuler.</p>
        <textarea
          v-model="cancelForm.motif"
          class="w-full border rounded-lg p-3 h-28"
          placeholder="Motif d'annulation"
        />
        <button
          class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 disabled:bg-red-300"
          type="button"
          :disabled="cancelForm.processing"
          @click="submitCancel"
        >
          {{ cancelForm.processing ? 'Envoi...' : 'Confirmer l’annulation' }}
        </button>
      </section>
    </div>
  </div>
</template>
