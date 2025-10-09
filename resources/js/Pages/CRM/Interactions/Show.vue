<script setup>
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
  interaction: Object,
});

const showFollowUpModal = ref(false);

const followUpForm = useForm({
  follow_up_notes: '',
});

const typeIcons = {
  call: '📞',
  email: '📧',
  meeting: '🤝',
  note: '📝',
  sms: '💬',
  social_media: '📱',
  webinar: '💻',
  demo: '🎥',
  other: '📄',
};

const getOutcomeColor = (outcome) => {
  const colors = {
    positive: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    neutral: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    negative: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  };
  return colors[outcome] || colors.neutral;
};

const getDirectionColor = (direction) => {
  const colors = {
    inbound: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    outbound: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
  };
  return colors[direction] || colors.outbound;
};

const formatDuration = (minutes) => {
  if (!minutes) return null;
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
};

const formatDateTime = (datetime) => {
  if (!datetime) return null;
  return new Date(datetime).toLocaleString();
};

const markFollowUpComplete = async () => {
  try {
    await axios.post(route('interactions.mark-follow-up-complete', props.interaction.id));
    // Reload the page to get updated data
    window.location.reload();
  } catch (error) {
    console.error('Error marking follow-up complete:', error);
    alert('Failed to mark follow-up as complete');
  }
};

const getRelatedEntityName = () => {
  const entity = props.interaction.interactable;
  if (!entity) return 'Unknown';

  return (
    entity.name ||
    (entity.first_name && entity.last_name ? `${entity.first_name} ${entity.last_name}` : '') ||
    entity.subject ||
    entity.title ||
    `#${entity.id}`
  );
};

const getRelatedEntityType = () => {
  return props.interaction.interactable_type?.split('\\').pop() || 'Unknown';
};

const isFollowUpOverdue = () => {
  if (!props.interaction.follow_up_date || props.interaction.follow_up_completed_at) {
    return false;
  }
  return new Date(props.interaction.follow_up_date) < new Date();
};
</script>

<template>
  <Head :title="`Interaction: ${interaction.subject}`" />

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <Link
                :href="route('interactions.index')"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </Link>
              <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                  {{ interaction.subject }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Interaction #{{ interaction.id }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('interactions.edit', interaction.id)"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
              >
                Edit
              </Link>
              <SecondaryButton
                v-if="interaction.follow_up_required && !interaction.follow_up_completed_at"
                @click="markFollowUpComplete"
                class="bg-green-500 hover:bg-green-700 text-white"
              >
                Complete Follow-up
              </SecondaryButton>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Basic Information -->
              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Basic Information</h3>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                    <dd class="mt-1 flex items-center space-x-2">
                      <span class="text-lg">{{ typeIcons[interaction.type] || '📄' }}</span>
                      <span class="text-sm font-medium text-gray-900 dark:text-gray-100 capitalize">
                        {{ interaction.type.replace('_', ' ') }}
                      </span>
                    </dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Direction</dt>
                    <dd class="mt-1">
                      <span
                        :class="[
                          'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                          getDirectionColor(interaction.direction),
                        ]"
                      >
                        {{ interaction.direction }}
                      </span>
                    </dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ formatDateTime(interaction.interaction_date) }}
                    </dd>
                  </div>

                  <div v-if="interaction.duration_minutes">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ formatDuration(interaction.duration_minutes) }}
                    </dd>
                  </div>

                  <div v-if="interaction.outcome">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Outcome</dt>
                    <dd class="mt-1">
                      <span
                        :class="[
                          'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                          getOutcomeColor(interaction.outcome),
                        ]"
                      >
                        {{ interaction.outcome }}
                      </span>
                    </dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created by</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ interaction.user?.name || 'Unknown' }}
                    </dd>
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Description</h3>
                <div class="prose dark:prose-invert max-w-none">
                  <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ interaction.description }}</p>
                </div>
              </div>

              <!-- Attendees (for meetings) -->
              <div
                v-if="interaction.attendees && interaction.attendees.length > 0"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6"
              >
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Attendees</h3>
                <div class="space-y-2">
                  <div
                    v-for="(attendee, index) in interaction.attendees"
                    :key="index"
                    class="flex items-center space-x-2"
                  >
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                      />
                    </svg>
                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ attendee }}</span>
                  </div>
                </div>
              </div>

              <!-- Attachments -->
              <div
                v-if="interaction.attachments && interaction.attachments.length > 0"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6"
              >
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Attachments</h3>
                <div class="space-y-2">
                  <div
                    v-for="(attachment, index) in interaction.attachments"
                    :key="index"
                    class="flex items-center space-x-2"
                  >
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                      />
                    </svg>
                    <a
                      href="#"
                      class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      {{ attachment.name || attachment }}
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
              <!-- Related Entity -->
              <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Related To</h3>
                <div class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ getRelatedEntityType() }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ getRelatedEntityName() }}
                    </dd>
                  </div>
                </div>
              </div>

              <!-- Follow-up Information -->
              <div
                v-if="interaction.follow_up_required"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6"
              >
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Follow-up</h3>

                <div class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                      <span
                        v-if="interaction.follow_up_completed_at"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                      >
                        Completed
                      </span>
                      <span
                        v-else-if="isFollowUpOverdue()"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"
                      >
                        Overdue
                      </span>
                      <span
                        v-else
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"
                      >
                        Pending
                      </span>
                    </dd>
                  </div>

                  <div v-if="interaction.follow_up_date">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ formatDateTime(interaction.follow_up_date) }}
                    </dd>
                  </div>

                  <div v-if="interaction.follow_up_completed_at">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ formatDateTime(interaction.follow_up_completed_at) }}
                    </dd>
                  </div>

                  <div v-if="interaction.follow_up_notes">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ interaction.follow_up_notes }}
                    </dd>
                  </div>
                </div>
              </div>

              <!-- Contact Details -->
              <div
                v-if="interaction.contact_details && Object.keys(interaction.contact_details).length > 0"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6"
              >
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Contact Details Used</h3>
                <div class="space-y-3">
                  <div v-for="(value, key) in interaction.contact_details" :key="key">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">
                      {{ key.replace('_', ' ') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ value }}</dd>
                  </div>
                </div>
              </div>

              <!-- Timestamps -->
              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Timestamps</h3>
                <div class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ formatDateTime(interaction.created_at) }}
                    </dd>
                  </div>
                  <div v-if="interaction.updated_at !== interaction.created_at">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                      {{ formatDateTime(interaction.updated_at) }}
                    </dd>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
