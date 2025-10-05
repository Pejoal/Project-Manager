<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateCampaignModal from './CreateCampaignModal.vue';

const props = defineProps({
  campaigns: Object,
  filters: Object,
  users: Array,
});

const showModal = ref(false);
const editingCampaign = ref(null);

const openModal = () => {
  editingCampaign.value = null;
  showModal.value = true;
};

const openEditModal = (campaign) => {
  editingCampaign.value = campaign;
  showModal.value = true;
};

const closeModal = () => {
  editingCampaign.value = null;
  showModal.value = false;
};

// Column configuration for DataTable
const columns = [
  {
    key: 'name',
    label: 'Campaign',
    sortable: true,
    component: (item) => `
      <div>
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
          ${item.name}
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
          ${item.type.replace(/_/g, ' ').toUpperCase()}
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
        draft: '#6B7280',
        active: '#10B981',
        paused: '#F59E0B',
        completed: '#8B5CF6',
        cancelled: '#EF4444',
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
    key: 'budget',
    label: 'Budget',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.budget ? '$' + parseFloat(item.budget).toLocaleString() : 'N/A'}
      </div>
    `,
  },
  {
    key: 'actual_cost',
    label: 'Actual Cost',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        $${parseFloat(item.actual_cost || 0).toLocaleString()}
      </div>
    `,
  },
  {
    key: 'leads_generated',
    label: 'Leads',
    sortable: true,
    component: (item) => `
      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
        ${item.leads_generated || 0}
      </div>
    `,
  },
  {
    key: 'opportunities_created',
    label: 'Opportunities',
    sortable: true,
    component: (item) => `
      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
        ${item.opportunities_created || 0}
      </div>
    `,
  },
  {
    key: 'revenue_generated',
    label: 'Revenue',
    sortable: true,
    component: (item) => `
      <div class="text-sm font-medium text-green-600 dark:text-green-400">
        $${parseFloat(item.revenue_generated || 0).toLocaleString()}
      </div>
    `,
  },
  {
    key: 'roi',
    label: 'ROI',
    sortable: false,
    component: (item) => {
      const cost = parseFloat(item.actual_cost || 0);
      const revenue = parseFloat(item.revenue_generated || 0);
      const roi = cost > 0 ? (((revenue - cost) / cost) * 100).toFixed(1) : 0;
      const color = roi > 0 ? '#10B981' : roi < 0 ? '#EF4444' : '#6B7280';
      return `
        <div class="text-sm font-medium" style="color: ${color}">
          ${roi}%
        </div>
      `;
    },
  },
  {
    key: 'date_range',
    label: 'Duration',
    sortable: false,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.start_date ? new Date(item.start_date).toLocaleDateString() : 'N/A'} - 
        ${item.end_date ? new Date(item.end_date).toLocaleDateString() : 'N/A'}
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
      { value: 'draft', label: 'Draft' },
      { value: 'active', label: 'Active' },
      { value: 'paused', label: 'Paused' },
      { value: 'completed', label: 'Completed' },
      { value: 'cancelled', label: 'Cancelled' },
    ],
    placeholder: 'All Statuses',
  },
  {
    key: 'type',
    label: 'Type',
    type: 'select',
    options: [
      { value: 'email', label: 'Email' },
      { value: 'social_media', label: 'Social Media' },
      { value: 'webinar', label: 'Webinar' },
      { value: 'trade_show', label: 'Trade Show' },
      { value: 'direct_mail', label: 'Direct Mail' },
      { value: 'telemarketing', label: 'Telemarketing' },
      { value: 'other', label: 'Other' },
    ],
    placeholder: 'All Types',
  },
  {
    key: 'created_by',
    label: 'Created By',
    type: 'vue-select',
    options: props.users,
    reduce: (user) => user.id,
    optionLabel: 'name',
    placeholder: 'All Users',
  },
  {
    key: 'budget_min',
    label: 'Min Budget',
    type: 'number',
    placeholder: '0',
  },
  {
    key: 'budget_max',
    label: 'Max Budget',
    type: 'number',
    placeholder: '100000',
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
          { value: 'draft', label: 'Draft' },
          { value: 'active', label: 'Active' },
          { value: 'paused', label: 'Paused' },
          { value: 'completed', label: 'Completed' },
          { value: 'cancelled', label: 'Cancelled' },
        ],
        required: true,
      },
    ],
  },
  {
    value: 'delete',
    label: 'Delete Campaigns',
  },
];

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    campaign_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('campaigns.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle row clicks
const handleRowClick = (campaign) => {
  window.location.href = route('campaigns.show', campaign.id);
};
</script>

<template>
  <Head title="Campaigns" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.campaigns.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('crm.campaigns.create') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create/Edit Campaign Modal -->
      <CreateCampaignModal :show="showModal" :campaign="editingCampaign" :users="users" @close="closeModal" />

      <!-- Campaigns DataTable -->
      <DataTable
        :data="campaigns"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="campaigns.index"
        :search-placeholder="$t('crm.search_campaigns')"
        :empty-state-text="$t('crm.no_campaigns_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('campaigns.show', item.id)"
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
            <!-- <Link
              :href="route('campaigns.report', item.id)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.view_report') }}
            </Link> -->
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
