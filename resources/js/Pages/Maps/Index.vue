<script setup>
import { ref, onMounted, computed } from 'vue';
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
const addingPolygon = ref(false);
const lineCoordinates = ref([]);
const polygonCoordinates = ref([]);
const features = ref([]);
const filterText = ref('');
const selectedFeature = ref(null);
const newPropertyKey = ref('');
const newPropertyValue = ref('');
const isFeaturesListOpen = ref(false);
const isControlsOpen = ref(false);

const filteredFeatures = computed(() => {
  return features.value.filter((feature) => {
    return feature.properties.name.toLowerCase().includes(filterText.value.toLowerCase());
  });
});

// Save map preferences
const savePreferences = () => {
  const preferences = {
    center: map.value.getCenter(),
    zoom: map.value.getZoom(),
    selectedStyle: selectedStyle.value,
    addingPoints: addingPoints.value,
    addingLineString: addingLineString.value,
    addingPolygon: addingPolygon.value,
    isFeaturesListOpen: isFeaturesListOpen.value,
    isControlsOpen: isControlsOpen.value,
    features: features.value,
  };
  localStorage.setItem('preferences', JSON.stringify(preferences));
};

// Load map preferences
const loadPreferences = () => {
  const preferences = JSON.parse(localStorage.getItem('preferences'));
  if (preferences) {
    map.value.setCenter(preferences.center);
    map.value.setZoom(preferences.zoom);
    addingPoints.value = preferences.addingPoints;
    selectedStyle.value = preferences.selectedStyle;
    changeStyle(preferences.selectedStyle);
    addingLineString.value = preferences.addingLineString;
    addingPolygon.value = preferences.addingPolygon;
    isFeaturesListOpen.value = preferences.isFeaturesListOpen;
    isControlsOpen.value = preferences.isControlsOpen;
    features.value = preferences.features || [];

    // Re-add sources and layers for points, lines, and polygons
    if (map.value.getSource('points')) {
      map.value.getSource('points').setData({
        type: 'FeatureCollection',
        features: features.value.filter((f) => f.geometry.type === 'Point'),
      });
    }

    if (map.value.getSource('lines')) {
      map.value.getSource('lines').setData({
        type: 'FeatureCollection',
        features: features.value.filter((f) => f.geometry.type === 'LineString'),
      });
    }

    if (map.value.getSource('polygons')) {
      map.value.getSource('polygons').setData({
        type: 'FeatureCollection',
        features: features.value.filter((f) => f.geometry.type === 'Polygon'),
      });
    }
  }
};

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

  // Add sources and layers for points, lines, and polygons
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

    map.value.addSource('polygons', {
      type: 'geojson',
      data: {
        type: 'FeatureCollection',
        features: [],
      },
    });

    map.value.addLayer({
      id: 'polygons',
      type: 'fill',
      source: 'polygons',
      paint: {
        'fill-color': '#00ff00',
        'fill-opacity': 0.5,
      },
    });

    loadPreferences();
  });

  // Change cursor style on mouse enter and leave for points and lines
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

  map.value.on('mouseenter', 'polygons', () => {
    map.value.getCanvas().style.cursor = 'pointer';
  });

  map.value.on('mouseleave', 'polygons', () => {
    map.value.getCanvas().style.cursor = '';
  });

  // Add a click event listener to add a point, line, or polygon
  map.value.on('click', (e) => {
    if (addingPoints.value) {
      const feature = {
        type: 'Feature',
        geometry: {
          type: 'Point',
          coordinates: [e.lngLat.lng, e.lngLat.lat],
        },
        properties: {
          name: `Point ${features.value.length + 1}`,
        },
      };
      features.value.push(feature);

      map.value.getSource('points').setData({
        type: 'FeatureCollection',
        features: features.value.filter((f) => f.geometry.type === 'Point'),
      });
    } else if (addingLineString.value) {
      lineCoordinates.value.push([e.lngLat.lng, e.lngLat.lat]);

      if (lineCoordinates.value.length > 1) {
        const feature = {
          type: 'Feature',
          geometry: {
            type: 'LineString',
            coordinates: lineCoordinates.value,
          },
          properties: {
            name: `Line ${features.value.length + 1}`,
          },
        };
        features.value.push(feature);

        map.value.getSource('lines').setData({
          type: 'FeatureCollection',
          features: features.value.filter((f) => f.geometry.type === 'LineString'),
        });
      }

      // Change cursor style while drawing a line
      map.value.getCanvas().style.cursor = 'crosshair';
    } else if (addingPolygon.value) {
      polygonCoordinates.value.push([e.lngLat.lng, e.lngLat.lat]);

      if (polygonCoordinates.value.length > 2) {
        const feature = {
          type: 'Feature',
          geometry: {
            type: 'Polygon',
            coordinates: [polygonCoordinates.value],
          },
          properties: {
            name: `Polygon ${features.value.length + 1}`,
          },
        };
        features.value.push(feature);

        map.value.getSource('polygons').setData({
          type: 'FeatureCollection',
          features: features.value.filter((f) => f.geometry.type === 'Polygon'),
        });
      }

      // Change cursor style while drawing a polygon
      map.value.getCanvas().style.cursor = 'crosshair';
    } else {
      // Deselect feature if clicking on the map without adding mode
      selectedFeature.value = null;
    }

    savePreferences();
  });

  // Add a click event listener to select a feature
  map.value.on('click', 'points', (e) => {
    if (!addingPoints.value && !addingLineString.value && !addingPolygon.value) {
      const feature = e.features[0];
      selectFeature(feature);
    }
  });

  map.value.on('click', 'lines', (e) => {
    if (!addingPoints.value && !addingLineString.value && !addingPolygon.value) {
      const feature = e.features[0];
      selectFeature(feature);
    }
  });

  map.value.on('click', 'polygons', (e) => {
    if (!addingPoints.value && !addingLineString.value && !addingPolygon.value) {
      const feature = e.features[0];
      selectFeature(feature);
    }
  });
});

