<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  DoughnutChart,
  BarChart,
  LineChart,
  RadarChart,
  PolarAreaChart,
  PieChart,
} from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  translations: Object,
  doughnutData: Object,
  lineChartData: Object,
  settings: {
    type: Object,
    default: {},
  },
});

Chart.register(...registerables);

const barChartData = ref({
  labels: ['January', 'February', 'March', 'April', 'May'],
  datasets: [
    {
      label: 'Revenue',
      backgroundColor: '#42A5F5',
      data: [10000, 12000, 15000, 17000, 20000],
    },
    {
      label: 'Expenses',
      backgroundColor: '#FF6384',
      data: [5000, 6000, 7000, 8000, 9000],
    },
  ],
});

const radarChartData = ref({
  labels: [
    'Communication',
    'Technical Skills',
    'Teamwork',
    'Problem Solving',
    'Leadership',
  ],
  datasets: [
    {
      label: 'Employee A',
      backgroundColor: 'rgba(179,181,198,0.2)',
      borderColor: 'rgba(179,181,198,1)',
      pointBackgroundColor: 'rgba(179,181,198,1)',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: 'rgba(179,181,198,1)',
      data: [65, 59, 90, 81, 56],
    },
    {
      label: 'Employee B',
      backgroundColor: 'rgba(255,99,132,0.2)',
      borderColor: 'rgba(255,99,132,1)',
      pointBackgroundColor: 'rgba(255,99,132,1)',
      pointBorderColor: '#fff',
      pointHoverBackgroundColor: '#fff',
      pointHoverBorderColor: 'rgba(255,99,132,1)',
      data: [28, 48, 40, 19, 96],
    },
  ],
});

const polarAreaChartData = ref({
  labels: ['Red', 'Green', 'Yellow', 'Grey', 'Blue'],
  datasets: [
    {
      data: [11, 16, 7, 3, 14],
      backgroundColor: ['#FF6384', '#4BC0C0', '#FFCE56', '#E7E9ED', '#36A2EB'],
      label: 'My dataset', // for legend
    },
  ],
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
});

const form = useForm({
  clients_color: props.settings?.clients_color,
  projects_color: props.settings?.projects_color,
  tasks_color: props.settings?.tasks_color,
});

const updateSettings = () => {
  form.put('/api/settings', {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        {{ translations.welcome }} {{ $page.props.auth.user.name }}
      </h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-1 lg:px-8">
      <div
        class="p-2 my-1 bg-white dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
      >
        <div class="mb-2 pb-2 border-b">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Update Chart Colors
          </h3>
          <form @submit.prevent="updateSettings">
            <div class="mb-4">
              <label
                for="clients_color"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Clients Color</label
              >
              <input
                v-model="form.clients_color"
                type="color"
                id="clients_color"
                class="mt-1 block w-full"
              />
            </div>
            <div class="mb-4">
              <label
                for="projects_color"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Projects Color</label
              >
              <input
                v-model="form.projects_color"
                type="color"
                id="projects_color"
                class="mt-1 block w-full"
              />
            </div>
            <div class="mb-4">
              <label
                for="tasks_color"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Tasks Color</label
              >
              <input
                v-model="form.tasks_color"
                type="color"
                id="tasks_color"
                class="mt-1 block w-full"
              />
            </div>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 bg-blue-500 text-white rounded"
            >
              Save
            </button>
          </form>
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Clients, Projects, and Tasks
          </h3>
          <DoughnutChart :chart-data="doughnutData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Pie Chart Example
          </h3>
          <PieChart :chart-data="doughnutData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Project Progress
          </h3>
          <LineChart :chart-data="lineChartData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Revenue vs Expenses
          </h3>
          <BarChart :chart-data="barChartData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Employee Skills Comparison
          </h3>
          <RadarChart :chart-data="radarChartData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Polar Area Chart Example
          </h3>
          <PolarAreaChart
            :chart-data="polarAreaChartData"
            :options="chartOptions"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Add any additional styling here */
</style>
