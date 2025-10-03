<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CreateTimeEntryModal from '../TimeEntries/CreateTimeEntryModal.vue';

const props = defineProps({
  profile: Object,
  stats: Object,
  projects: {
    type: Array,
    default: () => [],
  },
});

const showCreateTimeEntryModal = ref(false);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount || 0);
};

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('de-DE', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatDateShort = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('de-DE');
};

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  return new Date(datetime).toLocaleString('de-DE', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const statusClass = computed(() => {
  return props.profile.is_active
    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
    : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
});

const employmentStatus = computed(() => {
  const now = new Date();
  const hireDate = new Date(props.profile.hire_date);
  const terminationDate = props.profile.termination_date ? new Date(props.profile.termination_date) : null;

  if (terminationDate && terminationDate <= now) {
    return { text: 'Terminated', class: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' };
  }

  if (hireDate > now) {
    return { text: 'Future Employee', class: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' };
  }

  return { text: 'Active', class: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' };
});

const employmentDuration = computed(() => {
  const now = new Date();
  const hireDate = new Date(props.profile.hire_date);
  const endDate = props.profile.termination_date ? new Date(props.profile.termination_date) : now;

  const diffTime = Math.abs(endDate - hireDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  const years = Math.floor(diffDays / 365);
  const months = Math.floor((diffDays % 365) / 30);

  if (years > 0) {
    return `${years} year${years > 1 ? 's' : ''}, ${months} month${months > 1 ? 's' : ''}`;
  }
  return `${months} month${months > 1 ? 's' : ''}`;
});

const overtimeRate = computed(() => {
  return props.profile.base_hourly_rate * props.profile.overtime_rate_multiplier;
});

const displayFields = computed(() => {
  return [
    { key: 'employee_id', label: 'Employee ID', value: props.profile.employee_id || 'Auto-generated' },
    { key: 'base_hourly_rate', label: 'Base Hourly Rate', value: formatCurrency(props.profile.base_hourly_rate) },
    { key: 'overtime_rate', label: 'Overtime Rate', value: formatCurrency(overtimeRate.value) },
    {
      key: 'standard_hours_per_day',
      label: 'Standard Hours per Day',
      value: `${props.profile.standard_hours_per_day}h`,
    },
    {
      key: 'standard_hours_per_week',
      label: 'Standard Hours per Week',
      value: `${props.profile.standard_hours_per_week}h`,
    },
    {
      key: 'payment_method',
      label: 'Payment Method',
      value: props.profile.payment_method?.replace('_', ' ').toUpperCase(),
    },
    { key: 'bank_name', label: 'Bank Name', value: props.profile.bank_name || 'N/A' },
    {
      key: 'bank_account_number',
      label: 'Account Number',
      value: props.profile.bank_account_number ? `****${props.profile.bank_account_number.slice(-4)}` : 'N/A',
    },
    { key: 'tax_id', label: 'Tax ID', value: props.profile.tax_id || 'N/A' },
    { key: 'hire_date', label: 'Hire Date', value: formatDate(props.profile.hire_date) },
    { key: 'termination_date', label: 'Termination Date', value: formatDate(props.profile.termination_date) },
    { key: 'employment_duration', label: 'Employment Duration', value: employmentDuration.value },
  ];
});
</script>

<template>
  <Head :title="`Profile: ${profile.user.name}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <div class="flex-shrink-0">
            <div
              class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center"
            >
              <span class="text-2xl font-bold text-white">
                {{ profile.user.name.charAt(0).toUpperCase() }}
              </span>
            </div>
          </div>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
              {{ profile.user.name }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ profile.user.email }}</p>
            <div class="flex items-center space-x-2 mt-1">
              <!-- Account Status -->
              <div :class="statusClass" class="px-2 py-1 rounded-full text-xs font-medium flex items-center gap-1">
                <span class="opacity-75">Account:</span>
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                </svg>
                <span>{{ profile.is_active ? 'Active' : 'Inactive' }}</span>
              </div>

              <!-- Employment Status -->
              <div
                :class="employmentStatus.class"
                class="px-2 py-1 rounded-full text-xs font-medium flex items-center gap-1"
              >
                <span class="opacity-75">Employment:</span>

                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                </svg>
                <span>{{ employmentStatus.text }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('employee-profiles.edit', profile.id)"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded transition-colors"
          >
            Edit Profile
          </Link>
          <Link
            :href="route('employee-profiles.index')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors"
          >
            Back to List
          </Link>
        </div>
      </div>
    </main>
  </header>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                <svg
                  class="w-5 h-5 text-blue-600 dark:text-blue-300"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  ></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Hours (This Month)</h3>
              <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {{ formatHours(stats.current_month_hours) }}h
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                <svg
                  class="w-5 h-5 text-green-600 dark:text-green-300"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                  ></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Earnings (This Month)</h3>
              <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {{ formatCurrency(stats.current_month_earnings) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                <svg
                  class="w-5 h-5 text-yellow-600 dark:text-yellow-300"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                  ></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Time Entries</h3>
              <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.pending_entries }}</p>
              <Link
                v-if="stats.pending_entries > 0"
                :href="route('time-entries.index', { user_id: profile.user_id, status: 'pending' })"
                class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline"
              >
                View pending entries →
              </Link>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                <svg
                  class="w-5 h-5 text-purple-600 dark:text-purple-300"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  ></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Hours</h3>
              <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {{ formatHours(stats.total_hours) }}h
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Quick Actions</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Manage this employee's data.</p>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <Link
              :href="route('time-entries.index', { user_id: profile.user_id })"
              class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
            >
              <div class="flex-shrink-0">
                <svg
                  class="w-6 h-6 text-blue-600 dark:text-blue-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  ></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">View Time Entries</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">{{ stats.total_hours }}h total</p>
              </div>
            </Link>

            <Link
              :href="route('payslips.index', { user_id: profile.user_id })"
              class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors"
            >
              <div class="flex-shrink-0">
                <svg
                  class="w-6 h-6 text-green-600 dark:text-green-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  ></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">View Payslips</p>
                <p class="text-xs text-green-600 dark:text-green-400">
                  {{ formatCurrency(stats.total_earnings) }} total
                </p>
              </div>
            </Link>

            <button
              @click="showCreateTimeEntryModal = true"
              class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors"
            >
              <div class="flex-shrink-0">
                <svg
                  class="w-6 h-6 text-purple-600 dark:text-purple-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-purple-900 dark:text-purple-100">Add Time Entry</p>
                <p class="text-xs text-purple-600 dark:text-purple-400">Log new hours</p>
              </div>
            </button>
            <CreateTimeEntryModal :show="showCreateTimeEntryModal" v-on:close="showCreateTimeEntryModal = false" :user-id="profile.user_id" />

            <button
              v-if="profile.is_active"
              class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
              @click="$inertia.patch(route('employee-profiles.deactivate', profile.id))"
            >
              <div class="flex-shrink-0">
                <svg
                  class="w-6 h-6 text-red-600 dark:text-red-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"
                  ></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-red-900 dark:text-red-100">Deactivate</p>
                <p class="text-xs text-red-600 dark:text-red-400">Suspend profile</p>
              </div>
            </button>

            <button
              v-else
              class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors"
              @click="$inertia.patch(route('employee-profiles.activate', profile.id))"
            >
              <div class="flex-shrink-0">
                <svg
                  class="w-6 h-6 text-green-600 dark:text-green-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  ></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">Activate</p>
                <p class="text-xs text-green-600 dark:text-green-400">Restore profile</p>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Profile Information -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Profile Information</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Personal and payroll details.</p>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700">
          <dl class="grid grid-cols-1 md:grid-cols-2">
            <div
              v-for="field in displayFields"
              :key="field.key"
              class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700/50 odd:bg-white dark:odd:bg-gray-800"
            >
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ field.label }}
              </dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                {{ field.value }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Recent Activity (if available) -->
      <div
        v-if="profile.timeEntries && profile.timeEntries.length > 0"
        class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg"
      >
        <div class="px-4 py-5 sm:px-6">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Recent Time Entries</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Latest 10 time entries.</p>
            </div>
            <Link
              :href="route('time-entries.index', { user_id: profile.user_id })"
              class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
              View all →
            </Link>
          </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700">
          <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            <li
              v-for="entry in profile.timeEntries"
              :key="entry.id"
              class="px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="route('tasks.show', { project: entry.project.slug, task: entry.task.id })"
                      class="text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      {{ entry.task.name }}
                    </Link>
                    <span
                      :class="
                        entry.is_approved
                          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
                      "
                      class="px-2 py-1 rounded-full text-xs font-medium"
                    >
                      {{ entry.is_approved ? 'Approved' : 'Pending' }}
                    </span>
                  </div>
                  <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ formatDateTime(entry.start_datetime) }} - {{ formatDateTime(entry.end_datetime) }}
                  </div>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ formatHours(entry.hours_worked) }}h
                  </div>
                  <div class="text-sm font-medium text-green-600 dark:text-green-400">
                    {{ formatCurrency(entry.gross_amount) }}
                  </div>
                  <Link
                    :href="route('time-entries.show', entry.id)"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                  >
                    View →
                  </Link>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
