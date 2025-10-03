<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import vSelect from 'vue-select';
import PrimaryButton from './PrimaryButton.vue';
import Pagination from './Pagination.vue';
import Checkbox from './Checkbox.vue';

const props = defineProps({
  // Data props
  data: {
    type: Object,
    required: true,
    default: () => ({ data: [], current_page: 1, last_page: 1, total: 0 }),
  },
  columns: {
    type: Array,
    required: true,
    default: () => [],
  },

  // Filter configuration
  filters: {
    type: Object,
    default: () => ({}),
  },
  filterConfig: {
    type: Array,
    default: () => [],
  },

  // Bulk operations
  bulkActions: {
    type: Array,
    default: () => [],
  },
  canBulkAction: {
    type: Boolean,
    default: false,
  },

  // Search configuration
  searchable: {
    type: Boolean,
    default: true,
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...',
  },

  // Pagination
  perPageOptions: {
    type: Array,
    default: () => [10, 25, 50, 100],
  },

  // Table configuration
  sortable: {
    type: Boolean,
    default: true,
  },
  hoverable: {
    type: Boolean,
    default: true,
  },
  striped: {
    type: Boolean,
    default: false,
  },

  // Route for filtering
  routeName: {
    type: String,
    required: true,
  },

  // Route parameters (for nested routes)
  routeParams: {
    type: Object,
    default: () => ({}),
  },

  // Loading state
  loading: {
    type: Boolean,
    default: false,
  },

  // Custom classes
  tableClass: {
    type: String,
    default: '',
  },

  // Empty state
  emptyStateText: {
    type: String,
    default: 'No data found',
  },
  emptyStateIcon: {
    type: String,
    default:
      'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
  },
});

const emit = defineEmits(['bulk-action', 'row-click', 'cell-click', 'sort-change']);

// Local state
const selectedItems = ref([]);
const filtersVisible = ref(true);
const sortColumn = ref(props.filters.sort_by || '');
const sortDirection = ref(props.filters.sort_direction || 'asc');

// Initialize form data - only include values that exist in props.filters
const initializeFormData = () => {
  const formData = {
    search: props.filters.search || '',
    per_page: props.filters.per_page || props.perPageOptions[0],
    sort_by: props.filters.sort_by || '',
    sort_direction: props.filters.sort_direction || 'asc',
  };

  // Only add filter values that actually exist in props.filters
  Object.keys(props.filters).forEach((key) => {
    if (!['search', 'per_page', 'sort_by', 'sort_direction'].includes(key)) {
      formData[key] = props.filters[key];
    }
  });

  // Initialize filterConfig fields that are not in props.filters with appropriate empty values
  props.filterConfig.forEach((filter) => {
    if (!(filter.key in formData)) {
      // Use appropriate empty value based on field type
      if (filter.multiple || filter.type === 'multiselect') {
        formData[filter.key] = [];
      } else {
        formData[filter.key] = '';
      }
    }
  });

  return formData;
};

// Form for handling filters and pagination
const form = useForm(initializeFormData());

// Bulk actions form
const bulkForm = useForm({
  action: '',
  selected_items: [],
  ...props.bulkActions.reduce((acc, action) => {
    if (action.fields) {
      action.fields.forEach((field) => {
        acc[field.name] = field.default || '';
      });
    }
    return acc;
  }, {}),
});

// Watch for filter changes with debounce
let debounceTimer = null;
let isInitialLoad = true;

watch(
  () => form.data(),
  (newData, oldData) => {
    // Skip initial load
    if (isInitialLoad) {
      isInitialLoad = false;
      return;
    }

    // Don't trigger on initial load
    if (oldData) {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        applyFilters();
      }, 300);
    }
  },
  { deep: true }
);

// Computed properties
const pagination = computed(() => ({
  prev_page_url: props.data.prev_page_url,
  current_page: props.data.current_page,
  last_page: props.data.last_page,
  total: props.data.total,
  next_page_url: props.data.next_page_url,
}));

