<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateInteractionModal from './CreateInteractionModal.vue';

const showModal = ref(false);

const props = defineProps({
  interactions: Object,
  users: Array,
  filters: Object,
});

// Column configuration for DataTable
const columns = [
  {
    key: 'type',
    label: 'Type',
    sortable: true,
    component: (item) => {
      const typeIcons = {
        call: '📞',
        email: '📧',
        meeting: '🤝',
        note: '📝',
        sms: '💬',
        social_media: '📱',
        webinar: '💻',
        demo: '🎥',
        other: '📄',
      };
      const directionColors = {
        inbound: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        outbound: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
      };
      return `
        <div class="flex items-center space-x-2">
          <span class="text-lg">${typeIcons[item.type] || '📄'}</span>
          <div>
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 capitalize">
              ${item.type.replace('_', ' ')}
            </div>
            <span class="inline-flex px-2 py-1 text-xs rounded-full ${directionColors[item.direction]}">
              ${item.direction}
            </span>
          </div>
        </div>
      `;
    },
  },
  {
    key: 'subject',
    label: 'Subject',
    sortable: true,
    component: (item) => `
      <div class="max-w-xs">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
          ${item.subject || 'No subject'}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
          ${item.description ? item.description.substring(0, 60) + (item.description.length > 60 ? '...' : '') : ''}
        </div>
      </div>
    `,
  },
  {
    key: 'interactable',
    label: 'Related To',
    component: (item) => {
      if (!item.interactable) return '<span class="text-gray-400">Unknown</span>';
      const name =
        item.interactable.name ||
        item.interactable.first_name + ' ' + item.interactable.last_name ||
        item.interactable.subject ||
        item.interactable.title ||
        `#${item.interactable.id}`;
      const type = item.interactable_type?.split('\\').pop() || 'Unknown';
      return `
        <div>
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
            ${name.trim()}
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            ${type}
          </div>
        </div>
      `;
    },
  },
  {
    key: 'interaction_date',
    label: 'Date',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${new Date(item.interaction_date).toLocaleDateString()}
      </div>
      <div class="text-sm text-gray-500 dark:text-gray-400">
        ${new Date(item.interaction_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
      </div>
    `,
  },
  {
    key: 'duration_minutes',
    label: 'Duration',
    component: (item) => {
      if (!item.duration_minutes) return '<span class="text-gray-400">-</span>';
      const hours = Math.floor(item.duration_minutes / 60);
      const minutes = item.duration_minutes % 60;
      const duration = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
      return `
        <div class="flex items-center text-sm text-gray-900 dark:text-gray-100">
          <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          ${duration}
        </div>
      `;
    },
  },
  {
    key: 'outcome',
    label: 'Outcome',
    component: (item) => {
      if (!item.outcome) return '<span class="text-gray-400">-</span>';
      const outcomeColors = {
        positive: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        neutral: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        negative: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
      };
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${outcomeColors[item.outcome]}">
          ${item.outcome}
        </span>
      `;
    },
  },
  {
    key: 'user',
    exportKey: 'user.name',
    label: 'User',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.user?.name || 'Unknown'}
      </div>
    `,
  },
  {
    key: 'follow_up_status',
    label: 'Follow-up',
    component: (item) => {
      if (!item.follow_up_required) return '<span class="text-gray-400">None</span>';
      if (item.follow_up_completed_at) {
        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>';
      }
      if (item.follow_up_date) {
        const isOverdue = new Date(item.follow_up_date) < new Date();
        const colorClass = isOverdue
          ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        return `
          <div>
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${colorClass}">
              ${isOverdue ? 'Overdue' : 'Pending'}
            </span>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              ${new Date(item.follow_up_date).toLocaleDateString()}
            </div>
          </div>
        `;
      }
      return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Required</span>';
    },
  },
  {
    key: 'actions',
    label: 'Actions',
    component: () => '', // Will use slot
  },
];

// Filter configuration
const filterConfig = [
  {
    key: 'type',
    label: 'Type',
    type: 'select',
    options: [
      { value: 'call', label: 'Call' },
      { value: 'email', label: 'Email' },
      { value: 'meeting', label: 'Meeting' },
      { value: 'note', label: 'Note' },
      { value: 'sms', label: 'SMS' },
      { value: 'social_media', label: 'Social Media' },
      { value: 'webinar', label: 'Webinar' },
      { value: 'demo', label: 'Demo' },
      { value: 'other', label: 'Other' },
    ],
    placeholder: 'All Types',
  },
  {
    key: 'direction',
    label: 'Direction',
    type: 'select',
    options: [
      { value: 'inbound', label: 'Inbound' },
      { value: 'outbound', label: 'Outbound' },
    ],
    placeholder: 'All Directions',
  },
  {
    key: 'user_id',
    label: 'User',
    type: 'vue-select',
    options: props.users,
    reduce: (user) => user.id,
    optionLabel: 'name',
    placeholder: 'Select User',
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

// Bulk actions configuration
const bulkActions = [
  {
    value: 'mark_follow_up_complete',
    label: 'Mark Follow-up Complete',
  },
  {
    value: 'delete',
    label: 'Delete Interactions',
  },
];

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    action: action,
    interaction_ids: items,
    ...data,
  });

  bulkForm.post(route('interactions.bulk-action'), {
    preserveState: true,
    onSuccess: () => {
      // Success handled by backend
    },
  });
};

// Handle row clicks
const handleRowClick = (interaction) => {
  window.location.href = route('interactions.show', interaction.id);
};

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};
</script>

<template>
  <Head title="Interactions" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            {{ $t('crm.interactions') }}
          </h1>
          <p class="text-sm text-gray-600 dark:text-gray-400">Track and manage all customer interactions</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('interactions.analytics')"
            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
            {{ $t('crm.analytics') }}
          </Link>
          <Link
            :href="route('interactions.follow-ups')"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            {{ $t('crm.follow_ups') }}
          </Link>
          <PrimaryButton @click="openModal">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ $t('crm.add_interaction') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create Interaction Modal -->
      <CreateInteractionModal :show="showModal" :users="users" @close="closeModal" />

      <!-- Interactions DataTable -->
      <DataTable
        :data="interactions"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="interactions.index"
        :route-params="{}"
        :search-placeholder="$t('crm.search_interactions')"
        :empty-state-text="$t('crm.no_interactions')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
        :except="['users']"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('interactions.show', item.id)"
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              {{ $t('words.view') }}
            </Link>
            <Link
              :href="route('interactions.edit', item.id)"
              class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
            >
              {{ $t('words.edit') }}
            </Link>
            <button
              v-if="item.follow_up_required && !item.follow_up_completed_at"
              @click="markFollowUpComplete(item)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.complete_follow_up') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
