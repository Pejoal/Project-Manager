<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
  ArcElement,
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip,
} from 'chart.js';
import { computed } from 'vue';
import { BarChart, DoughnutChart } from 'vue-chart-3';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement, PointElement, LineElement);

const props = defineProps({
  stats: Object,
  dailyInteractions: Array,
  interactionsByType: Array,
  topUsers: Array,
  filters: Object,
});

const form = useForm({
  date_from: props.filters.date_from || new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  date_to: props.filters.date_to || new Date().toISOString().split('T')[0],
});

// Chart configurations
const dailyChartData = computed(() => ({
  labels: props.dailyInteractions.map((d) => new Date(d.date).toLocaleDateString()),
  datasets: [
    {
      label: 'Daily Interactions',
      data: props.dailyInteractions.map((d) => d.count),
      backgroundColor: 'rgba(59, 130, 246, 0.5)',
      borderColor: 'rgb(59, 130, 246)',
      borderWidth: 2,
    },
  ],
}));

const typeChartData = computed(() => {
  const typeColors = {
    call: '#EF4444',
    email: '#10B981',
    meeting: '#8B5CF6',
    note: '#6B7280',
    sms: '#F59E0B',
    social_media: '#EC4899',
    webinar: '#3B82F6',
    demo: '#14B8A6',
    other: '#9CA3AF',
  };

  return {
    labels: props.interactionsByType.map((t) => t.type.charAt(0).toUpperCase() + t.type.slice(1).replace('_', ' ')),
    datasets: [
      {
        data: props.interactionsByType.map((t) => t.count),
        backgroundColor: props.interactionsByType.map((t) => typeColors[t.type] || '#9CA3AF'),
        borderWidth: 2,
        borderColor: '#ffffff',
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    title: {
      display: false,
    },
  },
};

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'right',
    },
    title: {
      display: false,
    },
  },
};

const applyFilters = () => {
  form.get(route('interactions.analytics'), {
    preserveState: true,
    preserveScroll: true,
  });
};

const formatDuration = (minutes) => {
  if (!minutes) return 'N/A';
  const hours = Math.floor(minutes / 60);
  const mins = Math.round(minutes % 60);
  return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
};

// Stats cards configuration
const statsCards = computed(() => [
  {
    title: 'Total Interactions',
    value: props.stats.total_interactions?.toLocaleString() || '0',
    icon: '📊',
    color: 'bg-blue-500',
  },
  {
    title: 'Total Calls',
    value: props.stats.total_calls?.toLocaleString() || '0',
    icon: '📞',
    color: 'bg-red-500',
  },
  {
    title: 'Total Emails',
    value: props.stats.total_emails?.toLocaleString() || '0',
    icon: '📧',
    color: 'bg-green-500',
  },
  {
    title: 'Total Meetings',
    value: props.stats.total_meetings?.toLocaleString() || '0',
    icon: '🤝',
    color: 'bg-purple-500',
  },
  {
    title: 'Average Duration',
    value: formatDuration(props.stats.avg_duration),
    icon: '⏱️',
    color: 'bg-yellow-500',
  },
  {
    title: 'Positive Outcomes',
    value: props.stats.positive_outcomes?.toLocaleString() || '0',
    icon: '✅',
    color: 'bg-emerald-500',
  },
  {
    title: 'Pending Follow-ups',
    value: props.stats.pending_follow_ups?.toLocaleString() || '0',
    icon: '⏰',
    color: 'bg-orange-500',
  },
]);
</script>

<template>
  <Head title="Interactions Analytics" />

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <Link
                :href="route('interactions.index')"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </Link>
              <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Interactions Analytics</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Analyze your interaction patterns and performance
                </p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('interactions.follow-ups')"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
              >
                View Follow-ups
              </Link>
              <Link
                :href="route('interactions.index')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                Back to Interactions
              </Link>
            </div>
          </div>
        </div>

        <!-- Date Range Filter -->
        <div class="p-6">
          <form @submit.prevent="applyFilters" class="flex items-end space-x-4">
            <div>
              <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                From Date
              </label>
              <input
                id="date_from"
                v-model="form.date_from"
                type="date"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <div>
              <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> To Date </label>
              <input
                id="date_to"
                v-model="form.date_to"
                type="date"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
            >
              {{ form.processing ? 'Loading...' : 'Apply' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div
          v-for="stat in statsCards"
          :key="stat.title"
          class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg"
        >
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div :class="[stat.color, 'p-3 rounded-md']">
                  <span class="text-2xl">{{ stat.icon }}</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ stat.title }}
                  </dt>
                  <dd>
                    <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ stat.value }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Interactions Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daily Interactions Trend</h3>
          <div class="h-64">
            <BarChart v-if="dailyInteractions.length > 0" :data="dailyChartData" :options="chartOptions" />
            <div v-else class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
              No data available for the selected period
            </div>
          </div>
        </div>

        <!-- Interactions by Type Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Interactions by Type</h3>
          <div class="h-64">
            <DoughnutChart v-if="interactionsByType.length > 0" :data="typeChartData" :options="doughnutOptions" />
            <div v-else class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
              No data available for the selected period
            </div>
          </div>
        </div>
      </div>

      <!-- Top Users Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Top Users by Interactions</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Rank
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  User
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Interactions
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Percentage
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-if="topUsers.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  No data available for the selected period
                </td>
              </tr>
              <tr v-for="(user, index) in topUsers" :key="user.user_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <span
                      :class="[
                        'inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium',
                        index === 0
                          ? 'bg-yellow-100 text-yellow-800'
                          : index === 1
                            ? 'bg-gray-100 text-gray-800'
                            : index === 2
                              ? 'bg-orange-100 text-orange-800'
                              : 'bg-blue-100 text-blue-800',
                      ]"
                    >
                      {{ index + 1 }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ user.user?.name || 'Unknown User' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-gray-100">
                    {{ user.count.toLocaleString() }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                      <div
                        class="bg-blue-600 h-2 rounded-full"
                        :style="{ width: `${(user.count / topUsers[0]?.count) * 100 || 0}%` }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-900 dark:text-gray-100">
                      {{ Math.round((user.count / stats.total_interactions) * 100) || 0 }}%
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Insights Section -->
      <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Key Insights</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Success Rate -->
          <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <dt class="text-sm font-medium text-green-700 dark:text-green-300">Success Rate</dt>
                <dd class="text-lg font-semibold text-green-900 dark:text-green-100">
                  {{
                    stats.total_interactions > 0
                      ? Math.round((stats.positive_outcomes / stats.total_interactions) * 100)
                      : 0
                  }}%
                </dd>
              </div>
            </div>
          </div>

          <!-- Follow-up Rate -->
          <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <dt class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Pending Follow-ups</dt>
                <dd class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">
                  {{ stats.pending_follow_ups || 0 }}
                </dd>
              </div>
            </div>
          </div>

          <!-- Most Popular Type -->
          <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <dt class="text-sm font-medium text-blue-700 dark:text-blue-300">Most Popular Type</dt>
                <dd class="text-lg font-semibold text-blue-900 dark:text-blue-100 capitalize">
                  {{ interactionsByType.length > 0 ? interactionsByType[0].type.replace('_', ' ') : 'N/A' }}
                </dd>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
