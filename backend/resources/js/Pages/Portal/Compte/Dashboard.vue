<script setup>
import { watch } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { router, useForm } from '@inertiajs/vue3'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  compte: Object,
  demandes: Array,
  clients: Array,
  facturesRecentes: Array,
  tickets: Array,
})

const statusLabels = {
  soumise: 'Soumise',
  en_etude: 'En étude',
  planification: 'Planification',
  validee: 'Validée',
  installee: 'Installée',
  active: 'Active',
  annulee: 'Annulée',
  close: 'Clôturée',
}

const logout = () => {
  router.post('/portal/compte/logout')
}

const linkForm = useForm({
  code: '',
  telephone: '',
})

const savForm = useForm({
  client_id: props.clients?.[0]?.id || '',
  type: 'incident',
  priority: 'normal',
  subject: '',
  description: '',
})

watch(
  () => props.clients,
  (clients) => {
    if (!savForm.client_id && clients.length) {
      savForm.client_id = clients[0].id
    }
  },
  { immediate: true },
)

const submitLink = () => {
  linkForm.post('/portal/compte/clients/link', {
    onSuccess: () => linkForm.reset(),
  })
}

const submitSav = () => {
  savForm.post('/portal/compte/sav', {
    onSuccess: () => savForm.reset('subject', 'description'),
  })
}
</script>

