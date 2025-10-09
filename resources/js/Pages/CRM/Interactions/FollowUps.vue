<script setup>
import DataTable from '@/Components/DataTable.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed } from 'vue';

const props = defineProps({
  followUps: Object,
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
      return `
        <div class="flex items-center space-x-2">
          <span class="text-lg">${typeIcons[item.type] || '📄'}</span>
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100 capitalize">
            ${item.type.replace('_', ' ')}
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
        (item.interactable.first_name && item.interactable.last_name
          ? `${item.interactable.first_name} ${item.interactable.last_name}`
          : '') ||
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
    key: 'follow_up_date',
    label: 'Follow-up Date',
    sortable: true,
    component: (item) => {
      const isOverdue = new Date(item.follow_up_date) < new Date();
      const colorClass = isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100';
      return `
        <div class="${colorClass}">
          <div class="text-sm font-medium">
            ${new Date(item.follow_up_date).toLocaleDateString()}
          </div>
          <div class="text-sm">
            ${new Date(item.follow_up_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
          </div>
          ${isOverdue ? '<div class="text-xs text-red-500 font-medium">OVERDUE</div>' : ''}
        </div>
      `;
    },
  },
  {
    key: 'user',
    exportKey: 'user.name',
    label: 'Assigned To',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.user?.name || 'Unknown'}
      </div>
    `,
  },
  {
    key: 'follow_up_notes',
    label: 'Notes',
    component: (item) => `
      <div class="text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
        ${item.follow_up_notes || 'No notes'}
      </div>
    `,
  },
  {
    key: 'status',
    label: 'Status',
    component: (item) => {
      const isOverdue = new Date(item.follow_up_date) < new Date();
      const colorClass = isOverdue
        ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
      const statusText = isOverdue ? 'Overdue' : 'Pending';
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${colorClass}">
          ${statusText}
        </span>
      `;
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
    key: 'overdue',
    label: 'Show Only Overdue',
    type: 'checkbox',
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
];

// Bulk actions configuration
const bulkActions = [
  {
    value: 'mark_follow_up_complete',
    label: 'Mark Follow-ups Complete',
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

// Mark individual follow-up complete
const markFollowUpComplete = async (interaction) => {
  try {
    await axios.post(route('interactions.mark-follow-up-complete', interaction.id));
    // Reload the page to get updated data
    window.location.reload();
  } catch (error) {
    console.error('Error marking follow-up complete:', error);
    alert('Failed to mark follow-up as complete');
  }
};

// Get priority based on overdue status
const getPriority = (followUpDate) => {
  const now = new Date();
  const followUp = new Date(followUpDate);
  const diffHours = (now - followUp) / (1000 * 60 * 60);

  if (diffHours > 48) return { level: 'high', text: 'High Priority', color: 'bg-red-500' };
  if (diffHours > 24) return { level: 'medium', text: 'Medium Priority', color: 'bg-yellow-500' };
  if (diffHours > 0) return { level: 'low', text: 'Low Priority', color: 'bg-orange-500' };
  return { level: 'pending', text: 'Pending', color: 'bg-blue-500' };
};

// Computed stats
const stats = computed(() => {
  const total = props.followUps.total || 0;
  const overdue = props.followUps.data?.filter((item) => new Date(item.follow_up_date) < new Date()).length || 0;
  const today =
    props.followUps.data?.filter((item) => {
      const followUpDate = new Date(item.follow_up_date);
      const today = new Date();
      return followUpDate.toDateString() === today.toDateString();
    }).length || 0;

  return { total, overdue, today };
});
</script>

<template>
  <Head title="Follow-ups" />

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
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Follow-ups</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage pending interaction follow-ups</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('interactions.analytics')"
                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded"
              >
                Analytics
              </Link>
              <Link
                :href="route('interactions.index')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                All Interactions
              </Link>
            </div>
          </div>
        </div>

        <!-- Stats Summary -->
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Follow-ups -->
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="bg-blue-500 p-3 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                      />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <dt class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Follow-ups</dt>
                  <dd class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                    {{ stats.total }}
                  </dd>
                </div>
              </div>
            </div>

            <!-- Overdue -->
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="bg-red-500 p-3 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <dt class="text-sm font-medium text-red-700 dark:text-red-300">Overdue</dt>
                  <dd class="text-2xl font-semibold text-red-900 dark:text-red-100">
                    {{ stats.overdue }}
                  </dd>
                </div>
              </div>
            </div>

            <!-- Due Today -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="bg-yellow-500 p-3 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <dt class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Due Today</dt>
                  <dd class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">
                    {{ stats.today }}
                  </dd>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Follow-ups DataTable -->
      <DataTable
        :data="followUps"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="interactions.follow-ups"
        :route-params="{}"
        :search-placeholder="$t('crm.search_follow_ups')"
        :empty-state-text="$t('crm.no_follow_ups')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
        :except="['users']"
      >
        <!-- Custom Subject with Priority Badge -->
        <template #cell-subject="{ item }">
          <div @click.stop class="max-w-xs">
            <div class="flex items-center space-x-2">
              <button
                @click="handleRowClick(item)"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-left"
              >
                {{ item.subject || 'No subject' }}
              </button>
              <span
                v-if="new Date(item.follow_up_date) < new Date()"
                :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  getPriority(item.follow_up_date).color,
                  'text-white',
                ]"
              >
                {{ getPriority(item.follow_up_date).text }}
              </span>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">
              {{
                item.description ? item.description.substring(0, 60) + (item.description.length > 60 ? '...' : '') : ''
              }}
            </div>
          </div>
        </template>

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
              @click="markFollowUpComplete(item)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.complete') }}
            </button>
          </div>
        </template>
      </DataTable>

      <!-- Quick Actions -->
      <div
        v-if="stats.overdue > 0"
        class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"
              />
            </svg>
            <div>
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Attention Required</h3>
              <p class="text-sm text-red-700 dark:text-red-300">
                You have {{ stats.overdue }} overdue follow-up{{ stats.overdue > 1 ? 's' : '' }} that need{{
                  stats.overdue === 1 ? 's' : ''
                }}
                immediate attention.
              </p>
            </div>
          </div>
          <div class="flex space-x-2">
            <Link
              :href="route('interactions.follow-ups', { overdue: true })"
              class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm"
            >
              View Overdue
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
