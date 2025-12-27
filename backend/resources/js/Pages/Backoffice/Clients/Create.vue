<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
defineProps({ bts: Array, partners: Array })

const form = ref({ nom: '', telephone: '', type: 'domicile', email_facturation: '', bts_id: '', partner_id: '', lat: '', lng: '' })
const submitting = ref(false)
const submit = () => {
  submitting.value = true
  router.post('/backoffice/clients', form.value, { onFinish: () => (submitting.value = false) })
}
</script>

<template>
  <div class=–p-6 max-w-2xl–>
    <h1 class=–text-2xl font-semibold mb-4–>Nouveau client</h1>
    <div class=–grid gap-3–>
      <div>
        <label class=–block text-sm–>Nom</label>
        <input v-model=–form.nom– class=–w-full border rounded p-2– />
      </div>
      <div>
        <label class=–block text-sm–>TÃ©lÃ©phone</label>
        <input v-model=–form.telephone– class=–w-full border rounded p-2– />
      </div>
      <div>
        <label class=–block text-sm–>Type</label>
        <select v-model=–form.type– class=–w-full border rounded p-2–>
          <option value=–domicile–>Domicile</option>
          <option value=–entreprise–>Entreprise</option>
        </select>
      </div>
      <div v-if=–form.type === 'entreprise'–>
        <label class=–block text-sm–>E‑mail de facturation</label>
        <input v-model=–form.email_facturation– class=–w-full border rounded p-2– />
      </div>
      <div class=–grid grid-cols-2 gap-3–>
        <div>
          <label class=–block text-sm–>BTS</label>
          <select v-model=–form.bts_id– class=–w-full border rounded p-2–>
            <option value=––>â€“</option>
            <option v-for=–b in bts– :key=–b.id– :value=–b.id–>{{ b.code }} ({{ b.ville }})</option>
          </select>
        </div>
        <div>
          <label class=–block text-sm–>Partenaire</label>
          <select v-model=–form.partner_id– class=–w-full border rounded p-2–>
            <option value=––>â€“</option>
            <option v-for=–p in partners– :key=–p.id– :value=–p.id–>{{ p.nom }}</option>
          </select>
        </div>
      </div>
      <div class=–grid grid-cols-2 gap-3–>
        <div>
          <label class=–block text-sm–>Latitude</label>
          <input v-model=–form.lat– class=–w-full border rounded p-2– />
        </div>
        <div>
          <label class=–block text-sm–>Longitude</label>
          <input v-model=–form.lng– class=–w-full border rounded p-2– />
        </div>
      </div>
      <button :disabled=–submitting– @click=–submit– class=–px-4 py-2 bg-brand text-white rounded–>Enregistrer</button>
    </div>
  </div>
</template>


