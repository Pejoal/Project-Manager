<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import vSelect from 'vue-select';
import CreateTimeEntryModal from './CreateTimeEntryModal.vue';

const props = defineProps({
  timeEntries: Object,
  filters: Object,
  users: Array,
  projects: Array,
  canManageAll: Boolean,
});

const showModal = ref(false);
const selectedEntries = ref([]);
const filtersVisible = ref(true);

const form = useForm({
  user_id: props.filters.user_id || '',
  project_id: props.filters.project_id || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

const bulkForm = useForm({
  time_entry_ids: [],
});

watch(
  () => [form.user_id, form.project_id, form.status, form.date_from, form.date_to],
  () => {
    applyFilters();
  },
  { deep: true }
);

const applyFilters = () => {
  const params = {
    user_id: form.user_id,
    project_id: form.project_id,
    status: form.status,
    date_from: form.date_from,
    date_to: form.date_to,
  };

  form.get(route('time-entries.index'), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    data: params,
  });
};

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount || 0);
};

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString();
};

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

const approveEntry = (entryId) => {
  if (confirm('Are you sure you want to approve this time entry?')) {
    useForm().patch(route('time-entries.approve', entryId), {
      preserveState: true,
    });
  }
};

const toggleEntrySelection = (entryId) => {
  const index = selectedEntries.value.indexOf(entryId);
  if (index > -1) {
    selectedEntries.value.splice(index, 1);
  } else {
    selectedEntries.value.push(entryId);
  }
};

const toggleSelectAll = () => {
  if (selectedEntries.value.length === props.timeEntries.data.length) {
    selectedEntries.value = [];
  } else {
    selectedEntries.value = props.timeEntries.data.map((entry) => entry.id);
  }
};

const bulkApprove = () => {
  if (selectedEntries.value.length === 0) {
    alert('Please select time entries to approve');
    return;
  }

  if (confirm(`Are you sure you want to approve ${selectedEntries.value.length} time entries?`)) {
    bulkForm.time_entry_ids = selectedEntries.value;
    bulkForm.post(route('time-entries.bulk-approve'), {
      preserveState: true,
      onSuccess: () => {
        selectedEntries.value = [];
      },
    });
  }
};

const pagination = computed(() => {
  return {
    prev_page_url: props.timeEntries.prev_page_url,
    current_page: props.timeEntries.current_page,
    last_page: props.timeEntries.last_page,
    total: props.timeEntries.total,
    next_page_url: props.timeEntries.next_page_url,
  };
});
</script>

<template>
  <Head title="Time Entries" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('payroll.time_entries.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('payroll.time_entries.create') }}
          </PrimaryButton>
          <PrimaryButton
            v-if="canManageAll && selectedEntries.length > 0"
            @click="bulkApprove"
            :disabled="bulkForm.processing"
            class="bg-green-500 hover:bg-green-700"
          >
            {{ $t('payroll.time_entries.bulk_approve') }} ({{ selectedEntries.length }})
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <CreateTimeEntryModal 
        :show="showModal" 
        :projects="projects"
        @close="closeModal" 
      />


      <!-- Filters Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <PrimaryButton @click="toggleFilters">
            {{ filtersVisible ? 'Hide Filters' : 'Show Filters' }}
          </PrimaryButton>
        </div>

        <transition name="slide-down">
          <div v-if="filtersVisible" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
              <!-- User Filter -->
              <div v-if="canManageAll">
                <InputLabel for="user_filter" value="User" />
                <vSelect
                  id="user_filter"
                  v-model="form.user_id"
                  :options="users"
                  :reduce="(user) => user.id"
                  label="name"
                  placeholder="All Users"
                  class="text-gray-700"
                />
              </div>

              <!-- Project Filter -->
              <div>
                <InputLabel for="project_filter" value="Project" />
                <vSelect
                  id="project_filter"
                  v-model="form.project_id"
                  :options="projects"
                  :reduce="(project) => project.id"
                  label="name"
                  placeholder="All Projects"
                  class="text-gray-700"
                />
              </div>

              <!-- Status Filter -->
              <div>
                <InputLabel for="status_filter" value="Status" />
                <select
                  id="status_filter"
                  v-model="form.status"
                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                >
                  <option value="">All Status</option>
                  <option value="pending">{{ $t('payroll.time_entries.pending') }}</option>
                  <option value="approved">{{ $t('payroll.time_entries.approved') }}</option>
                </select>
              </div>

              <!-- Date From -->
              <div>
                <InputLabel for="date_from" value="Date From" />
                <input
                  id="date_from"
                  v-model="form.date_from"
                  type="date"
                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                />
              </div>

              <!-- Date To -->
              <div>
                <InputLabel for="date_to" value="Date To" />
                <input
                  id="date_to"
                  v-model="form.date_to"
                  type="date"
                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                />
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Time Entries Table -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  v-if="canManageAll"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  <Checkbox
                    :checked="selectedEntries.length === timeEntries.data.length && timeEntries.data.length > 0"
                    @change="toggleSelectAll"
                  />
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.time_entries.user') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('words.task') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('words.project') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.time_entries.start_datetime') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.time_entries.end_datetime') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.time_entries.hours_worked') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.time_entries.gross_amount') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.time_entries.status') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('words.actions_column') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="entry in timeEntries.data" :key="entry.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td v-if="canManageAll" class="px-6 py-4 whitespace-nowrap">
                  <Checkbox :checked="selectedEntries.includes(entry.id)" @change="toggleEntrySelection(entry.id)" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                          {{ entry.user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ entry.user.name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-gray-100">
                    <Link
                      v-if="entry.task"
                      :href="route('tasks.show', { project: entry.project.slug, task: entry.task.id })"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      {{ entry.task.name }}
                    </Link>
                    <span v-else class="text-gray-500 dark:text-gray-400">N/A</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-gray-100">
                    <Link
                      :href="route('projects.show', { project: entry.project.slug })"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      {{ entry.project.name }}
                    </Link>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(entry.start_datetime) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(entry.end_datetime) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatHours(entry.hours_worked) }}h
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatCurrency(entry.gross_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="entry.is_approved"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100"
                  >
                    {{ $t('payroll.time_entries.approved') }}
                  </span>
                  <span
                    v-else
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100"
                  >
                    {{ $t('payroll.time_entries.pending') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <Link
                      :href="route('time-entries.show', entry.id)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      {{ $t('words.view') }}
                    </Link>
                    <Link
                      v-if="!entry.is_approved && (canManageAll || entry.user_id === $page.props.auth.user.id)"
                      :href="route('time-entries.edit', entry.id)"
                      class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                    >
                      {{ $t('words.edit') }}
                    </Link>
                    <button
                      v-if="canManageAll && !entry.is_approved"
                      @click="approveEntry(entry.id)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      {{ $t('payroll.time_entries.approve') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="timeEntries.data.length === 0">
                <td :colspan="canManageAll ? 10 : 9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  No time entries found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="pagination" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: max-height 0.3s ease-in-out;
}

.slide-down-enter-from,
.slide-down-leave-to {
  max-height: 0;
  overflow: hidden;
}

.slide-down-enter-to,
.slide-down-leave-from {
  max-height: 50rem;
  overflow: auto;
}
</style>
