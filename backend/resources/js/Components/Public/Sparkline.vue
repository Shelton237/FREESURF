<script setup>
const props = defineProps({ values: { type: Array, default: () => [] }, width: { type: Number, default: 160 }, height: { type: Number, default: 40 }, color: { type: String, default: '#dc2626' } })
const max = Math.max(1, ...props.values)
const points = props.values.map((v, i) => {
  const x = (i / Math.max(1, props.values.length - 1)) * props.width
  const y = props.height - (v / max) * props.height
  return `${x},${y}`
}).join(' ')
</script>

<template>
  <svg :width="width" :height="height">
    <polyline :points="points" :stroke="color" fill="none" stroke-width="2" />
  </svg>
</template>

