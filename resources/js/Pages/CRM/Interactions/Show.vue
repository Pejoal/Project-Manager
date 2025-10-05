<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { IconArrowLeft, IconCalendar, IconCheck, IconClock, IconMail, IconPhone, IconUser } from '@tabler/icons-vue';

const props = defineProps({
  interaction: Object,
});

const markFollowUpCompleteForm = useForm({});

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};

const formatDateTime = (datetime) => {
  if (!datetime) return '';
  return new Date(datetime).toLocaleString();
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

const getOutcomeColor = (outcome) => {
  const colors = {
    positive: 'bg-green-100 text-green-800',
    neutral: 'bg-yellow-100 text-yellow-800',
    negative: 'bg-red-100 text-red-800',
  };
  return colors[outcome] || 'bg-gray-100 text-gray-800';
};

const getInteractableName = () => {
  const interactable = props.interaction.interactable;
  if (!interactable) return 'Unknown';

  if (props.interaction.interactable_type === 'App\\Models\\Contact') {
    return `${interactable.first_name || ''} ${interactable.last_name || ''}`.trim() || 'Unknown Contact';
  }

  return interactable.name || interactable.subject || 'Unknown';
};

const getInteractableType = () => {
  const type = props.interaction.interactable_type;
  if (!type) return 'Unknown';

  return type.split('\\').pop();
};

const markFollowUpComplete = () => {
  if (confirm('Mark follow-up as complete?')) {
    markFollowUpCompleteForm.patch(route('interactions.mark-follow-up-complete', props.interaction.id), {
      preserveState: true,
    });
  }
};

const deleteInteraction = () => {
  if (confirm('Are you sure you want to delete this interaction?')) {
    useForm().delete(route('interactions.destroy', props.interaction.id));
  }
};
</script>

<template>
  <Head :title="interaction.subject" />

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
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ interaction.subject }}
            </h1>
            <div class="flex items-center space-x-4 mt-2">
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
              <span
                v-if="interaction.outcome"
                :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getOutcomeColor(interaction.outcome)}`"
              >
                {{ interaction.outcome?.toUpperCase() }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('interactions.edit', interaction.id)"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('words.edit') }}
          </Link>
          <button @click="deleteInteraction" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            {{ $t('words.delete') }}
          </button>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3">
          <!-- Interaction Details -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-6">
              <div class="flex items-center space-x-4">
                <span>{{ $t('words.by') }} {{ interaction.user?.name || 'Unknown' }}</span>
                <span>{{ formatDateTime(interaction.interaction_date) }}</span>
                <span v-if="interaction.duration_minutes" class="flex items-center">
                  <IconClock class="w-4 h-4 mr-1" />
                  {{ interaction.duration_minutes }} {{ $t('crm.minutes') }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <div class="prose prose-lg max-w-none dark:prose-invert">
              <div class="whitespace-pre-wrap">{{ interaction.description }}</div>
            </div>
          </div>

          <!-- Contact Details -->
          <div
            v-if="interaction.contact_details && Object.keys(interaction.contact_details).length > 0"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.contact_details') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="(value, key) in interaction.contact_details" :key="key">
                <div class="text-sm">
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ key }}:</span>
                  <span class="text-gray-900 dark:text-gray-100 ml-2">{{ value }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Attachments -->
          <div
            v-if="interaction.attachments && interaction.attachments.length > 0"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.attachments') }}
            </h3>
            <div class="space-y-2">
              <div
                v-for="attachment in interaction.attachments"
                :key="attachment.name"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md"
              >
                <div class="flex items-center">
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ attachment.name }}
                  </span>
                  <span class="ml-2 text-xs text-gray-500 dark:text-gray-400"> ({{ attachment.size }}) </span>
                </div>
                <a
                  :href="attachment.url"
                  target="_blank"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                >
                  {{ $t('words.download') }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Interaction Info -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.interaction_info') }}
            </h3>
            <div class="space-y-3 text-sm">
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.type') }}:</span>
                <div class="mt-1">
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeColor(interaction.type)}`"
                  >
                    <component :is="getTypeIcon(interaction.type)" class="w-3 h-3 mr-1" />
                    {{ interaction.type?.replace('_', ' ').toUpperCase() }}
                  </span>
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.direction') }}:</span>
                <div class="mt-1">
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getDirectionColor(interaction.direction)}`"
                  >
                    {{ interaction.direction?.toUpperCase() }}
                  </span>
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.date') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(interaction.interaction_date) }}
                </div>
              </div>
              <div v-if="interaction.duration_minutes">
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.duration') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ interaction.duration_minutes }} {{ $t('crm.minutes') }}
                </div>
              </div>
              <div v-if="interaction.outcome">
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.outcome') }}:</span>
                <div class="mt-1">
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getOutcomeColor(interaction.outcome)}`"
                  >
                    {{ interaction.outcome?.toUpperCase() }}
                  </span>
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.user') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ interaction.user?.name || 'Unknown' }}
                </div>
              </div>
            </div>
          </div>

          <!-- Related Entity -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.related_to') }}
            </h3>
            <div class="space-y-3 text-sm">
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.type') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ getInteractableType() }}
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.name') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ getInteractableName() }}
                </div>
              </div>
            </div>
          </div>

          <!-- Follow-up -->
          <div v-if="interaction.follow_up_required" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.follow_up') }}
            </h3>
            <div class="space-y-3 text-sm">
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.follow_up_date') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ formatDate(interaction.follow_up_date) }}
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.status') }}:</span>
                <div class="mt-1">
                  <span
                    v-if="interaction.follow_up_completed_at"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                  >
                    <IconCheck class="w-3 h-3 mr-1" />
                    {{ $t('crm.completed') }}
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                  >
                    <IconClock class="w-3 h-3 mr-1" />
                    {{ $t('crm.pending') }}
                  </span>
                </div>
              </div>
              <div v-if="!interaction.follow_up_completed_at" class="pt-2">
                <button
                  @click="markFollowUpComplete"
                  :disabled="markFollowUpCompleteForm.processing"
                  class="w-full bg-green-500 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded text-sm"
                >
                  {{ $t('crm.mark_complete') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.quick_actions') }}
            </h3>
            <div class="space-y-2">
              <Link
                :href="
                  route('interactions.create', {
                    type: getInteractableType().toLowerCase(),
                    id: interaction.interactable_id,
                  })
                "
                class="block text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ $t('crm.add_new_interaction') }}
              </Link>
              <Link
                :href="route('interactions.index', { interactable_type: interaction.interactable_type })"
                class="block text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ $t('crm.view_all_interactions') }}
              </Link>
              <Link
                :href="route('interactions.analytics')"
                class="block text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ $t('crm.view_analytics') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
