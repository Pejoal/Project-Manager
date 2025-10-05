<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
  IconCalendar,
  IconChevronDown,
  IconEye,
  IconMail,
  IconPencil,
  IconPhone,
  IconPlus,
  IconSearch,
  IconTrash,
  IconUser,
} from '@tabler/icons-vue';
import { ref } from 'vue';

const props = defineProps({
  interactions: Object,
  users: Array,
  filters: Object,
  sort: Object,
});

const form = useForm({
  search: props.filters?.search || '',
  type: props.filters?.type || '',
  direction: props.filters?.direction || '',
  user_id: props.filters?.user_id || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
  interactable_type: props.filters?.interactable_type || '',
});

const selectedInteractions = ref([]);
const bulkActionForm = useForm({
  action: '',
  interaction_ids: [],
});

// Search and filter
const search = () => {
  router.get(route('interactions.index'), form.data(), {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  form.reset();
  router.get(route('interactions.index'));
};

// Sorting
const sortBy = (field) => {
  const direction = props.sort?.sort === field && props.sort?.direction === 'asc' ? 'desc' : 'asc';
  router.get(
    route('interactions.index'),
    {
      ...form.data(),
      sort: field,
      direction,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

// Interaction actions
const deleteInteraction = (interaction) => {
  if (confirm('Are you sure you want to delete this interaction?')) {
    useForm().delete(route('interactions.destroy', interaction.id), {
      preserveState: true,
    });
  }
};

// Bulk actions
const toggleSelectAll = () => {
  if (selectedInteractions.value.length === props.interactions.data.length) {
    selectedInteractions.value = [];
  } else {
    selectedInteractions.value = props.interactions.data.map((interaction) => interaction.id);
  }
};

const executeBulkAction = () => {
  if (!bulkActionForm.action || selectedInteractions.value.length === 0) return;

  bulkActionForm.interaction_ids = selectedInteractions.value;

  if (bulkActionForm.action === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedInteractions.value.length} interactions?`)) return;
  }

  bulkActionForm.post(route('interactions.bulk-action'), {
    preserveState: true,
    onSuccess: () => {
      selectedInteractions.value = [];
      bulkActionForm.reset();
    },
  });
};

// Utility functions
const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};

const getTypeIcon = (type) => {
  const icons = {
    call: IconPhone,
    email: IconMail,
    meeting: IconCalendar,
    note: IconUser,
  };
  return icons[type] || IconUser;
};

const getTypeColor = (type) => {
  const colors = {
    call: 'bg-blue-100 text-blue-800',
    email: 'bg-green-100 text-green-800',
    meeting: 'bg-purple-100 text-purple-800',
    note: 'bg-gray-100 text-gray-800',
    sms: 'bg-yellow-100 text-yellow-800',
    social_media: 'bg-pink-100 text-pink-800',
    webinar: 'bg-indigo-100 text-indigo-800',
    demo: 'bg-orange-100 text-orange-800',
    other: 'bg-gray-100 text-gray-800',
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};

const getDirectionColor = (direction) => {
  return direction === 'inbound' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
};

const getInteractableName = (interaction) => {
  if (!interaction.interactable) return 'Unknown';

  if (interaction.interactable_type === 'App\\Models\\Contact') {
    return (
      `${interaction.interactable.first_name || ''} ${interaction.interactable.last_name || ''}`.trim() ||
      'Unknown Contact'
    );
  }

  return interaction.interactable.name || interaction.interactable.subject || 'Unknown';
};
</script>

<template>
  <Head title="Interactions" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.interactions') }}
        </h1>
        <div class="flex space-x-3">
          <Link
            :href="route('interactions.follow-ups')"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.follow_ups') }}
          </Link>
          <Link
            :href="route('interactions.analytics')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.analytics') }}
          </Link>
          <Link
            :href="route('interactions.create')"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <IconPlus class="w-4 h-4 mr-2" />
            {{ $t('crm.add_interaction') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Search and Filters -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
          <!-- Search -->
          <div class="relative">
            <IconSearch class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
            <input
              v-model="form.search"
              @keyup.enter="search"
              type="text"
              :placeholder="$t('words.search')"
              class="pl-10 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            />
          </div>

          <!-- Type Filter -->
          <select
            v-model="form.type"
            @change="search"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          >
            <option value="">{{ $t('crm.all_types') }}</option>
            <option value="call">{{ $t('crm.call') }}</option>
            <option value="email">{{ $t('crm.email') }}</option>
            <option value="meeting">{{ $t('crm.meeting') }}</option>
            <option value="note">{{ $t('crm.note') }}</option>
            <option value="sms">{{ $t('crm.sms') }}</option>
            <option value="social_media">{{ $t('crm.social_media') }}</option>
            <option value="webinar">{{ $t('crm.webinar') }}</option>
            <option value="demo">{{ $t('crm.demo') }}</option>
            <option value="other">{{ $t('crm.other') }}</option>
          </select>

          <!-- Direction Filter -->
          <select
            v-model="form.direction"
            @change="search"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          >
            <option value="">{{ $t('crm.all_directions') }}</option>
            <option value="inbound">{{ $t('crm.inbound') }}</option>
            <option value="outbound">{{ $t('crm.outbound') }}</option>
          </select>

          <!-- User Filter -->
          <select
            v-model="form.user_id"
            @change="search"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          >
            <option value="">{{ $t('crm.all_users') }}</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Date From -->
          <input
            v-model="form.date_from"
            @change="search"
            type="date"
            :placeholder="$t('crm.date_from')"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          />

          <!-- Date To -->
          <input
            v-model="form.date_to"
            @change="search"
            type="date"
            :placeholder="$t('crm.date_to')"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          />

          <!-- Actions -->
          <div class="flex space-x-2">
            <button @click="search" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.search') }}
            </button>
            <button @click="clearFilters" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.clear') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedInteractions.length > 0" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg mb-6">
        <div class="flex items-center justify-between">
          <span class="text-sm text-blue-700 dark:text-blue-300">
            {{ selectedInteractions.length }} {{ $t('crm.interactions_selected') }}
          </span>
          <div class="flex items-center space-x-4">
            <select
              v-model="bulkActionForm.action"
              class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
            >
              <option value="">{{ $t('crm.bulk_actions') }}</option>
              <option value="mark_follow_up_complete">{{ $t('crm.mark_follow_up_complete') }}</option>
              <option value="delete">{{ $t('words.delete') }}</option>
            </select>
            <button
              @click="executeBulkAction"
              :disabled="!bulkActionForm.action"
              class="bg-blue-500 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded text-sm"
            >
              {{ $t('words.execute') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Interactions Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left">
                <input
                  type="checkbox"
                  @change="toggleSelectAll"
                  :checked="selectedInteractions.length === interactions.data.length && interactions.data.length > 0"
                  class="rounded border-gray-300 dark:border-gray-700"
                />
              </th>
              <th
                @click="sortBy('subject')"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
              >
                {{ $t('words.subject') }}
                <IconChevronDown v-if="sort?.sort === 'subject'" class="w-4 h-4 inline ml-1" />
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.type') }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('crm.related_to') }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.user') }}
              </th>
              <th
                @click="sortBy('interaction_date')"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
              >
                {{ $t('crm.date') }}
                <IconChevronDown v-if="sort?.sort === 'interaction_date'" class="w-4 h-4 inline ml-1" />
              </th>
              <th
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="interaction in interactions.data"
              :key="interaction.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  v-model="selectedInteractions"
                  :value="interaction.id"
                  type="checkbox"
                  class="rounded border-gray-300 dark:border-gray-700"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ interaction.subject }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ interaction.description?.substring(0, 100)
                      }}{{ interaction.description?.length > 100 ? '...' : '' }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeColor(interaction.type)}`"
                  >
                    <component :is="getTypeIcon(interaction.type)" class="w-3 h-3 mr-1" />
                    {{ interaction.type?.replace('_', ' ').toUpperCase() }}
                  </span>
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getDirectionColor(interaction.direction)}`"
                  >
                    {{ interaction.direction?.toUpperCase() }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ getInteractableName(interaction) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ interaction.user?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ formatDate(interaction.interaction_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <Link
                  :href="route('interactions.show', interaction.id)"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                  title="View"
                >
                  <IconEye class="w-4 h-4 inline" />
                </Link>
                <Link
                  :href="route('interactions.edit', interaction.id)"
                  class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                  title="Edit"
                >
                  <IconPencil class="w-4 h-4 inline" />
                </Link>
                <button
                  @click="deleteInteraction(interaction)"
                  class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                  title="Delete"
                >
                  <IconTrash class="w-4 h-4 inline" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div
          v-if="interactions.links"
          class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6"
        >
          <div class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
              <Link
                v-if="interactions.prev_page_url"
                :href="interactions.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                {{ $t('words.previous') }}
              </Link>
              <Link
                v-if="interactions.next_page_url"
                :href="interactions.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                {{ $t('words.next') }}
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  {{ $t('words.showing') }}
                  <span class="font-medium">{{ interactions.from }}</span>
                  {{ $t('words.to') }}
                  <span class="font-medium">{{ interactions.to }}</span>
                  {{ $t('words.of') }}
                  <span class="font-medium">{{ interactions.total }}</span>
                  {{ $t('words.results') }}
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <Link
                    v-for="link in interactions.links"
                    :key="link.label"
                    :href="link.url"
                    v-html="link.label"
                    :class="{
                      'relative inline-flex items-center px-2 py-2 border text-sm font-medium': true,
                      'bg-blue-50 border-blue-500 text-blue-600': link.active,
                      'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                      'cursor-not-allowed opacity-50': !link.url,
                    }"
                  />
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
