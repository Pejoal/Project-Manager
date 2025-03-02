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

const addingPoints = ref(false);
const addingLineString = ref(false);
const lineCoordinates = ref([]);

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

  // Add a source and layer for the points
  map.value.on('load', () => {
    map.value.addSource('points', {
      type: 'geojson',
      data: {
        type: 'FeatureCollection',
        features: [],
      },
    });

    map.value.addLayer({
      id: 'points',
      type: 'circle',
      source: 'points',
      paint: {
        'circle-radius': 5,
        'circle-color': '#602cFf',
        'circle-stroke-width': 2,
        'circle-stroke-color': '#ffffff',
      },
    });

    map.value.addSource('lines', {
      type: 'geojson',
      data: {
        type: 'FeatureCollection',
        features: [],
      },
    });

    map.value.addLayer({
      id: 'lines',
      type: 'line',
      source: 'lines',
      paint: {
        'line-color': '#ff0000',
        'line-width': 2,
      },
    });
  });

  map.value.on('mouseenter', 'points', () => {
    map.value.getCanvas().style.cursor = 'pointer';
  });

  map.value.on('mouseleave', 'points', () => {
    map.value.getCanvas().style.cursor = '';
  });

  map.value.on('mouseenter', 'lines', () => {
    map.value.getCanvas().style.cursor = 'pointer';
  });

  map.value.on('mouseleave', 'lines', () => {
    map.value.getCanvas().style.cursor = '';
  });

  // Add a click event listener to add a point or a line
  map.value.on('click', (e) => {
    if (addingPoints.value) {
      const features = map.value.getSource('points')._data.features;
      features.push({
        type: 'Feature',
        geometry: {
          type: 'Point',
          coordinates: [e.lngLat.lng, e.lngLat.lat],
        },
      });

      map.value.getSource('points').setData({
        type: 'FeatureCollection',
        features: features,
      });
    } else if (addingLineString.value) {
      lineCoordinates.value.push([e.lngLat.lng, e.lngLat.lat]);

      if (lineCoordinates.value.length > 1) {
        const features = map.value.getSource('lines')._data.features;
        features.push({
          type: 'Feature',
          geometry: {
            type: 'LineString',
            coordinates: lineCoordinates.value,
          },
        });

        map.value.getSource('lines').setData({
          type: 'FeatureCollection',
          features: features,
        });
      }

      // Change cursor style while drawing a line
      map.value.getCanvas().style.cursor = 'crosshair';
    }
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
    pitch: 0,
    speed: 2,
  });

  setTimeout(() => {
    map.value.easeTo({ bearing: 0 });
  }, 1500);
};

const toggleAddingPoints = () => {
  addingPoints.value = !addingPoints.value;
  addingLineString.value = false;
  lineCoordinates.value = [];
  map.value.getCanvas().style.cursor = addingPoints.value ? 'pointer' : '';
};

const toggleAddingLineString = () => {
  addingLineString.value = !addingLineString.value;
  addingPoints.value = false;
  lineCoordinates.value = [];
  map.value.getCanvas().style.cursor = addingLineString.value ? 'crosshair' : '';
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
      <div class="mb-4">
        <button
          @click="toggleAddingPoints"
          :class="{ 'bg-green-500': addingPoints, 'bg-gray-500': !addingPoints }"
          class="px-4 py-2 text-white rounded-md hover:bg-green-600"
        >
          {{ addingPoints ? 'Disable' : 'Enable' }} Adding Points
        </button>
        <button
          @click="toggleAddingLineString"
          :class="{ 'bg-green-500': addingLineString, 'bg-gray-500': !addingLineString }"
          class="px-4 py-2 text-white rounded-md hover:bg-green-600 ml-2"
        >
          {{ addingLineString ? 'Disable' : 'Enable' }} Adding LineString
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
