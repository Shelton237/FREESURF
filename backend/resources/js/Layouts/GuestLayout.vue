<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900 flex items-center justify-center p-6">
      <div class="w-full max-w-md">
        <div class="flex items-center justify-center mb-6">
          <Link href="/" class="flex items-center gap-2">
            <img src="/logo.png" alt="FREESURF" class="h-10" />
            <span class="font-bold">FREESURF</span>
          </Link>
        </div>
        <div class="bg-white rounded-lg shadow border border-gray-100 p-6">
          <slot />
        </div>
      </div>
    </div>
</template>