const allSelected = computed(() => {
  return selectedItems.value.length === props.data.data?.length && props.data.data?.length > 0;
});

const someSelected = computed(() => {
  return selectedItems.value.length > 0 && selectedItems.value.length < props.data.data?.length;
});

const selectedBulkAction = computed(() => {
  return props.bulkActions.find((action) => action.value === bulkForm.action);
});

// Methods
const applyFilters = () => {
  const params = { ...form.data() };

  // Remove empty values
  Object.keys(params).forEach((key) => {
    if (params[key] === '' || params[key] === null || params[key] === undefined) {
      delete params[key];
    }
  });

  // Use Inertia visit instead of form.get to handle route parameters properly
  router.visit(route(props.routeName, props.routeParams), {
    method: 'get',
    data: params,
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const sortBy = (column) => {
  if (!props.sortable || !column.sortable) return;

  if (sortColumn.value === column.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column.key;
    sortDirection.value = 'asc';
  }

  form.sort_by = column.key;
  form.sort_direction = sortDirection.value;

  emit('sort-change', { column: column.key, direction: sortDirection.value });

  // Apply sorting immediately
  applyFilters();
};

const getSortIcon = (column) => {
  if (!column.sortable) return '';
  if (sortColumn.value !== column.key) return '↕';
  return sortDirection.value === 'asc' ? '↑' : '↓';
};

const toggleItemSelection = (itemId) => {
  const index = selectedItems.value.indexOf(itemId);
  if (index > -1) {
    selectedItems.value.splice(index, 1);
  } else {
    selectedItems.value.push(itemId);
  }
};

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = props.data.data?.map((item) => getItemId(item)) || [];
  }
};

const getItemId = (item) => {
  return item.id || item.uuid || Object.values(item)[0];
};

const executeBulkAction = () => {
  if (selectedItems.value.length === 0) {
    alert('Please select items first');
    return;
  }

  if (!bulkForm.action) {
    alert('Please select an action');
    return;
  }

  const action = selectedBulkAction.value;

  // Validate required fields
  if (action.fields) {
    const missingFields = action.fields.filter((field) => field.required && !bulkForm[field.name]);

    if (missingFields.length > 0) {
      alert(`Please fill in required fields: ${missingFields.map((f) => f.label).join(', ')}`);
      return;
    }
  }

  if (confirm(`Are you sure you want to ${action.label.toLowerCase()} ${selectedItems.value.length} items?`)) {
    bulkForm.selected_items = selectedItems.value;

    emit('bulk-action', {
      action: bulkForm.action,
      items: selectedItems.value,
      data: { ...bulkForm.data() },
    });

    // Reset selections
    selectedItems.value = [];
    bulkForm.reset();
  }
};

const getCellValue = (item, column) => {
  if (column.render) {
    return column.render(item);
  }

  const keys = column.key.split('.');
  let value = item;

  for (const key of keys) {
    value = value?.[key];
    if (value === undefined || value === null) break;
  }

  // return value;
};

const getCellClasses = (column) => {
  const classes = ['px-6 py-4 whitespace-nowrap'];

  if (column.align === 'center') classes.push('text-center');
  else if (column.align === 'right') classes.push('text-right');
  else classes.push('text-left');

  if (column.width) classes.push(`w-${column.width}`);
  if (column.cellClass) classes.push(column.cellClass);

  return classes.join(' ');
};

const getHeaderClasses = (column) => {
  const classes = ['px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'];

  if (column.align === 'center') classes.push('text-center');
  else if (column.align === 'right') classes.push('text-right');
  else classes.push('text-left');

  if (column.sortable && props.sortable) {
    classes.push('cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800');
  }

  if (column.headerClass) classes.push(column.headerClass);

  return classes.join(' ');
};

const getRowClasses = (item, index) => {
  const classes = [];

  if (props.hoverable) classes.push('hover:bg-gray-50 dark:hover:bg-gray-700');
  if (props.striped && index % 2 === 1) classes.push('bg-gray-50 dark:bg-gray-700');

  classes.push('transition duration-150');

  return classes.join(' ');
};

const onRowClick = (item) => {
  emit('row-click', item);
};

const onCellClick = (item, column) => {
  emit('cell-click', { item, column });
};

// Initialize sorting from props
onMounted(() => {
  if (props.filters.sort_by) {
    sortColumn.value = props.filters.sort_by;
    sortDirection.value = props.filters.sort_direction || 'asc';
  }
});
</script>

<template>
  <div class="space-y-6">
    <!-- Search and Actions Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <!-- Search -->
      <div v-if="searchable" class="relative flex-1 sm:flex-initial">
        <input
          v-model="form.search"
          type="text"
          :placeholder="searchPlaceholder"
          class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
        />
        <svg
          class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
      </div>

      <!-- Per Page -->
      <div class="flex items-center space-x-2">
        <InputLabel for="per_page" value="Show" class="text-sm" />
        <select
          id="per_page"
          v-model="form.per_page"
          class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"
        >
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
        <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div
      v-if="canBulkAction && bulkActions.length > 0 && selectedItems.length > 0"
      class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
    >
      <div class="flex flex-wrap items-center gap-4">
        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
          {{ selectedItems.length }} items selected
        </span>

        <select
          v-model="bulkForm.action"
          class="rounded border-blue-300 dark:border-blue-700 dark:bg-blue-900 text-blue-900 dark:text-blue-100 text-sm"
        >
          <option value="">Select Action</option>
          <option v-for="action in bulkActions" :key="action.value" :value="action.value">
            {{ action.label }}
          </option>
        </select>

        <!-- Dynamic fields for selected action -->
        <template v-if="selectedBulkAction && selectedBulkAction.fields">
          <div v-for="field in selectedBulkAction.fields" :key="field.name" class="flex items-center space-x-2">
            <InputLabel :for="field.name" :value="field.label" class="text-sm" />

            <!-- Text/Number Input -->
            <TextInput
              v-if="field.type === 'text' || field.type === 'number'"
              :id="field.name"
              v-model="bulkForm[field.name]"
              :type="field.type"
              :placeholder="field.placeholder"
              :step="field.step"
              :min="field.min"
              :max="field.max"
              class="text-sm w-32"
            />

            <!-- Select -->
            <select
              v-else-if="field.type === 'select'"
              :id="field.name"
              v-model="bulkForm[field.name]"
              class="rounded border-blue-300 dark:border-blue-700 dark:bg-blue-900 text-blue-900 dark:text-blue-100 text-sm"
            >
              <option v-for="option in field.options" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>

            <!-- Multi-select -->
            <vSelect
              v-else-if="field.type === 'multiselect'"
              :id="field.name"
              v-model="bulkForm[field.name]"
              :options="field.options"
              :reduce="(option) => option.value"
              label="label"
              multiple
              :placeholder="field.placeholder"
              class="text-sm min-w-[200px]"
            />
          </div>
        </template>

        <PrimaryButton @click="executeBulkAction" :disabled="bulkForm.processing || !bulkForm.action" class="text-sm">
          Execute
        </PrimaryButton>

        <button
          @click="selectedItems = []"
          class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
        >
          Clear Selection
        </button>
      </div>
    </div>

    <!-- Filters Section -->
    <div v-if="filterConfig.length > 0" class="bg-white dark:bg-gray-800 shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <PrimaryButton @click="toggleFilters">
          {{ filtersVisible ? 'Hide Filters' : 'Show Filters' }}
        </PrimaryButton>
      </div>

      <transition name="slide-down">
        <div v-if="filtersVisible" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="filter in filterConfig" :key="filter.key">
              <InputLabel :for="filter.key" :value="filter.label" />

              <!-- Text Input -->
              <TextInput
                v-if="filter.type === 'text'"
                :id="filter.key"
                v-model="form[filter.key]"
                type="text"
                :placeholder="filter.placeholder"
                class="w-full"
              />

              <!-- Date Input -->
              <input
                v-else-if="filter.type === 'date'"
                :id="filter.key"
                v-model="form[filter.key]"
                type="date"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
              />

              <!-- Select -->
              <select
                v-else-if="filter.type === 'select'"
                :id="filter.key"
                v-model="form[filter.key]"
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
              >
                <option value="">{{ filter.placeholder || `All ${filter.label}` }}</option>
                <option v-for="option in filter.options" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>

              <!-- Vue Select -->
              <vSelect
                v-else-if="filter.type === 'vue-select'"
                :id="filter.key"
                v-model="form[filter.key]"
                :options="filter.options"
                :reduce="filter.reduce || ((option) => option.value)"
                :label="filter.optionLabel || 'label'"
                :placeholder="filter.placeholder"
                :multiple="filter.multiple"
                class="text-gray-700"
              />
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
      <div class="overflow-x-auto">
        <table :class="['min-w-full divide-y divide-gray-200 dark:divide-gray-700', tableClass]">
          <!-- Header -->
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <!-- Bulk Select Column -->
              <th
                v-if="canBulkAction"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
              >
                <Checkbox :checked="allSelected" :indeterminate="someSelected" @change="toggleSelectAll" />
              </th>

              <!-- Data Columns -->
              <th v-for="column in columns" :key="column.key" :class="getHeaderClasses(column)" @click="sortBy(column)">
                <div class="flex items-center space-x-1">
                  <span>{{ column.label }}</span>
                  <span v-if="column.sortable && sortable" class="text-gray-400">
                    {{ getSortIcon(column) }}
                  </span>
                </div>
              </th>
            </tr>
          </thead>

          <!-- Body -->
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <!-- Data Rows -->
            <tr
              v-for="(item, index) in data.data"
              :key="getItemId(item)"
              :class="getRowClasses(item, index)"
              @click="onRowClick(item)"
            >
              <!-- Bulk Select Cell -->
              <td v-if="canBulkAction" class="px-6 py-4 whitespace-nowrap">
                <Checkbox
                  :checked="selectedItems.includes(getItemId(item))"
                  @change="toggleItemSelection(getItemId(item))"
                  @click.stop
                />
              </td>

              <!-- Data Cells -->
              <td
                v-for="column in columns"
                :key="column.key"
                :class="getCellClasses(column)"
                @click="onCellClick(item, column)"
              >
                <!-- Slot for custom cell content -->
                <slot :name="`cell-${column.key}`" :item="item" :value="getCellValue(item, column)" :column="column">
                  <!-- Default cell content -->
                  <div v-if="column.component" v-html="column.component(item, getCellValue(item, column))" />
                  <span :class="['text-sm', column.textClass || 'text-gray-900 dark:text-gray-100']">
                    {{ getCellValue(item, column) }}
                  </span>
                </slot>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="!data.data || data.data.length === 0">
              <td
                :colspan="columns.length + (canBulkAction ? 1 : 0)"
                class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
              >
                <div class="flex flex-col items-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="emptyStateIcon" />
                  </svg>
                  <p class="mt-2">{{ emptyStateText }}</p>

                  <!-- Slot for custom empty state -->
                  <slot name="empty-state" />
                </div>
              </td>
            </tr>

            <!-- Loading State -->
            <tr v-if="loading">
              <td
                :colspan="columns.length + (canBulkAction ? 1 : 0)"
                class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
              >
                <div class="flex items-center justify-center">
                  <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                  </svg>
                  <span class="ml-2">Loading...</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination :pagination="pagination" />
    </div>

    <!-- Table Info -->
    <div v-if="data.data && data.data.length > 0" class="text-sm text-gray-600 dark:text-gray-400 text-center">
      Showing {{ (data.current_page - 1) * form.per_page + 1 }} to
      {{ Math.min(data.current_page * form.per_page, data.total) }} of {{ data.total }} entries
    </div>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: max-height 0.3s ease-in-out;
}

.slide-down-enter-from,
.slide-down-leave-to {
  max-height: 0;
  overflow: hidden;
}

.slide-down-enter-to,
.slide-down-leave-from {
  max-height: 50rem;
  overflow: auto;
}
</style>
