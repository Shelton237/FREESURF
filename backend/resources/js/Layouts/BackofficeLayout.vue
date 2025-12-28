<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const url = computed(() => page.url)

const navLinks = [
  { href: '/backoffice', label: 'Dashboard', match: '/backoffice' },
  { href: '/backoffice/bts', label: 'BTS', match: '/backoffice/bts' },
  { href: '/backoffice/clients', label: 'Clients', match: '/backoffice/clients' },
  { href: '/backoffice/work-orders/create', label: 'Interventions', match: '/backoffice/work-orders' },
  { href: '/backoffice/admin/users', label: 'Utilisateurs', match: '/backoffice/admin/users' },
]

const isActive = (match) => url.value.startsWith(match)
</script>

<template>
  <div class="min-h-screen flex bg-gray-50">
    <aside class="w-64 bg-white border-r flex flex-col">
      <div class="p-4 border-b flex items-center gap-3">
        <img src="/logo.png" alt="FREESURF" class="h-10" />
        <div>
          <div class="font-bold">FREESURF</div>
          <div class="text-xs text-gray-500">Backoffice CuWiP</div>
        </div>
      </div>
      <nav class="flex-1 p-4 space-y-1 text-sm">
        <template v-for="link in navLinks" :key="link.href">
          <Link
            :href="link.href"
            class="block px-3 py-2 rounded"
            :class="isActive(link.match) ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100'"
          >
            {{ link.label }}
          </Link>
        </template>
      </nav>
      <div class="p-4 border-t text-sm text-gray-500">
        Connecté en tant que<br />
        <span class="font-semibold text-gray-800">{{ page.props.auth?.user?.name }}</span>
        <Link :href="route('logout')" method="post" as="button" class="mt-2 text-xs text-red-600 hover:underline">
          Se déconnecter
        </Link>
      </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>
