<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateOpportunityModal from './CreateOpportunityModal.vue';

const props = defineProps({
  opportunities: Object,
  filters: Object,
  users: Array,
  contacts: Array,
});

const showModal = ref(false);
const editingOpportunity = ref(null);

const openModal = () => {
  editingOpportunity.value = null;
  showModal.value = true;
};

const openEditModal = (opportunity) => {
  editingOpportunity.value = opportunity;
  showModal.value = true;
};

const closeModal = () => {
  editingOpportunity.value = null;
  showModal.value = false;
};

// Column configuration for DataTable
const columns = [
  {
    key: 'name',
    label: 'Opportunity',
    sortable: true,
    component: (item) => `
      <div>
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
          ${item.name}
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
          ${item.contact ? item.contact.first_name + ' ' + item.contact.last_name : 'No Contact'}
        </div>
      </div>
    `,
  },
  {
    key: 'value',
    label: 'Value',
    sortable: true,
    component: (item) => `
      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
        $${parseFloat(item.value).toLocaleString()}
      </div>
    `,
  },
  {
    key: 'stage',
    label: 'Stage',
    sortable: true,
    component: (item) => {
      const stageColors = {
        prospecting: '#6B7280',
        qualification: '#3B82F6',
        needs_analysis: '#F59E0B',
        proposal: '#8B5CF6',
        negotiation: '#EF4444',
        closed_won: '#10B981',
        closed_lost: '#DC2626',
      };
      const color = stageColors[item.stage] || '#6B7280';
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
              style="background-color: ${color}">
          ${item.stage.replace(/_/g, ' ').toUpperCase()}
        </span>
      `;
    },
  },
  {
    key: 'probability',
    label: 'Probability',
    sortable: true,
    component: (item) => {
      const probability = item.probability || 0;
      const color = probability >= 80 ? '#10B981' : probability >= 50 ? '#F59E0B' : '#EF4444';
      return `
        <div class="flex items-center">
          <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
            <div class="h-2 rounded-full" style="width: ${probability}%; background-color: ${color}"></div>
          </div>
          <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${probability}%</span>
        </div>
      `;
    },
  },
  {
    key: 'expected_close_date',
    label: 'Expected Close',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${new Date(item.expected_close_date).toLocaleDateString()}
      </div>
    `,
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
    key: 'actions',
    label: 'Actions',
    component: () => '', // Will use slot
  },
];

// Filter configuration
const filterConfig = [
  {
    key: 'stage',
    label: 'Stage',
    type: 'select',
    options: [
      { value: 'prospecting', label: 'Prospecting' },
      { value: 'qualification', label: 'Qualification' },
      { value: 'needs_analysis', label: 'Needs Analysis' },
      { value: 'proposal', label: 'Proposal' },
      { value: 'negotiation', label: 'Negotiation' },
      { value: 'closed_won', label: 'Closed Won' },
      { value: 'closed_lost', label: 'Closed Lost' },
    ],
    placeholder: 'All Stages',
  },
  {
    key: 'type',
    label: 'Type',
    type: 'select',
    options: [
      { value: 'new_business', label: 'New Business' },
      { value: 'existing_business', label: 'Existing Business' },
      { value: 'renewal', label: 'Renewal' },
    ],
    placeholder: 'All Types',
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
    key: 'value_min',
    label: 'Min Value',
    type: 'number',
    placeholder: '0',
  },
  {
    key: 'value_max',
    label: 'Max Value',
    type: 'number',
    placeholder: '1000000',
  },
  {
    key: 'probability_min',
    label: 'Min Probability',
    type: 'number',
    placeholder: '0',
  },
];

// Bulk actions configuration
const bulkActions = [
  {
    value: 'update_stage',
    label: 'Update Stage',
    fields: [
      {
        name: 'stage',
        label: 'Stage',
        type: 'select',
        options: [
          { value: 'prospecting', label: 'Prospecting' },
          { value: 'qualification', label: 'Qualification' },
          { value: 'needs_analysis', label: 'Needs Analysis' },
          { value: 'proposal', label: 'Proposal' },
          { value: 'negotiation', label: 'Negotiation' },
          { value: 'closed_won', label: 'Closed Won' },
          { value: 'closed_lost', label: 'Closed Lost' },
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
    value: 'update_probability',
    label: 'Update Probability',
    fields: [
      {
        name: 'probability',
        label: 'Probability (%)',
        type: 'number',
        min: 0,
        max: 100,
        required: true,
      },
    ],
  },
  {
    value: 'delete',
    label: 'Delete Opportunities',
  },
];

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    opportunity_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('opportunities.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle row clicks
const handleRowClick = (opportunity) => {
  window.location.href = route('opportunities.show', opportunity.id);
};

// Close opportunity function
const closeOpportunity = (opportunity, outcome) => {
  const action = outcome === 'won' ? 'win' : 'lose';
  const message =
    outcome === 'won'
      ? 'Are you sure you want to mark this opportunity as won?'
      : 'Are you sure you want to mark this opportunity as lost?';

  if (confirm(message)) {
    const form = useForm({
      notes: '',
      loss_reason: outcome === 'lost' ? 'Competitor chosen' : null,
    });

    form.patch(route(`opportunities.${action}`, opportunity.id), {
      preserveState: true,
      onSuccess: () => {
        // Optionally show success notification
      },
    });
  }
};
</script>

<template>
  <Head title="Opportunities" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.opportunities.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('crm.opportunities.create') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create/Edit Opportunity Modal -->
      <CreateOpportunityModal
        :show="showModal"
        :opportunity="editingOpportunity"
        :users="users"
        :contacts="contacts"
        @close="closeModal"
      />

      <!-- Opportunities DataTable -->
      <DataTable
        :data="opportunities"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="opportunities.index"
        :search-placeholder="$t('crm.search_opportunities')"
        :empty-state-text="$t('crm.no_opportunities_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('opportunities.show', item.id)"
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
              v-if="item.stage !== 'closed_won' && item.stage !== 'closed_lost'"
              @click="closeOpportunity(item, 'won')"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.close_won') }}
            </button>
            <button
              v-if="item.stage !== 'closed_won' && item.stage !== 'closed_lost'"
              @click="closeOpportunity(item, 'lost')"
              class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
            >
              {{ $t('crm.close_lost') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
