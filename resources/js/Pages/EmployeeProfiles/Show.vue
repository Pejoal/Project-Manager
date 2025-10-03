<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  profile: Object,
  stats: Object,
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount || 0);
};

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString('de-DE') : 'N/A';
};

const statusClass = computed(() => {
  return props.profile.is_active
    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
    : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
});
</script>

<template>
  <Head :title="`Profile: ${profile.user.name}`" />
  <header class="bg-white dark:bg-gray-800 shadow">
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Employee Profile: {{ profile.user.name }}
        </h2>
        <Link
          :href="route('employee-profiles.edit', profile.id)"
          class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
        >
          Edit Profile
        </Link>
      </div>
    </main>
  </header>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
          <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Hours (This Month)</h3>
          <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
            {{ formatHours(stats.current_month_hours) }}h
          </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
          <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Earnings (This Month)</h3>
          <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
            {{ formatCurrency(stats.current_month_earnings) }}
          </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
          <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Time Entries</h3>
          <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.pending_entries }}</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Profile Information</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Personal and payroll details.</p>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700">
          <dl class="grid grid-cols-1 md:grid-cols-2">
            <div
              v-for="(value, key) in profile"
              :key="key"
              class="px-4 py-5 sm:px-6"
              :class="
                ['user', 'timeEntries', 'payslips'].includes(key)
                  ? 'hidden'
                  : 'bg-gray-50 dark:bg-gray-700/50 odd:bg-white dark:odd:bg-gray-800'
              "
            >
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">
                {{ key.replace(/_/g, ' ') }}
              </dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                <span v-if="key === 'is_active'" :class="statusClass" class="px-2 py-1 rounded-full text-xs">
                  {{ value ? 'Active' : 'Inactive' }}
                </span>
                <span v-else>{{ value || 'N/A' }}</span>
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>
