<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';
import CreateTimeEntryModal from '../TimeEntries/CreateTimeEntryModal.vue';
import CreateTaskModal from './CreateTaskModal.vue';
import TaskModal from './TaskModal.vue';

const showModal = ref(false);
const showCreateTimeEntryModal = ref(false);
const showTaskModal = ref(false);
const selectedTask = ref(null);
const selectedTaskForModal = ref(null);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const openTimeEntryModal = (task) => {
  selectedTask.value = task;
  showCreateTimeEntryModal.value = true;
};

const closeTimeEntryModal = () => {
  selectedTask.value = null;
  showCreateTimeEntryModal.value = false;
};

const openTaskDetailsModal = async (task) => {
  try {
    // Fetch full task details with attachments
    const response = await axios.get(
      route('tasks.show', {
        project: task.project.slug,
        task: task.id,
      })
    );
    selectedTaskForModal.value = response.data.props.task;
    showTaskModal.value = true;
  } catch (error) {
    console.error('Error fetching task details:', error);
    // Fallback to using the task data we have
    selectedTaskForModal.value = task;
    showTaskModal.value = true;
  }
};

const closeTaskModal = () => {
  showTaskModal.value = false;
  selectedTaskForModal.value = null;
};

const refreshTaskDetails = async () => {
  if (selectedTaskForModal.value) {
    try {
      const response = await axios.get(
        route('tasks.show', {
          project: selectedTaskForModal.value.project.slug,
          task: selectedTaskForModal.value.id,
        })
      );
      selectedTaskForModal.value = response.data.props.task;
    } catch (error) {
      console.error('Error refreshing task details:', error);
    }
  }
};

const props = defineProps({
  users: Array,
  tasks: Object,
  project: Object,
  projects: Array,
  statuses: Array,
  priorities: Array,
  filters: Object,
});

