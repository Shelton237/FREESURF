<script setup>
defineProps({ name: String, role: String, text: String })
</script>

<template>
  <div class="p-5 rounded border bg-white">
    <div class="text-sm text-gray-700">“{{ text }}”</div>
    <div class="mt-3 text-xs text-gray-500">— {{ name }}, {{ role }}</div>
  </div>
</template>

