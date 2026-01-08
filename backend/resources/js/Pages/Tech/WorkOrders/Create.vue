<script setup>
import TechLayout from '@/Layouts/TechLayout.vue'
import { router, useForm } from '@inertiajs/vue3'

defineOptions({ layout: TechLayout })

const props = defineProps({
  clients: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  types: { type: Array, default: () => ['survey'] },
})

const defaultType = props.types?.[0] || 'survey'

const form = useForm({
  client_id: '',
  type: defaultType,
  scheduled_at: '',
  notes: '',
})

const searchForm = useForm({
  search: props.filters?.search || '',
})

const submit = () => {
  form.post('/tech/work-orders', {
    preserveScroll: true,
  })
}

const applySearch = () => {
  router.get('/tech/work-orders/create', searchForm.data(), {
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <div class="px-4 py-8">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between text-sm">
        <a href="/tech" class="text-rose-600 hover:underline">← Retour</a>
        <span class="text-gray-500">Initiez une etude pour l'un de vos clients.</span>
      </div>

      <header class="bg-white rounded-2xl shadow-sm border p-6 space-y-2">
        <p class="text-xs uppercase text-gray-500">Terrain</p>
        <h1 class="text-3xl font-semibold text-gray-900">Nouvelle etude</h1>
        <p class="text-sm text-gray-600">
          Recherchez un client, puis planifiez votre passage pour lancer l'etude d'eligibilite.
        </p>
      </header>

      <section class="bg-white rounded-2xl border shadow-sm p-6 space-y-4">
        <form class="space-y-5" @submit.prevent="submit">
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Rechercher un client</label>
            <div class="flex flex-col gap-3 sm:flex-row">
              <input
                v-model="searchForm.search"
                type="search"
                placeholder="Code client, nom ou numero"
                class="flex-1 border rounded-lg px-3 py-2"
              />
              <button
                type="button"
                class="px-4 py-2 rounded-lg border text-sm font-semibold text-gray-700 hover:bg-gray-50"
                @click="applySearch"
              >
                Rechercher
              </button>
            </div>
            <p class="text-xs text-gray-500">Limite a 25 resultats. Contactez le manager si le client n'apparait pas.</p>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Client cible</label>
            <select v-model="form.client_id" required class="w-full border rounded-lg px-3 py-2">
              <option value="">Selectionnez un client</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.code }} · {{ client.nom }} · {{ client.telephone }}
              </option>
            </select>
          </div>

          <div v-if="props.types.length > 1" class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Type d'intervention</label>
            <select v-model="form.type" class="w-full border rounded-lg px-3 py-2">
              <option v-for="t in props.types" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <label class="space-y-1 text-sm text-gray-700">
              Date planifiee (optionnel)
              <input type="date" v-model="form.scheduled_at" class="w-full border rounded-lg px-3 py-2" />
            </label>
            <label class="space-y-1 text-sm text-gray-700">
              Notes
              <input
                type="text"
                v-model="form.notes"
                placeholder="Ex: Installer samedi apres-midi"
                class="w-full border rounded-lg px-3 py-2"
              />
            </label>
          </div>

          <button type="submit" class="px-4 py-2 bg-brand text-white rounded-lg font-semibold">
            Lancer l'etude
          </button>
        </form>
      </section>
    </div>
  </div>
</template>
