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
  { name: 'Satellite', url: 'https://api.maptiler.com/maps/hybrid/style.json?key=BvYa8EIWKdXmAJXCUcf4' },
  { name: 'Basic', url: 'https://api.maptiler.com/maps/basic/style.json?key=BvYa8EIWKdXmAJXCUcf4' },
  { name: 'Streets', url: 'https://api.maptiler.com/maps/streets/style.json?key=BvYa8EIWKdXmAJXCUcf4' },
  { name: 'Topo', url: 'https://api.maptiler.com/maps/topo/style.json?key=BvYa8EIWKdXmAJXCUcf4' },
  { name: 'Bright', url: 'https://api.maptiler.com/maps/bright/style.json?key=BvYa8EIWKdXmAJXCUcf4' },
  { name: 'Pastel', url: 'https://api.maptiler.com/maps/pastel/style.json?key=BvYa8EIWKdXmAJXCUcf4' },
]);
const selectedStyle = ref(styles.value[1].url);

const DEFAULT_CENTER = [6.617, 51.6581];
const DEFAULT_ZOOM = 15;

onMounted(() => {
  map.value = new maplibregl.Map({
    container: mapContainer.value,
    style: selectedStyle.value,
    center: DEFAULT_CENTER,
    zoom: DEFAULT_ZOOM,
  });

  map.value.addControl(new maplibregl.NavigationControl(), 'top-right');
  map.value.addControl(
    new maplibregl.GeolocateControl({
      positionOptions: {
        enableHighAccuracy: true,
      },
      trackUserLocation: true,
    }),
    'top-left'
  );
  map.value.addControl(
    new maplibregl.ScaleControl({
      maxWidth: 80,
      unit: 'metric',
    }),
    'bottom-right'
  );
  map.value.addControl(new maplibregl.FullscreenControl(), 'top-left');

  new maplibregl.Marker()
    .setLngLat([6.617, 51.6581])
    .setPopup(new maplibregl.Popup().setHTML('<h3>Marker 1</h3><p>Description for Marker 1</p>'))
    .addTo(map.value);

  new maplibregl.Marker()
    .setLngLat([7.617, 50.6581])
    .setPopup(new maplibregl.Popup().setHTML('<h3>Marker 2</h3><p>Description for Marker 2</p>'))
    .addTo(map.value);

  map.value.on('load', () => {
    map.value.addSource('points', {
      type: 'geojson',
      data: {
        type: 'FeatureCollection',
        features: [
          {
            type: 'Feature',
            geometry: {
              type: 'Point',
              coordinates: [6.617, 51.6581],
            },
            properties: {
              title: 'Mapbox',
              description: 'Essen, Germany',
            },
          },
        ],
      },
    });

    map.value.addLayer({
      id: 'points',
      type: 'symbol',
      source: 'points',
      layout: {
        'text-field': ['get', 'title'],
        'text-font': ['Open Sans Semibold', 'Arial Unicode MS Bold'],
        'text-offset': [0, 0.6],
        'text-anchor': 'top',
      },
    });
  });

  map.value.on('click', 'points', (e) => {
    const coordinates = e.features[0].geometry.coordinates.slice();
    const description = e.features[0].properties.description;

    new maplibregl.Popup().setLngLat(coordinates).setHTML(description).addTo(map.value);
  });

  map.value.on('mouseenter', 'points', () => {
    map.value.getCanvas().style.cursor = 'pointer';
  });

  map.value.on('mouseleave', 'points', () => {
    map.value.getCanvas().style.cursor = '';
  });
});

const changeStyle = (styleUrl) => {
  map.value.setStyle(styleUrl);
};

const resetView = () => {
  map.value.flyTo({
    center: DEFAULT_CENTER,
    zoom: DEFAULT_ZOOM,
    essential: true, // This ensures the animation is considered essential and will not be affected by user preferences
    speed: 2,
  });

  setTimeout(() => {
    map.value.easeTo({ bearing: 0 });
  }, 1000);
};
</script>

<template>
  <Head title="Maps" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Maplibre Example</h1>
    </template>
    <main class="mx-4">
      <div class="mb-4">
        <label for="mapStyle" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >Select Map Style:</label
        >
        <select
          id="mapStyle"
          v-model="selectedStyle"
          @change="changeStyle(selectedStyle)"
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        >
          <option v-for="style in styles" :key="style.url" :value="style.url">{{ style.name }}</option>
        </select>
      </div>
      <div class="mb-4">
        <button @click="resetView" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
          Reset View
        </button>
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
