<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateLeadModal from './CreateLeadModal.vue';

const props = defineProps({
  leads: Object,
  filters: Object,
  users: Array,
  campaigns: Array,
});

const showModal = ref(false);
const editingLead = ref(null);

const openModal = () => {
  editingLead.value = null;
  showModal.value = true;
};

const openEditModal = (lead) => {
  editingLead.value = lead;
  showModal.value = true;
};

const closeModal = () => {
  editingLead.value = null;
  showModal.value = false;
};

// Column configuration for DataTable
const columns = [
  {
    key: 'name',
    label: 'Lead',
    sortable: true,
    component: (item) => `
      <div>
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
          ${item.first_name} ${item.last_name}
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
          ${item.email}
        </div>
      </div>
    `,
  },
  {
    key: 'company',
    label: 'Company',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.company || 'N/A'}
      </div>
    `,
  },
  {
    key: 'status',
    label: 'Status',
    sortable: true,
    component: (item) => {
      const statusColors = {
        new: '#3B82F6',
        contacted: '#F59E0B',
        qualified: '#10B981',
        unqualified: '#EF4444',
        converted: '#8B5CF6',
        lost: '#6B7280',
      };
      const color = statusColors[item.status] || '#6B7280';
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
              style="background-color: ${color}">
          ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
        </span>
      `;
    },
  },
  {
    key: 'source',
    label: 'Source',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.source ? item.source.replace(/_/g, ' ').toUpperCase() : 'N/A'}
      </div>
    `,
  },
  {
    key: 'score',
    label: 'Score',
    sortable: true,
    component: (item) => {
      const score = item.score || 0;
      const color = score >= 80 ? '#10B981' : score >= 60 ? '#F59E0B' : '#EF4444';
      return `
        <div class="flex items-center">
          <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
            <div class="h-2 rounded-full" style="width: ${score}%; background-color: ${color}"></div>
          </div>
          <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${score}</span>
        </div>
      `;
    },
  },
  {
    key: 'estimated_value',
    label: 'Est. Value',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.estimated_value ? '$' + parseFloat(item.estimated_value).toLocaleString() : 'N/A'}
      </div>
    `,
  },
  {
    key: 'assigned_to',
    label: 'Assigned To',
    sortable: false,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.assigned_to?.name || 'Unassigned'}
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
      { value: 'new', label: 'New' },
      { value: 'contacted', label: 'Contacted' },
      { value: 'qualified', label: 'Qualified' },
      { value: 'unqualified', label: 'Unqualified' },
      { value: 'converted', label: 'Converted' },
      { value: 'lost', label: 'Lost' },
    ],
    placeholder: 'All Statuses',
  },
  {
    key: 'source',
    label: 'Source',
    type: 'select',
    options: [
      { value: 'website', label: 'Website' },
      { value: 'referral', label: 'Referral' },
      { value: 'social_media', label: 'Social Media' },
      { value: 'email_campaign', label: 'Email Campaign' },
      { value: 'phone_call', label: 'Phone Call' },
      { value: 'trade_show', label: 'Trade Show' },
      { value: 'advertisement', label: 'Advertisement' },
      { value: 'other', label: 'Other' },
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
  {
    key: 'score_min',
    label: 'Min Score',
    type: 'number',
    placeholder: '0',
  },
  {
    key: 'score_max',
    label: 'Max Score',
    type: 'number',
    placeholder: '100',
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
          { value: 'new', label: 'New' },
          { value: 'contacted', label: 'Contacted' },
          { value: 'qualified', label: 'Qualified' },
          { value: 'unqualified', label: 'Unqualified' },
          { value: 'converted', label: 'Converted' },
          { value: 'lost', label: 'Lost' },
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
    label: 'Delete Leads',
  },
];

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    lead_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('leads.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle row clicks
const handleRowClick = (lead) => {
  window.location.href = route('leads.show', lead.id);
};

// Convert lead function
const convertLead = (lead) => {
  if (confirm('Are you sure you want to convert this lead to a contact?')) {
    useForm().post(route('leads.convert', lead.id), {
      preserveState: true,
      onSuccess: () => {
        // Optionally show success notification
      },
    });
  }
};
</script>

<template>
  <Head title="Leads" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.leads.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('crm.leads.create') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create/Edit Lead Modal -->
      <CreateLeadModal
        :show="showModal"
        :lead="editingLead"
        :users="users"
        :campaigns="campaigns"
        @close="closeModal"
      />

      <!-- Leads DataTable -->
      <DataTable
        :data="leads"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="leads.index"
        :search-placeholder="$t('crm.search_leads')"
        :empty-state-text="$t('crm.no_leads_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('leads.show', item.id)"
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
              v-if="item.status !== 'converted'"
              @click="convertLead(item)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.convert') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