const changeStyle = (styleUrl) => {
  map.value.setStyle(styleUrl);

  map.value.once('idle', () => {
    // Re-add sources and layers for points, lines, and polygons if they do not exist
    if (!map.value.getSource('points')) {
      map.value.addSource('points', {
        type: 'geojson',
        data: {
          type: 'FeatureCollection',
          features: features.value.filter((f) => f.geometry.type === 'Point'),
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
    }

    if (!map.value.getSource('lines')) {
      map.value.addSource('lines', {
        type: 'geojson',
        data: {
          type: 'FeatureCollection',
          features: features.value.filter((f) => f.geometry.type === 'LineString'),
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
    }

    if (!map.value.getSource('polygons')) {
      map.value.addSource('polygons', {
        type: 'geojson',
        data: {
          type: 'FeatureCollection',
          features: features.value.filter((f) => f.geometry.type === 'Polygon'),
        },
      });

      map.value.addLayer({
        id: 'polygons',
        type: 'fill',
        source: 'polygons',
        paint: {
          'fill-color': '#00ff00',
          'fill-opacity': 0.5,
        },
      });
    }
  });
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
  addingPolygon.value = false;
  lineCoordinates.value = [];
  polygonCoordinates.value = [];
  map.value.getCanvas().style.cursor = addingPoints.value ? 'pointer' : '';
};

const toggleAddingLineString = () => {
  addingLineString.value = !addingLineString.value;
  addingPoints.value = false;
  addingPolygon.value = false;
  lineCoordinates.value = [];
  polygonCoordinates.value = [];
  map.value.getCanvas().style.cursor = addingLineString.value ? 'crosshair' : '';
};

const toggleAddingPolygon = () => {
  addingPolygon.value = !addingPolygon.value;
  addingPoints.value = false;
  addingLineString.value = false;
  lineCoordinates.value = [];
  polygonCoordinates.value = [];
  map.value.getCanvas().style.cursor = addingPolygon.value ? 'crosshair' : '';
};

const deleteFeatures = () => {
  features.value = [];
  selectedFeature.value = null;

  map.value.getSource('points').setData({
    type: 'FeatureCollection',
    features: [],
  });
  map.value.getSource('lines').setData({
    type: 'FeatureCollection',
    features: [],
  });
  map.value.getSource('polygons').setData({
    type: 'FeatureCollection',
    features: [],
  });
};

const deleteFeature = (feature) => {
  features.value = features.value.filter((f) => f !== feature);
  selectedFeature.value = null;

  map.value.getSource('points').setData({
    type: 'FeatureCollection',
    features: features.value.filter((f) => f.geometry.type === 'Point'),
  });
  map.value.getSource('lines').setData({
    type: 'FeatureCollection',
    features: features.value.filter((f) => f.geometry.type === 'LineString'),
  });
  map.value.getSource('polygons').setData({
    type: 'FeatureCollection',
    features: features.value.filter((f) => f.geometry.type === 'Polygon'),
  });
};

const selectFeature = (feature) => {
  selectedFeature.value = feature;

  const coordinates = feature.geometry.coordinates;
  let bounds;

  if (feature.geometry.type === 'Point') {
    bounds = new maplibregl.LngLatBounds(coordinates, coordinates);
  } else if (feature.geometry.type === 'LineString') {
    bounds = coordinates.reduce(
      (bounds, coord) => {
        return bounds.extend(coord);
      },
      new maplibregl.LngLatBounds(coordinates[0], coordinates[0])
    );
  } else if (feature.geometry.type === 'Polygon') {
    const flattenedCoordinates = coordinates.flat();
    bounds = flattenedCoordinates.reduce(
      (bounds, coord) => {
        return bounds.extend(coord);
      },
      new maplibregl.LngLatBounds(flattenedCoordinates[0], flattenedCoordinates[0])
    );
  }

  map.value.fitBounds(bounds, {
    padding: 20,
  });
};

const addPropertyToFeature = () => {
  if (selectedFeature.value && newPropertyKey.value && newPropertyValue.value) {
    selectedFeature.value.properties[newPropertyKey.value] = newPropertyValue.value;
    newPropertyKey.value = '';
    newPropertyValue.value = '';
  }
};

const toggleFeaturesList = () => {
  isFeaturesListOpen.value = !isFeaturesListOpen.value;
};
const toggleControls = () => {
  isControlsOpen.value = !isControlsOpen.value;
};
</script>

<template>
  <Head title="Maplibre Example" />
  <AppLayout>
    <main>
      <div class="p-4">
        <button
          @click="toggleControls"
          :class="{ 'bg-green-500': isControlsOpen, 'bg-gray-500': !isControlsOpen }"
          class="px-4 py-2 text-white rounded-md hover:bg-green-600"
        >
          {{ isControlsOpen ? 'Hide' : 'Show' }} Controls
        </button>
      </div>
      <section class="mx-4" v-if="isControlsOpen">
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
          <button
            @click="toggleAddingPolygon"
            :class="{ 'bg-green-500': addingPolygon, 'bg-gray-500': !addingPolygon }"
            class="px-4 py-2 text-white rounded-md hover:bg-green-600 ml-2"
          >
            {{ addingPolygon ? 'Disable' : 'Enable' }} Adding Polygon
          </button>
          <button @click="deleteFeatures" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 ml-2">
            Delete All Features
          </button>
        </div>
        <div class="mb-4">
          <button
            @click="toggleFeaturesList"
            :class="{ 'bg-green-500': isFeaturesListOpen, 'bg-gray-500': !isFeaturesListOpen }"
            class="px-4 py-2 text-white rounded-md hover:bg-green-600"
          >
            {{ isFeaturesListOpen ? 'Hide' : 'Show' }} Features List
          </button>
        </div>
        <div v-if="isFeaturesListOpen" class="mb-4">
          <input
            type="text"
            v-model="filterText"
            placeholder="Filter features"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          />
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Name
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Type
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="feature in filteredFeatures" :key="feature.properties.name">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ feature.properties.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                  {{ feature.geometry.type }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="selectFeature(feature)"
                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-600"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteFeature(feature)"
                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-600 ml-2"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="selectedFeature" class="mb-4">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Edit Feature: {{ selectedFeature.properties.name }}
          </h2>
          <div class="mt-2">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Properties:</h3>
            <ul class="list-disc pl-5">
              <li
                v-for="(value, key) in selectedFeature.properties"
                :key="key"
                class="text-sm text-gray-700 dark:text-gray-300"
              >
                {{ key }}: {{ value }}
              </li>
            </ul>
          </div>
          <div class="mt-2">
            <label for="newPropertyKey" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Property Key</label
            >
            <input
              type="text"
              id="newPropertyKey"
              v-model="newPropertyKey"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            />
          </div>
          <div class="mt-2">
            <label for="newPropertyValue" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Property Value</label
            >
            <input
              type="text"
              id="newPropertyValue"
              v-model="newPropertyValue"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            />
          </div>
          <div class="mt-4">
            <button @click="addPropertyToFeature" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
              Add Property
            </button>
          </div>
        </div>
      </section>

      <section ref="mapContainer" class="map-container"></section>
    </main>
  </AppLayout>
</template>

<style scoped>
.map-container {
  width: 100%;
  height: 500px;
}
</style>
