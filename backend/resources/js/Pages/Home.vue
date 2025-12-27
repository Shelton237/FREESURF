<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue'
import StatCard from '../Components/Public/StatCard.vue'
import FeatureCard from '../Components/Public/FeatureCard.vue'
import Sparkline from '../Components/Public/Sparkline.vue'
import Carousel from '../Components/Public/Carousel.vue'
import TestimonialCard from '../Components/Public/TestimonialCard.vue'

const props = defineProps({ appName: String, canLogin: Boolean, canRegister: Boolean, stats: Object, series: Object, slides: Array, testimonials: Array, news: Array })
</script>

<template>
  <PublicLayout :app-name="props.appName">
    <!-- Hero -->
    <section class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-8 items-center p-6 md:p-12">
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
          CuWiP — la plateforme FAI/WISP de <span class="text-red-600">FREESURF</span>
        </h1>
        <p class="mt-4 text-gray-600">
          Gérez vos BTS, clients, éligibilités, installations et facturation mensuelle avec notifications WhatsApp/SMS.
        </p>
        <div class="mt-6 flex gap-3">
          <a href="/portal" class="px-5 py-3 bg-red-600 text-white rounded shadow">Portail client</a>
          <a href="/login" class="px-5 py-3 border rounded">Se connecter (Backoffice)</a>
        </div>
        <div class="mt-8 grid grid-cols-3 gap-3 max-w-md">
          <StatCard label="Clients" :value="props.stats?.clients ?? 0" />
          <StatCard label="BTS" :value="props.stats?.bts ?? 0" />
          <StatCard label="Demandes" :value="props.stats?.demandes ?? 0" />
        </div>
        <div class="mt-3 text-sm text-gray-600">
          <div class="flex items-center gap-2">
            <span>Clients (14j)</span>
            <Sparkline :values="props.series?.clients ?? []" />
          </div>
          <div class="flex items-center gap-2 mt-1">
            <span>Demandes (14j)</span>
            <Sparkline color="#111827" :values="props.series?.demandes ?? []" />
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <Carousel :slides="props.slides || []" />
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="bg-gray-50">
      <div class="max-w-7xl mx-auto p-6 md:p-12">
        <h2 class="text-2xl font-bold mb-4">Fonctionnalités clés</h2>
        <div class="grid md:grid-cols-3 gap-4">
          <FeatureCard title="Gestion BTS" text="Créez vos stations (ville, code, GPS, composants, photos) et suivez la couverture." />
          <FeatureCard title="Cycle client" text="Prospection → Éligibilité → Installation → Actif, avec historique et justificatifs." />
          <FeatureCard title="Facturation auto" text="Générez chaque mois les factures, exportez les PDFs, et marquez les paiements." />
          <FeatureCard title="Notifications" text="Prévenez vos clients via WhatsApp/SMS lors d’émissions et confirmations de paiement." />
          <FeatureCard title="Dashboard" text="Statistiques et filtres dynamiques: clients par BTS, impayés, exclusions, etc." />
          <FeatureCard title="PWA mobile" text="Application installable et utilisable hors-ligne pour les équipes terrain." />
        </div>
      </div>
    </section>

    <!-- Steps -->
    <section id="etapes" class="max-w-7xl mx-auto p-6 md:p-12">
      <h2 class="text-2xl font-bold mb-4">Comment ça marche ?</h2>
      <ol class="space-y-2 text-gray-700 list-decimal list-inside">
        <li>Portail client: soumission de la demande (abonnement/réabonnement).</li>
        <li>Backoffice: étude d’éligibilité (couverture par BTS ou relai).</li>
        <li>Installation: prise de rendez‑vous et marquage terminé.</li>
        <li>Facturation: génération mensuelle, exclusions, envoi de PDF.</li>
        <li>Paiement: validation et notifications au client.</li>
      </ol>
      <div class="mt-6">
        <a href="/portal" class="px-5 py-3 bg-red-600 text-white rounded">Démarrer une demande</a>
      </div>
    </section>

    <!-- Testimonials & News -->
    <section class="bg-gray-50">
      <div class="max-w-7xl mx-auto p-6 md:p-12 grid md:grid-cols-2 gap-6">
        <div>
          <h2 class="text-2xl font-bold mb-4">Témoignages</h2>
          <div class="grid gap-3">
            <TestimonialCard v-for="(t, idx) in (props.testimonials || [])" :key="idx" v-bind="t" />
          </div>
        </div>
        <div>
          <h2 class="text-2xl font-bold mb-4">Actualités</h2>
          <div class="grid gap-3">
            <div v-for="(n, idx) in (props.news || [])" :key="idx" class="p-5 rounded border bg-white">
              <div class="text-sm text-gray-500">{{ n.date }}</div>
              <div class="font-semibold">{{ n.title }}</div>
              <div class="text-sm text-gray-700">{{ n.text }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </PublicLayout>
  
</template>

