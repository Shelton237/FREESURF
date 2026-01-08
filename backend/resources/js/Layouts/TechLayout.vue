<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const url = computed(() => page.url)

const navLinks = [
  { href: '/tech', label: 'Mes interventions', match: '/tech' },
  { href: '/tech/sav', label: 'Tickets SAV', match: '/tech/sav' },
  { href: '/tech/work-orders/create', label: 'Nouvelle etude', match: '/tech/work-orders/create' },
]

const isActive = (match) => url.value.startsWith(match)
</script>

<template>
  <div class="min-h-screen bg-slate-100 flex flex-col">
    <header class="bg-white border-b shadow-sm">
      <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <img src="/logo.png" alt="FREESURF" class="h-10 w-10 rounded-lg border" />
          <div>
            <p class="text-xs uppercase tracking-widest text-gray-500">Espace technicien</p>
            <p class="text-lg font-semibold text-gray-900">CuWiP Field</p>
          </div>
        </div>
        <div class="flex flex-wrap items-center gap-4">
          <nav class="flex items-center gap-2 text-sm font-medium">
            <Link
              v-for="link in navLinks"
              :key="link.href"
              :href="link.href"
              class="px-3 py-2 rounded-lg"
              :class="isActive(link.match) ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100'"
            >
              {{ link.label }}
            </Link>
          </nav>
          <div class="text-sm text-gray-600">
            <p class="font-semibold text-gray-900">{{ page.props.auth?.user?.name }}</p>
            <p class="text-xs uppercase">Technicien</p>
          </div>
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="text-xs text-red-600 border border-red-200 px-3 py-2 rounded-lg hover:bg-red-50"
          >
            Déconnexion
          </Link>
        </div>
      </div>
    </header>

    <main class="flex-1">
      <slot />
    </main>

    <footer class="bg-white border-t text-xs text-gray-500">
      <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <span>© {{ new Date().getFullYear() }} FREESURF - Terrain</span>
        <span>Support: +237 670 00 00 00</span>
      </div>
    </footer>
  </div>
</template>
