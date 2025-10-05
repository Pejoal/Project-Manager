<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateSupportTicketModal from './CreateSupportTicketModal.vue';

const props = defineProps({
  supportTickets: Object,
  filters: Object,
  users: Array,
  contacts: Array,
});

const showModal = ref(false);
const editingTicket = ref(null);

const openModal = () => {
  editingTicket.value = null;
  showModal.value = true;
};

const openEditModal = (ticket) => {
  editingTicket.value = ticket;
  showModal.value = true;
};

const closeModal = () => {
  editingTicket.value = null;
  showModal.value = false;
};

// Column configuration for DataTable
const columns = [
  {
    key: 'ticket_info',
    label: 'Ticket',
    sortable: true,
    component: (item) => `
      <div>
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
          #${item.ticket_number}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400 truncate max-w-xs">
          ${item.subject}
        </div>
      </div>
    `,
  },
  {
    key: 'contact_info',
    label: 'Contact',
    sortable: false,
    component: (item) => `
      <div>
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
          ${item.contact ? item.contact.first_name + ' ' + item.contact.last_name : 'Unknown'}
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
          ${item.contact ? item.contact.email : 'No email'}
        </div>
      </div>
    `,
  },
  {
    key: 'status',
    label: 'Status',
    sortable: true,
    component: (item) => {
      const statusColors = {
        open: '#3B82F6',
        in_progress: '#F59E0B',
        pending_customer: '#8B5CF6',
        resolved: '#10B981',
        closed: '#6B7280'
      };
      const color = statusColors[item.status] || '#6B7280';
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
              style="background-color: ${color}">
          ${item.status.replace(/_/g, ' ').toUpperCase()}
        </span>
      `;
    },
  },
  {
    key: 'priority',
    label: 'Priority',
    sortable: true,
    component: (item) => {
      const priorityColors = {
        low: '#10B981',
        medium: '#F59E0B',
        high: '#EF4444',
        urgent: '#DC2626'
      };
      const color = priorityColors[item.priority] || '#6B7280';
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
              style="background-color: ${color}">
          ${item.priority.toUpperCase()}
        </span>
      `;
    },
  },
  {
    key: 'type',
    label: 'Type',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.type.replace(/_/g, ' ').toUpperCase()}
      </div>
    `,
  },
  {
    key: 'source',
    label: 'Source',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.source.replace(/_/g, ' ').toUpperCase()}
      </div>
    `,
  },
  {
    key: 'assigned_to',
    label: 'Assigned To',
    sortable: false,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.assigned_to ? item.assigned_to.name : 'Unassigned'}
      </div>
    `,
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
    key: 'satisfaction_rating',
    label: 'Rating',
    sortable: true,
    component: (item) => {
      if (!item.satisfaction_rating) {
        return '<span class="text-gray-400 text-sm">N/A</span>';
      }
      const stars = '★'.repeat(item.satisfaction_rating) + '☆'.repeat(5 - item.satisfaction_rating);
      return `
        <div class="text-sm text-yellow-500">
          ${stars}
        </div>
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
    key: 'status',
    label: 'Status',
    type: 'select',
    options: [
      { value: 'open', label: 'Open' },
      { value: 'in_progress', label: 'In Progress' },
      { value: 'pending_customer', label: 'Pending Customer' },
      { value: 'resolved', label: 'Resolved' },
      { value: 'closed', label: 'Closed' },
    ],
    placeholder: 'All Statuses',
  },
  {
    key: 'priority',
    label: 'Priority',
    type: 'select',
    options: [
      { value: 'low', label: 'Low' },
      { value: 'medium', label: 'Medium' },
      { value: 'high', label: 'High' },
      { value: 'urgent', label: 'Urgent' },
    ],
    placeholder: 'All Priorities',
  },
  {
    key: 'type',
    label: 'Type',
    type: 'select',
    options: [
      { value: 'bug', label: 'Bug' },
      { value: 'feature_request', label: 'Feature Request' },
      { value: 'question', label: 'Question' },
      { value: 'complaint', label: 'Complaint' },
      { value: 'other', label: 'Other' },
    ],
    placeholder: 'All Types',
  },
  {
    key: 'source',
    label: 'Source',
    type: 'select',
    options: [
      { value: 'email', label: 'Email' },
      { value: 'phone', label: 'Phone' },
      { value: 'chat', label: 'Chat' },
      { value: 'web_form', label: 'Web Form' },
      { value: 'social_media', label: 'Social Media' },
    ],
    placeholder: 'All Sources',
  },
  {
    key: 'assigned_to',
    label: 'Assigned To',
    type: 'vue-select',
    options: props.users,
    reduce: (user) => user.id,
    optionLabel: 'name',
    placeholder: 'All Users',
  },
];

// Bulk actions configuration
const bulkActions = [
  {
    value: 'update_status',
    label: 'Update Status',
    fields: [
      {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
          { value: 'open', label: 'Open' },
          { value: 'in_progress', label: 'In Progress' },
          { value: 'pending_customer', label: 'Pending Customer' },
          { value: 'resolved', label: 'Resolved' },
          { value: 'closed', label: 'Closed' },
        ],
        required: true,
      },
    ],
  },
  {
    value: 'update_priority',
    label: 'Update Priority',
    fields: [
      {
        name: 'priority',
        label: 'Priority',
        type: 'select',
        options: [
          { value: 'low', label: 'Low' },
          { value: 'medium', label: 'Medium' },
          { value: 'high', label: 'High' },
          { value: 'urgent', label: 'Urgent' },
        ],
        required: true,
      },
    ],
  },
  {
    value: 'assign_to',
    label: 'Assign To User',
    fields: [
      {
        name: 'assigned_to',
        label: 'User',
        type: 'select',
        options: props.users.map((user) => ({ value: user.id, label: user.name })),
        required: true,
      },
    ],
  },
  {
    value: 'delete',
    label: 'Delete Tickets',
  },
];

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    ticket_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('support-tickets.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle row clicks
const handleRowClick = (ticket) => {
  window.location.href = route('support-tickets.show', ticket.id);
};
</script>

<template>
  <Head title="Support Tickets" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.support_tickets.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('crm.support_tickets.create') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create/Edit Support Ticket Modal -->
      <CreateSupportTicketModal 
        :show="showModal" 
        :ticket="editingTicket" 
        :users="users"
        :contacts="contacts"
        @close="closeModal" 
      />

      <!-- Support Tickets DataTable -->
      <DataTable
        :data="supportTickets"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="support-tickets.index"
        :search-placeholder="$t('crm.search_tickets')"
        :empty-state-text="$t('crm.no_tickets_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('support-tickets.show', item.id)"
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              {{ $t('words.view') }}
            </Link>
            <button
              @click="openEditModal(item)"
              class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
            >
              {{ $t('words.edit') }}
            </button>
            <button
              v-if="item.status !== 'resolved' && item.status !== 'closed'"
              @click="resolveTicket(item)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.resolve') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>