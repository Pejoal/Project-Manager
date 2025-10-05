<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
  IconArrowLeft,
  IconCalendar,
  IconCheck,
  IconClock,
  IconMail,
  IconPhone,
  IconTrendingUp,
  IconUsers,
} from '@tabler/icons-vue';
import { computed } from 'vue';

const props = defineProps({
  stats: Object,
  dailyInteractions: Array,
  interactionsByType: Array,
  topUsers: Array,
  filters: Object,
});

const form = useForm({
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

// Filter interactions
const applyFilters = () => {
  router.get(route('interactions.analytics'), form.data(), {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  form.reset();
  router.get(route('interactions.analytics'));
};

// Computed properties for charts and metrics
const totalInteractions = computed(() => props.stats?.total_interactions || 0);
const avgDuration = computed(() => {
  const duration = props.stats?.avg_duration || 0;
  return Math.round(duration);
});

const positiveOutcomeRate = computed(() => {
  if (!totalInteractions.value || !props.stats?.positive_outcomes) return 0;
  return Math.round((props.stats.positive_outcomes / totalInteractions.value) * 100);
});

const typeStats = computed(() => {
  return (
    props.interactionsByType?.map((item) => ({
      ...item,
      percentage: totalInteractions.value ? Math.round((item.count / totalInteractions.value) * 100) : 0,
    })) || []
  );
});

// Chart data preparation
const dailyChartData = computed(() => {
  if (!props.dailyInteractions?.length) return [];

  return props.dailyInteractions.map((item) => ({
    date: new Date(item.date).toLocaleDateString(),
    count: item.count,
  }));
});

// Utility functions
const getTypeIcon = (type) => {
  const icons = {
    call: IconPhone,
    email: IconMail,
    meeting: IconCalendar,
    note: IconUsers,
    sms: IconPhone,
    social_media: IconUsers,
    webinar: IconUsers,
    demo: IconUsers,
    other: IconUsers,
  };
  return icons[type] || IconUsers;
};

const getTypeColor = (type) => {
  const colors = {
    call: 'bg-blue-500',
    email: 'bg-green-500',
    meeting: 'bg-purple-500',
    note: 'bg-gray-500',
    sms: 'bg-yellow-500',
    social_media: 'bg-pink-500',
    webinar: 'bg-indigo-500',
    demo: 'bg-orange-500',
    other: 'bg-gray-400',
  };
  return colors[type] || 'bg-gray-400';
};

const formatDuration = (minutes) => {
  if (!minutes) return '0 min';
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;

  if (hours > 0) {
    return `${hours}h ${mins}m`;
  }
  return `${mins}m`;
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};
</script>

<template>
  <Head title="Interaction Analytics" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('interactions.index')"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <IconArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ $t('crm.interaction_analytics') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ $t('crm.interaction_analytics_description') }}
            </p>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('interactions.index')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.all_interactions') }}
          </Link>
          <Link
            :href="route('interactions.follow-ups')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.follow_ups') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Filters -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Date From -->
          <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              {{ $t('crm.date_from') }}
            </label>
            <input
              id="date_from"
              v-model="form.date_from"
              type="date"
              class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            />
          </div>

          <!-- Date To -->
          <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              {{ $t('crm.date_to') }}
            </label>
            <input
              id="date_to"
              v-model="form.date_to"
              type="date"
              class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            />
          </div>

          <!-- Actions -->
          <div class="flex items-end space-x-2">
            <button @click="applyFilters" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.filter') }}
            </button>
            <button @click="clearFilters" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.clear') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Interactions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconUsers class="w-8 h-8 text-blue-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.total_interactions') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ totalInteractions.toLocaleString() }}
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Total Calls -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconPhone class="w-8 h-8 text-green-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.total_calls') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ (stats?.total_calls || 0).toLocaleString() }}
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Total Emails -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconMail class="w-8 h-8 text-purple-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.total_emails') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ (stats?.total_emails || 0).toLocaleString() }}
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Total Meetings -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconCalendar class="w-8 h-8 text-orange-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.total_meetings') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ (stats?.total_meetings || 0).toLocaleString() }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Secondary Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Average Duration -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconClock class="w-8 h-8 text-indigo-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.avg_duration') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ formatDuration(avgDuration) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Positive Outcomes -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconCheck class="w-8 h-8 text-green-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.positive_outcomes') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ (stats?.positive_outcomes || 0).toLocaleString() }} ({{ positiveOutcomeRate }}%)
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Pending Follow-ups -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <IconTrendingUp class="w-8 h-8 text-yellow-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                  {{ $t('crm.pending_follow_ups') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ (stats?.pending_follow_ups || 0).toLocaleString() }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and Tables -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Interactions Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.daily_interactions') }}
          </h3>
          <div v-if="dailyChartData.length > 0" class="space-y-2">
            <!-- Simple bar chart representation -->
            <div v-for="item in dailyChartData.slice(-7)" :key="item.date" class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400 w-20">{{ item.date }}</span>
              <div class="flex-1 mx-4">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                  <div
                    class="bg-blue-500 h-4 rounded-full"
                    :style="`width: ${Math.max((item.count / Math.max(...dailyChartData.map((d) => d.count))) * 100, 5)}%`"
                  ></div>
                </div>
              </div>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100 w-8 text-right">{{ item.count }}</span>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 dark:text-gray-400 py-8">
            {{ $t('crm.no_data_available') }}
          </div>
        </div>

        <!-- Interactions by Type -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.interactions_by_type') }}
          </h3>
          <div v-if="typeStats.length > 0" class="space-y-4">
            <div v-for="item in typeStats" :key="item.type" class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <component :is="getTypeIcon(item.type)" class="w-5 h-5 text-gray-600" />
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100 capitalize">
                  {{ item.type.replace('_', ' ') }}
                </span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div
                    :class="`h-2 rounded-full ${getTypeColor(item.type)}`"
                    :style="`width: ${item.percentage}%`"
                  ></div>
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400 w-12 text-right">
                  {{ item.count }} ({{ item.percentage }}%)
                </span>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 dark:text-gray-400 py-8">
            {{ $t('crm.no_data_available') }}
          </div>
        </div>
      </div>

      <!-- Top Users -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          {{ $t('crm.most_active_users') }}
        </h3>
        <div v-if="topUsers?.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ $t('words.user') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ $t('crm.total_interactions') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ $t('crm.percentage') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="user in topUsers" :key="user.user_id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ user.user?.name || 'Unknown User' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ user.count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div class="flex items-center space-x-2">
                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                      <div
                        class="bg-blue-500 h-2 rounded-full"
                        :style="`width: ${totalInteractions ? Math.round((user.count / totalInteractions) * 100) : 0}%`"
                      ></div>
                    </div>
                    <span>{{ totalInteractions ? Math.round((user.count / totalInteractions) * 100) : 0 }}%</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-8">
          {{ $t('crm.no_data_available') }}
        </div>
      </div>
    </div>
  </div>
</template>
