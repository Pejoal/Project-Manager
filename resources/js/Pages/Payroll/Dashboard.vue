<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  stats: Object,
  recentTimeEntries: Array,
  pendingApprovals: Array,
  upcomingPayslips: Array,
  chartData: Object,
  canManagePayroll: Boolean,
});

const activeTab = ref('overview');

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount || 0);
};

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : 'N/A';
};

const formatDateTime = (datetime) => {
  return datetime ? new Date(datetime).toLocaleString() : 'N/A';
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
    approved: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
    paid: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
  };
  return colors[status] || colors.pending;
};

const totalPendingAmount = computed(() => {
  return props.pendingApprovals?.reduce((sum, item) => sum + parseFloat(item.gross_amount || 0), 0) || 0;
});

const totalUpcomingPayroll = computed(() => {
  return props.upcomingPayslips?.reduce((sum, item) => sum + parseFloat(item.net_pay || 0), 0) || 0;
});
</script>

<template>
  <Head title="Payroll Dashboard" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          {{ $t('payroll.dashboard.title') }}
        </h1>
        <div class="flex space-x-3">
          <Link
            :href="route('time-entries.create')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('payroll.time_entries.create') }}
          </Link>
          <Link
            v-if="canManagePayroll"
            :href="route('payslips.create')"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('payroll.payslips.generate') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Statistics Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Hours This Month -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $t('payroll.dashboard.total_hours_month') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ formatHours(stats?.total_hours_month) }}h
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $t('payroll.dashboard.pending_approvals') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ stats?.pending_approvals || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Payroll This Month -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $t('payroll.dashboard.total_payroll_month') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ formatCurrency(stats?.total_payroll_month) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Active Employees -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $t('payroll.dashboard.active_employees') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ stats?.active_employees || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button
              @click="activeTab = 'overview'"
              :class="[
                activeTab === 'overview'
                  ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              ]"
            >
              {{ $t('payroll.dashboard.overview') }}
            </button>
            <button
              @click="activeTab = 'recent'"
              :class="[
                activeTab === 'recent'
                  ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              ]"
            >
              {{ $t('payroll.dashboard.recent_activity') }}
            </button>
            <button
              v-if="canManagePayroll"
              @click="activeTab = 'pending'"
              :class="[
                activeTab === 'pending'
                  ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              ]"
            >
              {{ $t('payroll.dashboard.pending_approvals') }}
              <span
                v-if="pendingApprovals?.length > 0"
                class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs"
              >
                {{ pendingApprovals.length }}
              </span>
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-6">
            <!-- Quick Actions -->
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('payroll.dashboard.quick_actions') }}
              </h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link
                  :href="route('time-entries.index')"
                  class="relative group bg-white dark:bg-gray-700 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow"
                >
                  <div>
                    <span
                      class="rounded-lg inline-flex p-3 bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400 ring-4 ring-white dark:ring-gray-700"
                    >
                      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </span>
                  </div>
                  <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ $t('payroll.time_entries.title') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('payroll.dashboard.manage_time_entries') }}
                    </p>
                  </div>
                </Link>

                <Link
                  :href="route('employee-profiles.index')"
                  class="relative group bg-white dark:bg-gray-700 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow"
                >
                  <div>
                    <span
                      class="rounded-lg inline-flex p-3 bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 ring-4 ring-white dark:ring-gray-700"
                    >
                      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                      </svg>
                    </span>
                  </div>
                  <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ $t('payroll.employee_profiles.title') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('payroll.dashboard.manage_employees') }}
                    </p>
                  </div>
                </Link>

                <Link
                  :href="route('payslips.index')"
                  class="relative group bg-white dark:bg-gray-700 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow"
                >
                  <div>
                    <span
                      class="rounded-lg inline-flex p-3 bg-yellow-50 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 ring-4 ring-white dark:ring-gray-700"
                    >
                      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                      </svg>
                    </span>
                  </div>
                  <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ $t('payroll.payslips.title') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('payroll.dashboard.manage_payslips') }}
                    </p>
                  </div>
                </Link>

                <Link
                  :href="route('payroll.reports')"
                  class="relative group bg-white dark:bg-gray-700 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow"
                >
                  <div>
                    <span
                      class="rounded-lg inline-flex p-3 bg-purple-50 dark:bg-purple-900 text-purple-600 dark:text-purple-400 ring-4 ring-white dark:ring-gray-700"
                    >
                      <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                        />
                      </svg>
                    </span>
                  </div>
                  <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ $t('payroll.reports.title') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('payroll.dashboard.view_reports') }}
                    </p>
                  </div>
                </Link>
              </div>
            </div>

            <!-- Upcoming Payslips -->
            <div v-if="upcomingPayslips?.length > 0">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('payroll.dashboard.upcoming_payslips') }}
              </h3>
              <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    {{ upcomingPayslips.length }} payslips ready for processing
                  </span>
                  <span class="text-sm font-bold text-blue-900 dark:text-blue-100">
                    Total: {{ formatCurrency(totalUpcomingPayroll) }}
                  </span>
                </div>
                <div class="space-y-2">
                  <div
                    v-for="payslip in upcomingPayslips.slice(0, 3)"
                    :key="payslip.id"
                    class="flex justify-between text-sm"
                  >
                    <span class="text-blue-700 dark:text-blue-300">{{ payslip.user.name }}</span>
                    <span class="font-medium text-blue-900 dark:text-blue-100">{{
                      formatCurrency(payslip.net_pay)
                    }}</span>
                  </div>
                  <div v-if="upcomingPayslips.length > 3" class="text-xs text-blue-600 dark:text-blue-400">
                    +{{ upcomingPayslips.length - 3 }} more payslips
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activity Tab -->
          <div v-if="activeTab === 'recent'">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('payroll.dashboard.recent_time_entries') }}
            </h3>
            <div class="flow-root">
              <ul role="list" class="-mb-8">
                <li v-for="(entry, index) in recentTimeEntries" :key="entry.id">
                  <div class="relative pb-8">
                    <span
                      v-if="index !== recentTimeEntries.length - 1"
                      class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                      aria-hidden="true"
                    ></span>
                    <div class="relative flex space-x-3">
                      <div>
                        <span
                          :class="[
                            getStatusColor(entry.is_approved ? 'approved' : 'pending'),
                            'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800',
                          ]"
                        >
                          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                          </svg>
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ entry.user.name }}</span>
                            logged {{ formatHours(entry.hours_worked) }} hours
                            <span v-if="entry.task" class="text-blue-600 dark:text-blue-400"
                              >on {{ entry.task.name }}</span
                            >
                          </p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                          <time>{{ formatDateTime(entry.created_at) }}</time>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Pending Approvals Tab -->
          <div v-if="activeTab === 'pending' && canManagePayroll">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                {{ $t('payroll.dashboard.pending_approvals') }}
              </h3>
              <div v-if="pendingApprovals?.length > 0" class="text-sm text-gray-600 dark:text-gray-400">
                Total pending: {{ formatCurrency(totalPendingAmount) }}
              </div>
            </div>

            <div v-if="pendingApprovals?.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No pending approvals</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All time entries are up to date.</p>
            </div>

            <div v-else class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-md">
              <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                <li v-for="entry in pendingApprovals" :key="entry.id" class="px-6 py-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                          <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ entry.user.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                          {{ entry.user.name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ formatHours(entry.hours_worked) }}h - {{ formatCurrency(entry.gross_amount) }}
                          <span v-if="entry.task" class="ml-2">on {{ entry.task.name }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <Link
                        :href="route('time-entries.show', entry.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium"
                      >
                        View
                      </Link>
                      <button
                        @click="approveEntry(entry.id)"
                        class="bg-green-100 hover:bg-green-200 text-green-800 dark:bg-green-800 dark:hover:bg-green-700 dark:text-green-100 px-3 py-1 rounded-full text-sm font-medium"
                      >
                        Approve
                      </button>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    approveEntry(entryId) {
      if (confirm('Are you sure you want to approve this time entry?')) {
        this.$inertia.patch(
          route('time-entries.approve', entryId),
          {},
          {
            preserveState: true,
          }
        );
      }
    },
  },
};
</script>
