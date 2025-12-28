<script setup>
defineProps({ appName: { type: String, default: 'FREESURF' } })
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <header class="border-b sticky top-0 bg-white/90 backdrop-blur z-10">
      <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
        <a href="/" class="flex items-center gap-2">
          <img src="/logo.png" alt="logo" class="h-8 w-8" />
          <span class="font-bold">{{ appName }}</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="#features" class="hover:text-brand">Fonctionnalités</a>
          <a href="#etapes" class="hover:text-brand">Étapes</a>
          <a href="#contact" class="hover:text-brand">Contact</a>
        </nav>
        <div class="flex items-center gap-2">
          <a href="/portal" class="px-3 py-2 text-sm border rounded hover:bg-gray-50">Portail</a>
          <a href="/login" class="px-3 py-2 text-sm bg-brand text-white rounded">Backoffice</a>
        </div>
      </div>
    </header>

    <main class="flex-1">
      <div v-if="$page.props.flash?.success" class="bg-green-50 text-green-700 text-sm px-4 py-2 text-center">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash?.error" class="bg-red-50 text-red-600 text-sm px-4 py-2 text-center">
        {{ $page.props.flash.error }}
      </div>
      <slot />
    </main>

    <footer id="contact" class="border-t bg-gray-50">
      <div class="max-w-7xl mx-auto p-6 text-sm text-gray-600 flex items-center justify-between">
        <span>© {{ new Date().getFullYear() }} {{ appName }}. Tous droits réservés.</span>
        <a class="hover:text-brand" href="/portal">Accéder au portail</a>
      </div>
    </footer>
  </div>
</template>
