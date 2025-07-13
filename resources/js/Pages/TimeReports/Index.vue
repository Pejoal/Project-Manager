<template>
  <AppLayout title="Time Reports">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Time Reports</h2>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Generate Report Section -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Generate New Report</h3>
              <button
                @click="showGenerateModal = true"
                class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Generate Report
              </button>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Create detailed time reports for specific projects and date ranges to analyze productivity and track
              progress.
            </p>
          </div>
        </div>

        <!-- Quick Reports -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quick Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <button
                @click="generateWeeklyReport"
                class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-left transition-colors"
              >
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    ></path>
                  </svg>
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">This Week</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Current week summary</p>
                  </div>
                </div>
              </button>

              <button
                @click="generateMonthlyReport"
                class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-left transition-colors"
              >
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    ></path>
                  </svg>
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">This Month</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Current month summary</p>
                  </div>
                </div>
              </button>

              <button
                @click="getDashboardSummary"
                class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-left transition-colors"
              >
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                    ></path>
                  </svg>
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Dashboard</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last 30 days overview</p>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project</label>
                <select
                  v-model="filters.project_id"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">All Projects</option>
                  <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Period Type</label>
                <select
                  v-model="filters.period_type"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">All Types</option>
                  <option v-for="(label, key) in periodTypes" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <input
                  type="date"
                  v-model="filters.date_from"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <input
                  type="date"
                  v-model="filters.date_to"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
            </div>
            <div class="mt-4 flex justify-end">
              <button
                @click="applyFilters"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors mr-2"
              >
                Apply Filters
              </button>
              <button
                @click="clearFilters"
                class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Reports List -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Generated Reports</h3>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Project
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Period
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Type
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Total Hours
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Generated
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr
                    v-for="report in timeReports.data"
                    :key="report.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ report.project.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDateRange(report.start_date, report.end_date) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200"
                      >
                        {{ periodTypes[report.period_type] }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ report.total_hours }}h</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(report.created_at) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        @click="viewReport(report)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2"
                      >
                        View
                      </button>
                      <button
                        @click="exportReport(report)"
                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-2"
                      >
                        Export
                      </button>
                      <button
                        @click="deleteReport(report)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                  <tr v-if="timeReports.data.length === 0">
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                      No reports generated yet. Create your first report using the "Generate Report" button above.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center" v-if="timeReports.data.length > 0">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing {{ timeReports.from }} to {{ timeReports.to }} of {{ timeReports.total }} results
              </div>
              <div class="flex space-x-2">
                <button
                  v-for="link in timeReports.links"
                  :key="link.label"
                  @click="changePage(link.url)"
                  :disabled="!link.url"
                  :class="[
                    'px-3 py-1 text-sm border rounded transition-colors',
                    link.active
                      ? 'bg-blue-500 text-white border-blue-500 dark:bg-blue-600'
                      : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
                  ]"
                  v-html="link.label"
                ></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Generate Report Modal -->
    <div
      v-if="showGenerateModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50"
    >
      <div
        class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-600 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800"
      >
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Generate Time Report</h3>
          <form @submit.prevent="generateReport">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project</label>
              <select
                v-model="generateForm.project_id"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Select a project</option>
                <option v-for="project in projects" :key="project.id" :value="project.id">
                  {{ project.name }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Period Type</label>
              <select
                v-model="generateForm.period_type"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Select period type</option>
                <option v-for="(label, key) in periodTypes" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
              <input
                type="date"
                v-model="generateForm.start_date"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
              <input
                type="date"
                v-model="generateForm.end_date"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="showGenerateModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-green-500 dark:bg-green-600 rounded-md hover:bg-green-600 dark:hover:bg-green-700 transition-colors"
              >
                Generate Report
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  timeReports: Object,
  projects: Array,
  periodTypes: Object,
  filters: Object,
});

const showGenerateModal = ref(false);

const generateForm = reactive({
  project_id: '',
  period_type: '',
  start_date: '',
  end_date: '',
});

const filters = reactive({
  project_id: props.filters.project_id || '',
  period_type: props.filters.period_type || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString();
};

const formatDateRange = (startDate, endDate) => {
  const start = new Date(startDate).toLocaleDateString();
  const end = new Date(endDate).toLocaleDateString();
  return `${start} - ${end}`;
};

const generateReport = () => {
  router.post(route('time-reports.generate'), generateForm, {
    onSuccess: () => {
      showGenerateModal.value = false;
      Object.keys(generateForm).forEach((key) => {
        generateForm[key] = '';
      });
    },
  });
};

const generateWeeklyReport = () => {
  // Implementation for quick weekly report
  console.log('Generate weekly report');
};

const generateMonthlyReport = () => {
  // Implementation for quick monthly report
  console.log('Generate monthly report');
};

const getDashboardSummary = () => {
  // Implementation for dashboard summary
  router.get(route('time-reports.dashboard-summary'));
};

const viewReport = (report) => {
  router.visit(route('time-reports.show', report.id));
};

const exportReport = (report) => {
  // Implementation for exporting report
  router.post(route('time-reports.export', report.id), { format: 'csv' });
};

const deleteReport = (report) => {
  if (confirm('Are you sure you want to delete this time report?')) {
    router.delete(route('time-reports.destroy', report.id));
  }
};

const applyFilters = () => {
  router.get(route('time-reports.index'), filters, {
    preserveState: true,
  });
};

const clearFilters = () => {
  Object.keys(filters).forEach((key) => {
    filters[key] = '';
  });
  applyFilters();
};

const changePage = (url) => {
  if (url) {
    router.visit(url);
  }
};
</script>
