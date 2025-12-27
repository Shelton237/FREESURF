<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
const props = defineProps({ slides: { type: Array, default: () => [] }, interval: { type: Number, default: 4000 } })
const i = ref(0)
let t
const next = () => { i.value = (i.value + 1) % Math.max(1, props.slides.length) }
const prev = () => { i.value = (i.value - 1 + props.slides.length) % Math.max(1, props.slides.length) }
onMounted(() => { t = setInterval(next, props.interval) })
onBeforeUnmount(() => clearInterval(t))
</script>

<template>
  <div class="relative w-full overflow-hidden rounded border">
    <div class="aspect-[3/1] bg-gray-100 flex items-center justify-center">
      <div v-if="props.slides[i]" class="w-full h-full flex items-center justify-center">
        <img :src="props.slides[i].image" alt="" class="h-full object-contain" />
      </div>
    </div>
    <div class="absolute inset-x-0 bottom-0 bg-white/70 p-3">
      <div class="font-semibold">{{ props.slides[i]?.title }}</div>
      <div class="text-sm text-gray-700">{{ props.slides[i]?.text }}</div>
    </div>
    <button @click="prev" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1">‹</button>
    <button @click="next" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1">›</button>
  </div>
</template>

