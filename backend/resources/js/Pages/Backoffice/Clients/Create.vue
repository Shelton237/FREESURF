<script setup>
import BackofficeLayout from '@/Layouts/BackofficeLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
defineOptions({ layout: BackofficeLayout })
defineProps({ bts: Array, partners: Array })

const form = ref({ nom: '', telephone: '', type: 'domicile', email_facturation: '', bts_id: '', partner_id: '', lat: '', lng: '' })
const submitting = ref(false)
const submit = () => {
  submitting.value = true
  router.post('/backoffice/clients', form.value, { onFinish: () => (submitting.value = false) })
}
</script>

<template>
  <div class=Ã¢â‚¬â€œp-6 max-w-2xlÃ¢â‚¬â€œ>
    <h1 class=Ã¢â‚¬â€œtext-2xl font-semibold mb-4Ã¢â‚¬â€œ>Nouveau client</h1>
    <div class=Ã¢â‚¬â€œgrid gap-3Ã¢â‚¬â€œ>
      <div>
        <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>Nom</label>
        <input v-model=Ã¢â‚¬â€œform.nomÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ />
      </div>
      <div>
        <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>TÃƒÆ’Ã‚Â©lÃƒÆ’Ã‚Â©phone</label>
        <input v-model=Ã¢â‚¬â€œform.telephoneÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ />
      </div>
      <div>
        <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>Type</label>
        <select v-model=Ã¢â‚¬â€œform.typeÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ>
          <option value=Ã¢â‚¬â€œdomicileÃ¢â‚¬â€œ>Domicile</option>
          <option value=Ã¢â‚¬â€œentrepriseÃ¢â‚¬â€œ>Entreprise</option>
        </select>
      </div>
      <div v-if=Ã¢â‚¬â€œform.type === 'entreprise'Ã¢â‚¬â€œ>
        <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>EÃ¢â‚¬â€˜mail de facturation</label>
        <input v-model=Ã¢â‚¬â€œform.email_facturationÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ />
      </div>
      <div class=Ã¢â‚¬â€œgrid grid-cols-2 gap-3Ã¢â‚¬â€œ>
        <div>
          <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>BTS</label>
          <select v-model=Ã¢â‚¬â€œform.bts_idÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ>
            <option value=Ã¢â‚¬â€œÃ¢â‚¬â€œ>ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Å“</option>
            <option v-for=Ã¢â‚¬â€œb in btsÃ¢â‚¬â€œ :key=Ã¢â‚¬â€œb.idÃ¢â‚¬â€œ :value=Ã¢â‚¬â€œb.idÃ¢â‚¬â€œ>{{ b.code }} ({{ b.ville }})</option>
          </select>
        </div>
        <div>
          <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>Partenaire</label>
          <select v-model=Ã¢â‚¬â€œform.partner_idÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ>
            <option value=Ã¢â‚¬â€œÃ¢â‚¬â€œ>ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Å“</option>
            <option v-for=Ã¢â‚¬â€œp in partnersÃ¢â‚¬â€œ :key=Ã¢â‚¬â€œp.idÃ¢â‚¬â€œ :value=Ã¢â‚¬â€œp.idÃ¢â‚¬â€œ>{{ p.nom }}</option>
          </select>
        </div>
      </div>
      <div class=Ã¢â‚¬â€œgrid grid-cols-2 gap-3Ã¢â‚¬â€œ>
        <div>
          <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>Latitude</label>
          <input v-model=Ã¢â‚¬â€œform.latÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ />
        </div>
        <div>
          <label class=Ã¢â‚¬â€œblock text-smÃ¢â‚¬â€œ>Longitude</label>
          <input v-model=Ã¢â‚¬â€œform.lngÃ¢â‚¬â€œ class=Ã¢â‚¬â€œw-full border rounded p-2Ã¢â‚¬â€œ />
        </div>
      </div>
      <button :disabled=Ã¢â‚¬â€œsubmittingÃ¢â‚¬â€œ @click=Ã¢â‚¬â€œsubmitÃ¢â‚¬â€œ class=Ã¢â‚¬â€œpx-4 py-2 bg-brand text-white roundedÃ¢â‚¬â€œ>Enregistrer</button>
    </div>
  </div>
</template>



