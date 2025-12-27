<script setup>
defineProps({ label: String, value: [String, Number] })
</script>

<template>
  <div class="p-4 rounded border text-center">
    <div class="text-3xl font-bold text-red-600">{{ value }}</div>
    <div class="text-xs uppercase tracking-wide text-gray-600 mt-1">{{ label }}</div>
  </div>
</template>

