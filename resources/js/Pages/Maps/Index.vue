<script setup>
import { ref, onMounted } from 'vue';
import 'maplibre-gl/dist/maplibre-gl.css';
import maplibregl from 'maplibre-gl';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const mapContainer = ref(null);
const map = ref(null);
const styles = ref([
  { name: 'Default', url: 'https://demotiles.maplibre.org/style.json' },
  { name: 'Streets', url: 'mapbox://styles/mapbox/streets-v11' },
  { name: 'Satellite', url: 'mapbox://styles/mapbox/satellite-v9' },
]);
const selectedStyle = ref(styles.value[0].url);

onMounted(() => {
  map.value = new maplibregl.Map({
    container: mapContainer.value,
    style: selectedStyle.value,
    center: [6.617, 51.6581],
    zoom: 5,
  });

  map.value.addControl(new maplibregl.NavigationControl(), 'top-right');

  new maplibregl.Marker()
    .setLngLat([6.617, 51.6581])
    .setPopup(
      new maplibregl.Popup().setHTML(
        '<h3>Marker 1</h3><p>Description for Marker 1</p>'
      )
    )
    .addTo(map.value);

  new maplibregl.Marker()
    .setLngLat([7.617, 50.6581])
    .setPopup(
      new maplibregl.Popup().setHTML(
        '<h3>Marker 2</h3><p>Description for Marker 2</p>'
      )
    )
    .addTo(map.value);
});

const changeStyle = (styleUrl) => {
  map.value.setStyle(styleUrl);
};
</script>

<template>
  <Head title="Maps" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Map Example
      </h1>
    </template>
    <main class="mx-4">
      <div class="mb-4">
        <label
          for="mapStyle"
          class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >Select Map Style:</label
        >
        <select
          id="mapStyle"
          v-model="selectedStyle"
          @change="changeStyle(selectedStyle)"
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        >
          <option v-for="style in styles" :key="style.url" :value="style.url">
            {{ style.name }}
          </option>
        </select>
      </div>
      <div ref="mapContainer" class="map-container"></div>
    </main>
  </AppLayout>
</template>

<style scoped>
.map-container {
  width: 100%;
  height: 500px;
}
</style>