<template>
  <div class="bg-gray-50 min-h-[70vh] px-4 py-10">
    <div class="max-w-6xl mx-auto space-y-8">
      <header
        class="bg-white rounded-2xl shadow-sm p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4"
      >
        <div>
          <p class="text-sm text-gray-500">Bienvenue</p>
          <h1 class="text-2xl font-bold text-gray-900">{{ compte.nom }}</h1>
          <p class="text-sm text-gray-600">
            Téléphone: {{ compte.telephone }}
            <span v-if="compte.email">• {{ compte.email }}</span>
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <a href="/portal/compte/factures" class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
            Mes factures
          </a>
          <a href="/portal/compte/profil" class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">Mon profil</a>
          <button class="px-4 py-2 rounded-lg border text-sm text-gray-700 hover:bg-gray-100" @click="logout">
            Déconnexion
          </button>
        </div>
      </header>

      <div class="grid gap-6 lg:grid-cols-2">
        <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">Clients liés</h2>
              <p class="text-sm text-gray-500">Chaque client relié donne accès à ses factures et tickets.</p>
            </div>
            <span class="text-sm text-gray-500">{{ clients.length }} compte(s)</span>
          </div>
          <div v-if="clients.length" class="space-y-3">
            <article
              v-for="client in clients"
              :key="client.id"
              class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
            >
              <div>
                <p class="text-xs uppercase text-gray-500">#{{ client.code }}</p>
                <p class="text-base font-semibold text-gray-900">{{ client.nom }}</p>
                <p class="text-xs text-gray-500">Statut : {{ client.statut }}</p>
              </div>
              <div class="text-sm text-right">
                <p class="text-gray-500">Factures impayées</p>
                <p class="text-lg font-bold" :class="client.factures_impayees ? 'text-rose-600' : 'text-emerald-600'">
                  {{ client.factures_impayees }}
                </p>
              </div>
            </article>
          </div>
          <p v-else class="text-sm text-gray-500">Aucun client lié pour le moment.</p>
        </section>

        <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Lier un client</h2>
            <p class="text-sm text-gray-500">Renseignez le code client et le téléphone enregistré auprès de nos équipes.</p>
          </div>
          <div class="space-y-3">
            <label class="block text-sm text-gray-600">
              Code client *
              <input v-model="linkForm.code" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: CLI-000123" />
            </label>
            <label class="block text-sm text-gray-600">
              Téléphone référencé *
              <input v-model="linkForm.telephone" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: 699 00 00 00" />
            </label>
          </div>
          <button
            class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 disabled:bg-rose-300"
            type="button"
            :disabled="linkForm.processing"
            @click="submitLink"
          >
            {{ linkForm.processing ? 'Liaison...' : 'Lier le client' }}
          </button>
        </section>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">Factures récentes</h2>
              <p class="text-sm text-gray-500">Dernières factures liées à vos comptes.</p>
            </div>
            <a href="/portal/compte/factures" class="text-sm text-rose-600 hover:underline">Tout voir</a>
          </div>
          <div class="space-y-3">
            <article
              v-for="facture in facturesRecentes"
              :key="facture.id"
              class="border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
            >
              <div>
                <p class="text-xs uppercase text-gray-500">{{ facture.client.code }} • {{ facture.client.nom }}</p>
                <p class="text-base font-semibold text-gray-900">{{ facture.numero }}</p>
                <p class="text-xs text-gray-500">Échéance {{ facture.due_date || 'NC' }}</p>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-gray-900">{{ Number(facture.montant).toLocaleString('fr-FR') }} XAF</p>
                <p class="text-xs uppercase" :class="facture.statut === 'payee' ? 'text-emerald-600' : 'text-rose-600'">
                  {{ facture.statut }}
                </p>
                <a
                  class="text-xs text-rose-600 hover:underline"
                  :href="`/portal/compte/factures/${facture.id}`"
                >
                  Détails
                </a>
              </div>
            </article>
            <p v-if="!facturesRecentes.length" class="text-sm text-gray-500">Aucune facture disponible.</p>
          </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Support & SAV</h2>
            <p class="text-sm text-gray-500">Déclarez un incident ou suivez les tickets ouverts.</p>
          </div>
          <div class="space-y-3">
            <article
              v-for="ticket in tickets"
              :key="ticket.id"
              class="border rounded-xl p-3 text-sm text-gray-700 flex items-center justify-between"
            >
              <div>
                <p class="font-semibold">{{ ticket.subject }}</p>
                <p class="text-xs text-gray-500">Client #{{ ticket.client_id }} • {{ ticket.created_at }}</p>
              </div>
              <span class="text-xs uppercase px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                {{ ticket.status }}
              </span>
            </article>
            <p v-if="!tickets.length" class="text-sm text-gray-500">Aucun ticket en cours.</p>
          </div>
          <div class="border-t pt-4 space-y-3">
            <label class="block text-sm text-gray-600">
              Client lié *
              <select v-model="savForm.client_id" class="w-full border rounded-lg p-3 mt-1">
                <option value="">Sélectionner</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.nom }} ({{ client.code }})
                </option>
              </select>
            </label>
            <div class="grid sm:grid-cols-2 gap-3">
              <label class="block text-sm text-gray-600">
                Type *
                <select v-model="savForm.type" class="w-full border rounded-lg p-3 mt-1">
                  <option value="incident">Incident</option>
                  <option value="assistance">Assistance</option>
                  <option value="reclamation">Réclamation</option>
                </select>
              </label>
              <label class="block text-sm text-gray-600">
                Priorité *
                <select v-model="savForm.priority" class="w-full border rounded-lg p-3 mt-1">
                  <option value="low">Basse</option>
                  <option value="normal">Normale</option>
                  <option value="high">Haute</option>
                </select>
              </label>
            </div>
            <label class="block text-sm text-gray-600">
              Sujet *
              <input v-model="savForm.subject" class="w-full border rounded-lg p-3 mt-1" placeholder="Ex: Coupure service" />
            </label>
            <label class="block text-sm text-gray-600">
              Description *
              <textarea
                v-model="savForm.description"
                class="w-full border rounded-lg p-3 mt-1 h-24"
                placeholder="Expliquez votre demande"
              />
            </label>
            <button
              class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 disabled:bg-rose-300"
              type="button"
              :disabled="savForm.processing || !clients.length"
              @click="submitSav"
            >
              {{ savForm.processing ? 'Envoi...' : 'Créer un ticket' }}
            </button>
          </div>
        </section>
      </div>

      <section class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Mes demandes</h2>
            <p class="text-sm text-gray-500">Suivez l’avancement de vos demandes d’abonnement.</p>
          </div>
          <a href="/portal/demandes/nouvelle" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm">Nouvelle demande</a>
        </div>
        <div class="space-y-3">
          <article
            v-for="demande in demandes"
            :key="demande.id"
            class="border rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
          >
            <div>
              <p class="text-xs uppercase text-gray-500">#{{ demande.id }} • {{ demande.type }}</p>
              <p class="text-base font-semibold text-gray-900">{{ demande.adresse || 'Adresse non renseignée' }}</p>
              <p class="text-xs text-gray-500">Créée le {{ demande.created_at }}</p>
            </div>
            <div class="flex items-center gap-4">
              <div class="text-sm">
                <p class="text-gray-500">Statut</p>
                <p class="text-lg font-bold text-rose-600">
                  {{ statusLabels[demande.statut] || demande.statut }}
                </p>
              </div>
              <a class="text-sm text-rose-600 hover:underline" :href="`/portal/compte/demandes/${demande.id}`">Détail</a>
            </div>
          </article>
          <p v-if="!demandes.length" class="text-sm text-gray-500">Aucune demande pour le moment.</p>
        </div>
      </section>
    </div>
  </div>
</template>