// Column configuration for DataTable
const columns = [
  {
    key: 'name',
    label: 'Task',
    sortable: true,
  },
  {
    key: 'description',
    label: 'Description',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">
        ${item.description || 'No description'}
      </div>
    `,
  },
  {
    key: 'project',
    exportKey: 'project.name',
    label: 'Project',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-blue-600 dark:text-blue-400">
        ${item.project?.name || 'N/A'}
      </div>
    `,
  },
  {
    key: 'status',
    exportKey: 'status.name',
    label: 'Status',
    sortable: true,
    component: (item) => `
      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="background-color: ${item.status?.color}20; color: ${item.status?.color}">
        ${item.status?.name || 'No Status'}
      </span>
    `,
  },
  {
    key: 'priority',
    exportKey: 'priority.name',
    label: 'Priority',
    sortable: true,
    component: (item) => `
      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="background-color: ${item.priority?.color}20; color: ${item.priority?.color}">
        ${item.priority?.name || 'No Priority'}
      </span>
    `,
  },
  {
    key: 'assigned_to',
    label: 'Assigned To',
    sortable: true,
    component: (item) => {
      if (!item.assigned_to || item.assigned_to.length === 0) {
        return '<span class="text-gray-500 dark:text-gray-400 text-sm">Unassigned</span>';
      }
      const names = item.assigned_to.map((user) => user.name).join(', ');
      return `<div class="text-sm text-gray-900 dark:text-gray-100">${names}</div>`;
    },
  },
  {
    key: 'created_at',
    label: 'Created',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-500 dark:text-gray-400">
        ${new Date(item.created_at).toLocaleDateString()}
      </div>
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
  // {
  //   key: 'search',
  //   label: 'Search',
  //   type: 'text',
  //   placeholder: 'Search by name or description...',
  // },
  {
    key: 'status',
    label: 'Status',
    type: 'vue-select',
    options: props.statuses,
    reduce: (status) => status.id,
    optionLabel: 'name',
    placeholder: 'Select Status',
    multiple: true,
  },
  {
    key: 'priority',
    label: 'Priority',
    type: 'vue-select',
    options: props.priorities,
    reduce: (priority) => priority.id,
    optionLabel: 'name',
    placeholder: 'Select Priority',
    multiple: true,
  },
  {
    key: 'assigned_to',
    label: 'Assigned To',
    type: 'vue-select',
    options: props.users,
    reduce: (user) => user.id,
    optionLabel: 'name',
    placeholder: 'Select Users',
    multiple: true,
  },
  ...(props.project
    ? []
    : [
        {
          key: 'projects',
          label: 'Projects',
          type: 'vue-select',
          options: props.projects,
          reduce: (project) => project.id,
          optionLabel: 'name',
          placeholder: 'Select Projects',
          multiple: true,
        },
      ]),
  {
    key: 'assigned_to_me',
    label: 'Assigned to Me',
    type: 'checkbox',
  },
];

// Bulk actions configuration
const bulkActions = [
  {
    value: 'update_status',
    label: 'Update Status',
    fields: [
      {
        name: 'status_id',
        label: 'Status',
        type: 'select',
        options: props.statuses.map((status) => ({ value: status.id, label: status.name })),
        required: true,
      },
    ],
  },
  {
    value: 'update_priority',
    label: 'Update Priority',
    fields: [
      {
        name: 'priority_id',
        label: 'Priority',
        type: 'select',
        options: props.priorities.map((priority) => ({ value: priority.id, label: priority.name })),
        required: true,
      },
    ],
  },
  {
    value: 'assign_users',
    label: 'Assign Users',
    fields: [
      {
        name: 'user_ids',
        label: 'Users',
        type: 'multiselect',
        options: props.users.map((user) => ({ value: user.id, label: user.name })),
        placeholder: 'Select users to assign',
        required: true,
      },
    ],
  },
  {
    value: 'delete',
    label: 'Delete Tasks',
  },
];

// Get appropriate route name based on context
const routeName = computed(() => {
  return props.project ? 'tasks.index' : 'tasks.all';
});

// Get route parameters
const routeParams = computed(() => {
  return props.project ? { project: props.project.slug } : {};
});

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    task_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('tasks.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle row clicks
const handleRowClick = (task) => {
  const project = props.project || task.project;
  window.location.href = route('tasks.show', {
    project: project.slug,
    task: task.id,
  });
};

// Format datetime for display
const formatDateTime = (datetime) => {
  if (!datetime) return 'Not set';
  return new Date(datetime).toLocaleString('de-DE', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<template>
  <Head title="Tasks" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          <div v-if="project">
            {{ $t('words.tasks') }} for
            <Link
              :href="route('projects.show', { project: project.slug })"
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ project.name }}
            </Link>
          </div>
          <div v-else>{{ $t('words.all_tasks') }}</div>
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('words.create_task') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create Task Modal -->
      <CreateTaskModal
        :show="showModal"
        :users="users"
        :project="project"
        :projects="projects"
        :statuses="statuses"
        :priorities="priorities"
        @close="closeModal"
      />

      <!-- Task Details Modal -->
      <TaskModal
        :show="showTaskModal"
        :task="selectedTaskForModal"
        @close="closeTaskModal"
        @refresh="refreshTaskDetails"
      />

      <!-- Tasks DataTable -->
      <DataTable
        :data="tasks"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        :route-name="routeName"
        :route-params="routeParams"
        :search-placeholder="$t('words.search_tasks')"
        :empty-state-text="$t('words.no_tasks_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
        :except="['users', 'projects', 'statuses', 'priorities']"
      >
        <!-- Custom Task Name Cell with Description -->
        <template #cell-name="{ item }">
          <div @click.stop>
            <button
              @click="openTaskDetailsModal(item)"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-left"
              v-html="item.name"
            />
            <div v-if="item.start_datetime || item.end_datetime" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              <span v-if="item.start_datetime">{{ $t('words.start') }}: {{ formatDateTime(item.start_datetime) }}</span>
              <span v-if="item.start_datetime && item.end_datetime"> | </span>
              <span v-if="item.end_datetime">{{ $t('words.end') }}: {{ formatDateTime(item.end_datetime) }}</span>
            </div>
          </div>
        </template>
        <!-- Custom Project Cell (only for all tasks view) -->
        <template #cell-project="{ item }" v-if="!project">
          <div @click.stop>
            <Link
              :href="route('projects.show', { project: item.project.slug })"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
              {{ item.project.name }}
            </Link>
          </div>
        </template>

        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="
                route('tasks.show', {
                  project: project ? project.slug : item.project.slug,
                  task: item.id,
                })
              "
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              {{ $t('words.view') }}
            </Link>
            <Link
              :href="
                route('tasks.edit', {
                  project: project ? project.slug : item.project.slug,
                  task: item.id,
                })
              "
              class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
            >
              {{ $t('words.edit') }}
            </Link>
            <button
              @click="
                () => {
                  openTimeEntryModal(item);
                }
              "
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('words.log_time') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
    <CreateTimeEntryModal
      :show="showCreateTimeEntryModal"
      :projects="projects"
      :task="selectedTask"
      @close="closeTimeEntryModal"
    />
  </div>
</template>
