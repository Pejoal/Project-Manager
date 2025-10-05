<script setup>
import DataTable from '@/Components/DataTable.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  activities: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  filterOptions: {
    type: Object,
    required: true,
  },
});

// Column configuration for the DataTable component
const columns = computed(() => [
  {
    key: 'description',
    label: 'Action',
    sortable: false, // Cannot sort on this field directly
    component: (item) => `
      <div>
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${item.description}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Module: ${item.subject_type || 'System'}
        </div>
      </div>
    `,
  },
  {
    key: 'causer',
    exportKey: 'causer.name', // Use causer.name for export
    label: 'User',
    sortable: true, 
    component: (item) => {
      if (!item.causer) {
        return `<span class="text-sm text-gray-500 dark:text-gray-400">System</span>`;
      }
      return `
            <div class="flex items-center">
                <img class="h-8 w-8 rounded-full mr-3" src="${item.causer.profile_photo_url}" alt="${item.causer.name}">
                <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${item.causer.name}</div>
                </div>
            </div>
        `;
    },
  },
  {
    key: 'event',
    label: 'Event',
    sortable: true,
    component: (item) => {
      const colorMap = {
        created: 'background-color: #10B98130; color: #059669;',
        updated: 'background-color: #3B82F630; color: #2563EB;',
        deleted: 'background-color: #EF444430; color: #D91A2A;',
      };
      const style = colorMap[item.event] || 'background-color: #6B728030; color: #4B5563;';
      return `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="${style}">${item.event}</span>`;
    },
  },
  {
    key: 'created_at_human',
    label: 'Time',
    sortable: true, // Sorting should be on created_at
    component: (item) => `
        <div class="text-sm text-gray-600 dark:text-gray-400" title="${item.created_at_formatted}">
            ${item.created_at_human}
        </div>
    `,
  },
  {
    key: 'details',
    label: 'Details',
  },
]);

// Filter configuration for the DataTable component
const filterConfig = computed(() => [
  {
    key: 'event',
    label: 'Event',
    type: 'vue-select',
    options: props.filterOptions.events,
    placeholder: 'All Events',
    multiple: false,
  },
  {
    key: 'subject_type',
    label: 'Module',
    type: 'vue-select',
    options: props.filterOptions.subject_types,
    placeholder: 'All Modules',
    multiple: false,
  },
  {
    key: 'causer_id',
    label: 'User',
    type: 'vue-select',
    options: props.filterOptions.users,
    optionLabel: 'name',
    reduce: (user) => user.id,
    placeholder: 'All Users',
    multiple: false,
  },
]);
</script>

<template>
  <Head title="Activity Log" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Activity Log</h2>
    </div>
  </header>

  <div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <DataTable
        :data="activities"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :route-name="'activities.index'"
        :search-placeholder="'Search description or user...'"
        :empty-state-text="'No activities found.'"
        :can-bulk-action="false"
      >
        <!-- Custom Details Cell -->
        <template #cell-details="{ item }">
          <div v-if="item.properties?.attributes || item.properties?.old" class="text-xs">
            <!-- New Values -->
            <div v-if="item.properties?.attributes" class="mb-2">
              <p class="font-semibold text-gray-700 dark:text-gray-300">New Values:</p>
              <dl class="mt-1">
                <template v-for="(value, key) in item.properties?.attributes" :key="key">
                  <div v-if="!key.endsWith('_formatted')" class="flex">
                    <dt class="font-medium text-gray-500 dark:text-gray-400 w-28 truncate">{{ key }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-300">
                      {{ item.properties?.attributes[key + '_formatted'] || value }}
                    </dd>
                  </div>
                </template>
              </dl>
            </div>
            <!-- Old Values -->
            <div v-if="item.properties?.old">
              <p class="font-semibold text-gray-700 dark:text-gray-300">Old Values:</p>
              <dl class="mt-1">
                <template v-for="(value, key) in item.properties?.old" :key="key">
                  <div v-if="!key.endsWith('_formatted')" class="flex">
                    <dt class="font-medium text-gray-500 dark:text-gray-400 w-28 truncate">{{ key }}:</dt>
                    <dd class="text-gray-700 dark:text-gray-300">
                      {{ item.properties?.old[key + '_formatted'] || value }}
                    </dd>
                  </div>
                </template>
              </dl>
            </div>
          </div>
          <div v-else class="text-xs text-gray-500 dark:text-gray-400">No details.</div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
