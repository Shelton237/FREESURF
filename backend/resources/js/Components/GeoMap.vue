<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import L from 'leaflet'
import '../../css/leaflet.css'
import markerIcon2x from '../../images/leaflet/marker-icon-2x.png'
import markerIcon from '../../images/leaflet/marker-icon.png'
import markerShadow from '../../images/leaflet/marker-shadow.png'

const props = defineProps({
  center: {
    type: Object,
    default: () => ({ lat: 0, lng: 0 }),
  },
  zoom: {
    type: Number,
    default: 12,
  },
  bounds: {
    type: Object,
    default: null,
  },
  bts: {
    type: Array,
    default: () => [],
  },
  clients: {
    type: Array,
    default: () => [],
  },
  height: {
    type: String,
    default: '20rem',
  },
  fullscreen: {
    type: Boolean,
    default: false,
  },
})

L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
})

const mapEl = ref(null)
let mapInstance
let btsLayer
let clientsLayer

const applyBounds = () => {
  if (!mapInstance || !props.bounds?.southWest || !props.bounds?.northEast) {
    return
  }
  const bounds = L.latLngBounds(props.bounds.southWest, props.bounds.northEast)
  mapInstance.fitBounds(bounds, { padding: [32, 32], maxZoom: 13 })
}

const refreshLayers = () => {
  if (!mapInstance || !btsLayer || !clientsLayer) {
    return
  }

  btsLayer.clearLayers()
  clientsLayer.clearLayers()

  const bounds = []

  props.bts.forEach((bts) => {
    if (Number.isFinite(bts.lat) && Number.isFinite(bts.lng)) {
      const marker = L.marker([bts.lat, bts.lng])
      const popup = [
        `<strong>${bts.code}</strong>`,
        bts.ville ?? '',
        `${bts.clients ?? 0} clients actifs`,
      ]
      if (bts.url) {
        popup.push(
          `<a href="${bts.url}" target="_blank" rel="noopener" class="text-brand underline">Voir la fiche BTS</a>`,
        )
      }
      marker.bindPopup(popup.filter(Boolean).join('<br>'))
      btsLayer.addLayer(marker)
      bounds.push([bts.lat, bts.lng])
    }
  })

  props.clients.forEach((client) => {
    if (Number.isFinite(client.lat) && Number.isFinite(client.lng)) {
      const marker = L.circleMarker([client.lat, client.lng], {
        radius: 5,
        color: '#0ea5e9',
        fillColor: '#38bdf8',
        fillOpacity: 0.9,
        weight: 1,
      })
      const popupLines = [
        `<strong>${client.nom}</strong>`,
        client.code ?? '',
        client.statut ? `Statut: ${client.statut}` : '',
        client.bts ? `BTS: ${client.bts}` : '',
      ].filter(Boolean)
      if (client.url) {
        popupLines.push(
          `<a href="${client.url}" target="_blank" rel="noopener" class="text-brand underline">Ouvrir la fiche client</a>`,
        )
      }
      marker.bindPopup(popupLines.join('<br>'))
      clientsLayer.addLayer(marker)
      bounds.push([client.lat, client.lng])
    }
  })

  if (bounds.length > 1) {
    mapInstance.fitBounds(bounds, { padding: [24, 24], maxZoom: 14 })
  } else if (bounds.length === 1) {
    mapInstance.setView(bounds[0], 13)
  } else if (Number.isFinite(props.center?.lat) && Number.isFinite(props.center?.lng)) {
    mapInstance.setView([props.center.lat, props.center.lng], props.zoom)
    applyBounds()
  } else {
    applyBounds()
  }
}

const initMap = () => {
  if (!mapEl.value || mapInstance) {
    return
  }

  mapInstance = L.map(mapEl.value, {
    attributionControl: true,
  })

  const fallbackCenter = Number.isFinite(props.center?.lat) && Number.isFinite(props.center?.lng)
    ? [props.center.lat, props.center.lng]
    : [0, 0]

  mapInstance.setView(fallbackCenter, props.zoom)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(mapInstance)

  btsLayer = L.layerGroup().addTo(mapInstance)
  clientsLayer = L.layerGroup().addTo(mapInstance)

  if (props.bounds?.southWest && props.bounds?.northEast) {
    applyBounds()
  }
  refreshLayers()
}

watch(
  () => [props.bts, props.clients],
  () => refreshLayers(),
  { deep: true },
)

watch(
  () => props.center,
  (center) => {
    if (!mapInstance || !Number.isFinite(center?.lat) || !Number.isFinite(center?.lng)) {
      return
    }
    mapInstance.panTo([center.lat, center.lng], { animate: true })
  },
  { deep: true },
)

onMounted(() => initMap())
onBeforeUnmount(() => {
  if (mapInstance) {
    mapInstance.remove()
  }
})

watch(
  () => props.bounds,
  () => applyBounds(),
  { deep: true },
)

watch(
  () => props.fullscreen,
  () => {
    if (mapInstance) {
      setTimeout(() => mapInstance.invalidateSize(), 200)
    }
  },
)
</script>

<template>
  <div ref="mapEl" class="w-full rounded border border-gray-200 overflow-hidden" :style="{ height }"></div>
</template>
