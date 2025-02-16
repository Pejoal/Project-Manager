<script setup>
import { ref, onMounted } from 'vue';
import 'maplibre-gl/dist/maplibre-gl.css';
import maplibregl from 'maplibre-gl';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const mapContainer = ref(null);

onMounted(() => {
  const map = new maplibregl.Map({
    container: mapContainer.value,
    style: 'https://demotiles.maplibre.org/style.json', // Free MapLibre style
    center: [6.617, 51.6581],
    zoom: 5,
  });

  new maplibregl.Marker().setLngLat([6.617, 51.6581]).addTo(map);
});
</script>

<template>
  <Head title="Maps" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Map Example
      </h1>
    </template>
    <div ref="mapContainer" class="map-container"></div>
  </AppLayout>
</template>

<style scoped>
.map-container {
  width: 100%;
  height: 500px;
}
</style>
