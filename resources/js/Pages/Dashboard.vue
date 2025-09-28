<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { ref } from 'vue';
import { BarChart, DoughnutChart, LineChart, PieChart } from 'vue-chart-3';

const props = defineProps({
  tasksStatusesData: Object,
  tasksPrioritiesData: Object,
  doughnutData: Object,
  lineChartData: Object,
  settings: {
    type: Object,
    default: {},
  },
});

Chart.register(...registerables);

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

// onMounted(() => {
//   window.Echo.channel('chat').listen('MessageSent', (data) => {
//     console.log('Message received:', data.message);
//   });
// });
</script>

<template>
  <Head :title="trans('words.dashboard')" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ trans('words.welcome') }} {{ $page.props.auth.user.name }}
      </h2>
    </div>
  </header>

  <div class="max-w-7xl mx-auto sm:px-1 lg:px-8">
    <div class="p-2 my-1 bg-white dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
      <div class="mb-2 pb-2 border-b">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          {{ trans('words.update_chart_colors') }}
        </h3>
        <form @submit.prevent="updateSettings">
          <div class="mb-4">
            <label for="clients_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
              trans('words.clients_color')
            }}</label>
            <input v-model="form.clients_color" type="color" id="clients_color" class="mt-1 block w-full" />
          </div>
          <div class="mb-4">
            <label for="projects_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
              trans('words.projects_color')
            }}</label>
            <input v-model="form.projects_color" type="color" id="projects_color" class="mt-1 block w-full" />
          </div>
          <div class="mb-4">
            <label for="tasks_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
              trans('words.tasks_color')
            }}</label>
            <input v-model="form.tasks_color" type="color" id="tasks_color" class="mt-1 block w-full" />
          </div>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-500 text-white rounded">
            {{ trans('words.save') }}
          </button>
        </form>
      </div>
      <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ trans('words.overview') }}</h3>
        <LineChart :chart-data="lineChartData" :options="chartOptions" />
      </div>
      <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ trans('words.tasks_statuses') }}</h3>
        <BarChart :chart-data="tasksStatusesData" :options="chartOptions" />
      </div>
      <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          {{ trans('words.tasks_priorities') }}
        </h3>
        <LineChart :chart-data="tasksPrioritiesData" :options="chartOptions" />
      </div>
      <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          {{ trans('words.clients_projects_tasks') }}
        </h3>
        <DoughnutChart :chart-data="doughnutData" :options="chartOptions" />
      </div>
      <div class="mb-4 pb-2 border-b">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
          {{ trans('words.clients_projects_tasks') }}
        </h3>
        <PieChart :chart-data="doughnutData" :options="chartOptions" />
      </div>
    </div>
  </div>
</template>

<style scoped></style>
