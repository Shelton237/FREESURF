<script setup>
import TechLayout from '@/Layouts/TechLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: TechLayout })

const props = defineProps({
  ticket: Object,
})

const form = useForm({
  status: props.ticket.status || 'open',
  resolution_notes: props.ticket.resolution_notes || '',
  follow_up_at: props.ticket.follow_up_at ? props.ticket.follow_up_at.slice(0, 16) : '',
})

const submit = () => {
  form.patch(`/tech/sav/${props.ticket.id}`)
}
</script>

<template>
  <div class="px-4 py-8">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between text-sm">
        <a href="/tech/sav" class="text-rose-600 hover:underline">← Retour</a>
        <span class="text-gray-500">Ticket #{{ ticket.id }}</span>
      </div>

      <section class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
        <div>
          <p class="text-xs uppercase text-gray-500">Sujet</p>
          <h1 class="text-2xl font-semibold text-gray-900">{{ ticket.subject }}</h1>
          <p class="text-sm text-gray-500 mt-1">{{ ticket.description }}</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <p class="text-xs uppercase text-gray-500">Client</p>
            <p class="font-semibold text-gray-900">{{ ticket.client?.nom ?? '—' }}</p>
            <p class="text-sm text-gray-500">{{ ticket.client?.telephone ?? '—' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase text-gray-500">Canal</p>
            <p class="font-semibold text-gray-900">{{ ticket.channel }}</p>
            <p class="text-sm text-gray-500">Type : {{ ticket.type }}</p>
          </div>
        </div>
      </section>

      <section class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">Mise à jour</h2>
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="text-sm text-gray-600">
            Statut *
            <select v-model="form.status" class="mt-1 w-full border rounded-lg px-3 py-2">
              <option value="open">open</option>
              <option value="in_progress">in_progress</option>
              <option value="pending_client">pending_client</option>
              <option value="resolved">resolved</option>
              <option value="closed">closed</option>
            </select>
          </label>
          <label class="text-sm text-gray-600">
            Relance / suivi (date heure)
            <input v-model="form.follow_up_at" type="datetime-local" class="mt-1 w-full border rounded-lg px-3 py-2" />
          </label>
        </div>
        <label class="text-sm text-gray-600 w-full">
          Notes / Résolution
          <textarea
            v-model="form.resolution_notes"
            class="mt-1 w-full border rounded-lg px-3 py-2 h-32"
            placeholder="Résultat de l'intervention, matériel remplacé..."
          ></textarea>
        </label>
        <div class="flex items-center gap-3">
          <button
            class="px-5 py-2 rounded-lg text-white bg-brand hover:bg-rose-700 disabled:bg-rose-300"
            type="button"
            :disabled="form.processing"
            @click="submit"
          >
            {{ form.processing ? 'Enregistrement...' : 'Mettre à jour' }}
          </button>
          <span v-if="form.hasErrors" class="text-sm text-red-600">Vérifiez les champs.</span>
        </div>
      </section>
    </div>
  </div>
</template>

