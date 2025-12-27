<script setup>
defineProps({ title: String, text: String })
</script>

<template>
  <div class="p-5 rounded border bg-white">
    <div class="text-base font-semibold">{{ title }}</div>
    <p class="text-sm text-gray-600 mt-1">{{ text }}</p>
  </div>
</template>

