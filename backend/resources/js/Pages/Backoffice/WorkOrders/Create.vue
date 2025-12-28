<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
const props = defineProps({ client: Object, bts: Array, technicians: Array })
defineOptions({ layout: BackofficeLayout })
const form = ref({ type: 'install', client_id: props.client?.id || '', bts_id: '', assigned_to: '', scheduled_at: '', notes: '' })
const submit = () => router.post('/backoffice/work-orders', form.value)
</script>

<template>
  <div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-semibold mb-4">CrÃƒÂ©er une intervention</h1>
    <div class="grid gap-3">
      <div>
        <label class="block text-sm">Type</label>
        <select v-model="form.type" class="w-full border rounded p-2">
          <option value="install">Installation</option>
          <option value="survey">Survey</option>
          <option value="maintenance">Maintenance</option>
        </select>
      </div>
      <div v-if="!form.client_id">
        <label class="block text-sm">Client</label>
        <input v-model="form.client_id" class="w-full border rounded p-2" placeholder="ID client" />
      </div>
      <div>
        <label class="block text-sm">BTS (optionnel)</label>
        <select v-model="form.bts_id" class="w-full border rounded p-2">
          <option value="">Ã¢â‚¬â€œ</option>
          <option v-for="b in bts" :key="b.id" :value="b.id">{{ b.code }} ({{ b.ville }})</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Technicien</label>
        <select v-model="form.assigned_to" class="w-full border rounded p-2">
          <option value="">Ã¢â‚¬â€œ</option>
          <option v-for="t in technicians" :key="t.id" :value="t.id">{{ t.name }} ({{ t.email }})</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">PlanifiÃƒÂ©e le</label>
        <input v-model="form.scheduled_at" type="datetime-local" class="w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm">Notes</label>
        <textarea v-model="form.notes" class="w-full border rounded p-2"></textarea>
      </div>
      <button class="px-4 py-2 bg-red-600 text-white rounded" @click="submit">CrÃƒÂ©er</button>
    </div>
  </div>
</template>


