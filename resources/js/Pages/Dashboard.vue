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

defineProps({
  translations: Object,
});

Chart.register(...registerables);

// Doughnut Chart Data
const doughnutData = ref({
  labels: ['Clients', 'Projects', 'Tasks'],
  datasets: [
    {
      data: [30, 40, 20],
      backgroundColor: ['#77CEFF', '#0079AF', '#123E6B'],
    },
  ],
});

// Bar Chart Data
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

// Line Chart Data
const lineChartData = ref({
  labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
  datasets: [
    {
      label: 'Project Progress',
      borderColor: '#42A5F5',
      data: [20, 40, 60, 80, 100],
      fill: false,
    },
    {
      label: 'Milestones Achieved',
      borderColor: '#FF6384',
      data: [5, 10, 15, 20, 35, 50, 105],
      fill: false,
    },
  ],
});

// Radar Chart Data
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

// Polar Area Chart Data
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

// Pie Chart Data
const pieChartData = ref({
  labels: ['Red', 'Blue', 'Yellow'],
  datasets: [
    {
      data: [300, 50, 100],
      backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
    },
  ],
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
});
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
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Clients, Projects, and Tasks
          </h3>
          <DoughnutChart :chart-data="doughnutData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Revenue vs Expenses
          </h3>
          <BarChart :chart-data="barChartData" :options="chartOptions" />
        </div>
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Project Progress
          </h3>
          <LineChart :chart-data="lineChartData" :options="chartOptions" />
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

        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Pie Chart Example
          </h3>
          <PieChart :chart-data="pieChartData" :options="chartOptions" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Add any additional styling here */
</style>
