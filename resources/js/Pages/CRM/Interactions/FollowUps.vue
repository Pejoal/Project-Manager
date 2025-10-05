<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
  IconArrowLeft,
  IconCalendar,
  IconCheck,
  IconClock,
  IconEye,
  IconMail,
  IconPhone,
  IconUser,
} from '@tabler/icons-vue';
import { ref } from 'vue';

const props = defineProps({
  followUps: Object,
  users: Array,
  filters: Object,
});

const form = useForm({
  overdue: props.filters?.overdue || false,
  user_id: props.filters?.user_id || '',
});

const selectedFollowUps = ref([]);
const bulkActionForm = useForm({
  action: '',
  interaction_ids: [],
});

// Search and filter
const search = () => {
  router.get(route('interactions.follow-ups'), form.data(), {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  form.reset();
  router.get(route('interactions.follow-ups'));
};

// Follow-up actions
const markFollowUpComplete = (interaction) => {
  if (confirm('Mark follow-up as complete?')) {
    useForm().patch(route('interactions.mark-follow-up-complete', interaction.id), {
      preserveState: true,
    });
  }
};

// Bulk actions
const toggleSelectAll = () => {
  if (selectedFollowUps.value.length === props.followUps.data.length) {
    selectedFollowUps.value = [];
  } else {
    selectedFollowUps.value = props.followUps.data.map((interaction) => interaction.id);
  }
};

const executeBulkAction = () => {
  if (!bulkActionForm.action || selectedFollowUps.value.length === 0) return;

  bulkActionForm.interaction_ids = selectedFollowUps.value;

  bulkActionForm.post(route('interactions.bulk-action'), {
    preserveState: true,
    onSuccess: () => {
      selectedFollowUps.value = [];
      bulkActionForm.reset();
    },
  });
};

// Utility functions
const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};

const formatDateTime = (datetime) => {
  if (!datetime) return '';
  return new Date(datetime).toLocaleString();
};

const isOverdue = (date) => {
  if (!date) return false;
  return new Date(date) < new Date();
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

const getDaysUntilDue = (date) => {
  if (!date) return null;
  const dueDate = new Date(date);
  const today = new Date();
  const diffTime = dueDate - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays;
};

const getDueDateColor = (date) => {
  const days = getDaysUntilDue(date);
  if (days === null) return '';

  if (days < 0) return 'text-red-600 font-bold'; // Overdue
  if (days === 0) return 'text-orange-600 font-bold'; // Due today
  if (days <= 3) return 'text-yellow-600 font-semibold'; // Due soon
  return 'text-gray-600'; // Not urgent
};
</script>

<template>
  <Head title="Follow-ups" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('interactions.index')"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <IconArrowLeft class="w-5 h-5" />
          </Link>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            {{ $t('crm.follow_ups') }}
          </h1>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('interactions.index')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.all_interactions') }}
          </Link>
          <Link
            :href="route('interactions.analytics')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.analytics') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Filters -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Overdue Filter -->
          <div class="flex items-center">
            <input
              id="overdue"
              v-model="form.overdue"
              @change="search"
              type="checkbox"
              class="rounded border-gray-300 dark:border-gray-700"
            />
            <label for="overdue" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
              {{ $t('crm.show_overdue_only') }}
            </label>
          </div>

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

          <!-- Actions -->
          <div class="flex space-x-2">
            <button @click="search" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.filter') }}
            </button>
            <button @click="clearFilters" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.clear') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedFollowUps.length > 0" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg mb-6">
        <div class="flex items-center justify-between">
          <span class="text-sm text-blue-700 dark:text-blue-300">
            {{ selectedFollowUps.length }} {{ $t('crm.follow_ups_selected') }}
          </span>
          <div class="flex items-center space-x-4">
            <select
              v-model="bulkActionForm.action"
              class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
            >
              <option value="">{{ $t('crm.bulk_actions') }}</option>
              <option value="mark_follow_up_complete">{{ $t('crm.mark_complete') }}</option>
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

      <!-- Follow-ups Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left">
                <input
                  type="checkbox"
                  @change="toggleSelectAll"
                  :checked="selectedFollowUps.length === followUps.data.length && followUps.data.length > 0"
                  class="rounded border-gray-300 dark:border-gray-700"
                />
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('crm.interaction') }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('crm.related_to') }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('crm.follow_up_date') }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.user') }}
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
              v-for="interaction in followUps.data"
              :key="interaction.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  v-model="selectedFollowUps"
                  :value="interaction.id"
                  type="checkbox"
                  class="rounded border-gray-300 dark:border-gray-700"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div>
                    <div class="flex items-center space-x-2 mb-1">
                      <span
                        :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeColor(interaction.type)}`"
                      >
                        <component :is="getTypeIcon(interaction.type)" class="w-3 h-3 mr-1" />
                        {{ interaction.type?.replace('_', ' ').toUpperCase() }}
                      </span>
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ interaction.subject }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDateTime(interaction.interaction_date) }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ getInteractableName(interaction) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :class="`text-sm ${getDueDateColor(interaction.follow_up_date)}`">
                  {{ formatDate(interaction.follow_up_date) }}
                  <div v-if="isOverdue(interaction.follow_up_date)" class="text-xs text-red-600">
                    {{ $t('crm.overdue') }}
                  </div>
                  <div v-else-if="getDaysUntilDue(interaction.follow_up_date) === 0" class="text-xs text-orange-600">
                    {{ $t('crm.due_today') }}
                  </div>
                  <div v-else-if="getDaysUntilDue(interaction.follow_up_date) > 0" class="text-xs text-gray-500">
                    {{ getDaysUntilDue(interaction.follow_up_date) }} {{ $t('crm.days_remaining') }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ interaction.user?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <Link
                  :href="route('interactions.show', interaction.id)"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                  title="View"
                >
                  <IconEye class="w-4 h-4 inline" />
                </Link>
                <button
                  @click="markFollowUpComplete(interaction)"
                  class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                  title="Mark Complete"
                >
                  <IconCheck class="w-4 h-4 inline" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="followUps.data.length === 0" class="p-8 text-center">
          <IconClock class="w-12 h-12 mx-auto text-gray-400 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
            {{ $t('crm.no_follow_ups') }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            {{ $t('crm.no_follow_ups_description') }}
          </p>
          <Link
            :href="route('interactions.index')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.view_all_interactions') }}
          </Link>
        </div>

        <!-- Pagination -->
        <div
          v-if="followUps.links && followUps.data.length > 0"
          class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6"
        >
          <div class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
              <Link
                v-if="followUps.prev_page_url"
                :href="followUps.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                {{ $t('words.previous') }}
              </Link>
              <Link
                v-if="followUps.next_page_url"
                :href="followUps.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                {{ $t('words.next') }}
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  {{ $t('words.showing') }}
                  <span class="font-medium">{{ followUps.from }}</span>
                  {{ $t('words.to') }}
                  <span class="font-medium">{{ followUps.to }}</span>
                  {{ $t('words.of') }}
                  <span class="font-medium">{{ followUps.total }}</span>
                  {{ $t('words.results') }}
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <Link
                    v-for="link in followUps.links"
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
