<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateTimeEntryModal from './CreateTimeEntryModal.vue';

const props = defineProps({
  timeEntries: Object,
  filters: Object,
  users: Array,
  projects: Array,
  canManageAll: Boolean,
});

const showModal = ref(false);
const editingTimeEntry = ref(null);

const openModal = () => {
  editingTimeEntry.value = null;
  showModal.value = true;
};

const openEditModal = (timeEntry) => {
  editingTimeEntry.value = timeEntry;
  showModal.value = true;
};

const closeModal = () => {
  editingTimeEntry.value = null;
  showModal.value = false;
};

// Column configuration for DataTable
const columns = [
  {
    key: 'user',
    label: 'User',
    sortable: false,
    component: (item) => `
      <div class="flex items-center">
        <div class="flex-shrink-0 h-8 w-8">
          <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
              ${item.user.name.charAt(0).toUpperCase()}
            </span>
          </div>
        </div>
        <div class="ml-3">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
            ${item.user.name}
          </div>
        </div>
      </div>
    `,
  },
  {
    key: 'task',
    label: 'Task',
    component: (item) => {
      if (!item.task) {
        return '<span class="text-gray-500 dark:text-gray-400 text-sm">N/A</span>';
      }
      return `
        <div class="text-sm text-blue-600 dark:text-blue-400">
          ${item.task.name}
        </div>
      `;
    },
  },
  {
    key: 'project',
    label: 'Project',
    component: (item) => `
      <div class="text-sm text-blue-600 dark:text-blue-400">
        ${item.project.name}
      </div>
    `,
  },
  {
    key: 'start_datetime',
    label: 'Start Time',
    sortable: true,
    component: (item) => formatDateTime(item.start_datetime),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'end_datetime',
    label: 'End Time',
    sortable: true,
    component: (item) => formatDateTime(item.end_datetime),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'hours_worked',
    label: 'Hours',
    sortable: true,
    component: (item) => `${formatHours(item.hours_worked)}h`,
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'gross_amount',
    label: 'Amount',
    sortable: true,
    component: (item) => formatCurrency(item.gross_amount),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'is_approved',
    label: 'Status',
    component: (item) => `
      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
        item.is_approved
          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
      }">
        ${item.is_approved ? 'Approved' : 'Pending'}
      </span>
    `,
  },
  {
    key: 'actions',
    label: 'Actions',
    component: () => '', // Will use slot
  },
];

// Filter configuration
const filterConfig = [
  ...(props.canManageAll
    ? [
        {
          key: 'user_id',
          label: 'User',
          type: 'vue-select',
          options: props.users,
          reduce: (user) => user.id,
          optionLabel: 'name',
          placeholder: 'All Users',
        },
      ]
    : []),
  {
    key: 'project_id',
    label: 'Project',
    type: 'vue-select',
    options: props.projects,
    reduce: (project) => project.id,
    optionLabel: 'name',
    placeholder: 'All Projects',
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select',
    options: [
      { value: 'pending', label: 'Pending' },
      { value: 'approved', label: 'Approved' },
    ],
    placeholder: 'All Status',
  },
  {
    key: 'date_from',
    label: 'Date From',
    type: 'date',
  },
  {
    key: 'date_to',
    label: 'Date To',
    type: 'date',
  },
];

// Bulk actions configuration (only for admins)
const bulkActions = props.canManageAll
  ? [
      {
        value: 'approve',
        label: 'Approve Entries',
      },
      {
        value: 'reject',
        label: 'Reject Entries',
      },
      {
        value: 'delete',
        label: 'Delete Entries',
      },
    ]
  : [];

// Formatting functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount || 0);
};

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  return new Date(datetime).toLocaleString('de-DE', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    time_entry_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('time-entries.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle individual actions
const approveEntry = (entryId) => {
  if (confirm('Are you sure you want to approve this time entry?')) {
    useForm().patch(route('time-entries.approve', entryId), {
      preserveState: true,
    });
  }
};

// Handle row clicks
const handleRowClick = (entry) => {
  window.location.href = route('time-entries.show', entry.id);
};
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
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create/Edit Time Entry Modal -->
      <CreateTimeEntryModal :show="showModal" :projects="projects" :timeEntry="editingTimeEntry" @close="closeModal" />

      <!-- Time Entries DataTable -->
      <DataTable
        :data="timeEntries"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="canManageAll"
        route-name="time-entries.index"
        :search-placeholder="$t('payroll.filters.search')"
        :empty-state-text="'No time entries found.'"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Task Cell -->
        <template #cell-task="{ item }">
          <div v-if="item.task" class="text-sm">
            <Link
              :href="route('tasks.show', { project: item.project.slug, task: item.task.id })"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
              {{ item.task.name }}
            </Link>
          </div>
          <span v-else class="text-gray-500 dark:text-gray-400 text-sm">N/A</span>
        </template>

        <!-- Custom Project Cell -->
        <template #cell-project="{ item }">
          <Link
            :href="route('projects.show', { project: item.project.slug })"
            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
          >
            {{ item.project.name }}
          </Link>
        </template>

        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('time-entries.show', item.id)"
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              {{ $t('words.view') }}
            </Link>
            <button
              v-if="!item.is_approved && (canManageAll || item.user_id === $page.props.auth.user.id)"
              @click="openEditModal(item)"
              class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
            >
              {{ $t('words.edit') }}
            </button>
            <button
              v-if="canManageAll && !item.is_approved"
              @click="approveEntry(item.id)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('payroll.time_entries.approve') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
