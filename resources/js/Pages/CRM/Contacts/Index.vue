<script setup>
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateContactModal from './CreateContactModal.vue';

const props = defineProps({
  contacts: Object,
  filters: Object,
  users: Array,
});

const showModal = ref(false);
const editingContact = ref(null);

const openModal = () => {
  editingContact.value = null;
  showModal.value = true;
};

const openEditModal = (contact) => {
  editingContact.value = contact;
  showModal.value = true;
};

const closeModal = () => {
  editingContact.value = null;
  showModal.value = false;
};

// Column configuration for DataTable
const columns = [
  {
    key: 'name',
    label: 'Contact',
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
    key: 'job_title',
    label: 'Job Title',
    sortable: true,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.job_title || 'N/A'}
      </div>
    `,
  },
  {
    key: 'phone',
    label: 'Phone',
    sortable: false,
    component: (item) => `
      <div class="text-sm text-gray-900 dark:text-gray-100">
        ${item.phone || 'N/A'}
      </div>
    `,
  },
  {
    key: 'type',
    label: 'Type',
    sortable: true,
    component: (item) => {
      const typeColors = {
        customer: '#10B981',
        prospect: '#3B82F6',
        partner: '#8B5CF6',
        vendor: '#F59E0B',
        other: '#6B7280',
      };
      const color = typeColors[item.type] || '#6B7280';
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
              style="background-color: ${color}">
          ${item.type.charAt(0).toUpperCase() + item.type.slice(1)}
        </span>
      `;
    },
  },
  {
    key: 'status',
    label: 'Status',
    sortable: true,
    component: (item) => {
      const statusColors = {
        active: '#10B981',
        inactive: '#F59E0B',
        do_not_contact: '#EF4444',
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
    key: 'type',
    label: 'Type',
    type: 'select',
    options: [
      { value: 'customer', label: 'Customer' },
      { value: 'prospect', label: 'Prospect' },
      { value: 'partner', label: 'Partner' },
      { value: 'vendor', label: 'Vendor' },
      { value: 'other', label: 'Other' },
    ],
    placeholder: 'All Types',
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select',
    options: [
      { value: 'active', label: 'Active' },
      { value: 'inactive', label: 'Inactive' },
      { value: 'do_not_contact', label: 'Do Not Contact' },
    ],
    placeholder: 'All Statuses',
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
    key: 'company',
    label: 'Company',
    type: 'text',
    placeholder: 'Search by company...',
  },
];

// Bulk actions configuration
const bulkActions = [
  {
    value: 'update_type',
    label: 'Update Type',
    fields: [
      {
        name: 'type',
        label: 'Type',
        type: 'select',
        options: [
          { value: 'customer', label: 'Customer' },
          { value: 'prospect', label: 'Prospect' },
          { value: 'partner', label: 'Partner' },
          { value: 'vendor', label: 'Vendor' },
          { value: 'other', label: 'Other' },
        ],
        required: true,
      },
    ],
  },
  {
    value: 'update_status',
    label: 'Update Status',
    fields: [
      {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
          { value: 'active', label: 'Active' },
          { value: 'inactive', label: 'Inactive' },
          { value: 'do_not_contact', label: 'Do Not Contact' },
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
    label: 'Delete Contacts',
  },
];

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    contact_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('contacts.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle row clicks
const handleRowClick = (contact) => {
  window.location.href = route('contacts.show', contact.id);
};
</script>

<template>
  <Head title="Contacts" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.contacts.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton @click="openModal">
            {{ $t('crm.contacts.create') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Create/Edit Contact Modal -->
      <CreateContactModal :show="showModal" :contact="editingContact" :users="users" @close="closeModal" />

      <!-- Contacts DataTable -->
      <DataTable
        :data="contacts"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="contacts.index"
        :search-placeholder="$t('crm.search_contacts')"
        :empty-state-text="$t('crm.no_contacts_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2" @click.stop>
            <Link
              :href="route('contacts.show', item.id)"
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
            <Link
              :href="route('opportunities.create', { contact_id: item.id })"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('crm.create_opportunity') }}
            </Link>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
