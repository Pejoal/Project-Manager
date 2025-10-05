<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { IconArrowLeft, IconCalendar, IconMail, IconPencil, IconPhone, IconUser, IconUsers } from '@tabler/icons-vue';

const props = defineProps({
  interaction: Object,
  users: Array,
});

const form = useForm({
  type: props.interaction.type,
  direction: props.interaction.direction,
  subject: props.interaction.subject,
  description: props.interaction.description,
  interaction_date: props.interaction.interaction_date
    ? new Date(props.interaction.interaction_date).toISOString().slice(0, 16)
    : '',
  duration_minutes: props.interaction.duration_minutes,
  outcome: props.interaction.outcome || '',
  follow_up_required: props.interaction.follow_up_required || false,
  follow_up_date: props.interaction.follow_up_date || '',
  contact_details: props.interaction.contact_details || {},
  attachments: props.interaction.attachments || [],
});

const typeOptions = [
  { value: 'call', label: 'Call', icon: IconPhone },
  { value: 'email', label: 'Email', icon: IconMail },
  { value: 'meeting', label: 'Meeting', icon: IconCalendar },
  { value: 'note', label: 'Note', icon: IconUser },
  { value: 'sms', label: 'SMS', icon: IconPhone },
  { value: 'social_media', label: 'Social Media', icon: IconUsers },
  { value: 'webinar', label: 'Webinar', icon: IconUsers },
  { value: 'demo', label: 'Demo', icon: IconUsers },
  { value: 'other', label: 'Other', icon: IconUser },
];

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

const submit = () => {
  form.patch(route('interactions.update', props.interaction.id), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    },
  });
};
</script>

<template>
  <Head :title="`Edit: ${interaction.subject}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('interactions.show', interaction.id)"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <IconArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ $t('crm.edit_interaction') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ interaction.subject }}
            </p>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('interactions.show', interaction.id)"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('words.view') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                {{ $t('crm.interaction_details') }}
              </h2>

              <!-- Type and Direction -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Type -->
                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ $t('words.type') }} *
                  </label>
                  <select
                    id="type"
                    v-model="form.type"
                    required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :class="{ 'border-red-500': form.errors.type }"
                  >
                    <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>
                  <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                    {{ form.errors.type }}
                  </div>
                </div>

                <!-- Direction -->
                <div>
                  <label for="direction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ $t('crm.direction') }} *
                  </label>
                  <select
                    id="direction"
                    v-model="form.direction"
                    required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :class="{ 'border-red-500': form.errors.direction }"
                  >
                    <option value="inbound">{{ $t('crm.inbound') }}</option>
                    <option value="outbound">{{ $t('crm.outbound') }}</option>
                  </select>
                  <div v-if="form.errors.direction" class="mt-1 text-sm text-red-600">
                    {{ form.errors.direction }}
                  </div>
                </div>
              </div>

              <!-- Subject -->
              <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('words.subject') }} *
                </label>
                <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.subject }"
                  :placeholder="$t('crm.subject_placeholder')"
                />
                <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                  {{ form.errors.subject }}
                </div>
              </div>

              <!-- Description -->
              <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('words.description') }} *
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="6"
                  required
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.description }"
                  :placeholder="$t('crm.description_placeholder')"
                ></textarea>
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </div>
              </div>

              <!-- Date and Duration -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Interaction Date -->
                <div>
                  <label for="interaction_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ $t('crm.interaction_date') }} *
                  </label>
                  <input
                    id="interaction_date"
                    v-model="form.interaction_date"
                    type="datetime-local"
                    required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :class="{ 'border-red-500': form.errors.interaction_date }"
                  />
                  <div v-if="form.errors.interaction_date" class="mt-1 text-sm text-red-600">
                    {{ form.errors.interaction_date }}
                  </div>
                </div>

                <!-- Duration -->
                <div>
                  <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ $t('crm.duration_minutes') }}
                  </label>
                  <input
                    id="duration_minutes"
                    v-model="form.duration_minutes"
                    type="number"
                    min="0"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :class="{ 'border-red-500': form.errors.duration_minutes }"
                    :placeholder="$t('crm.duration_placeholder')"
                  />
                  <div v-if="form.errors.duration_minutes" class="mt-1 text-sm text-red-600">
                    {{ form.errors.duration_minutes }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Related Entity Info -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.related_to') }}
              </h3>
              <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                <div class="flex items-center space-x-3">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ getInteractableName() }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ getInteractableType() }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ $t('crm.related_entity_readonly') }}
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Outcome -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.outcome') }}
              </h3>

              <div class="mb-4">
                <label for="outcome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('crm.interaction_outcome') }}
                </label>
                <select
                  id="outcome"
                  v-model="form.outcome"
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                >
                  <option value="">{{ $t('crm.select_outcome') }}</option>
                  <option value="positive">{{ $t('crm.positive') }}</option>
                  <option value="neutral">{{ $t('crm.neutral') }}</option>
                  <option value="negative">{{ $t('crm.negative') }}</option>
                </select>
              </div>
            </div>

            <!-- Follow-up -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.follow_up') }}
              </h3>

              <div class="mb-4">
                <div class="flex items-center">
                  <input
                    id="follow_up_required"
                    v-model="form.follow_up_required"
                    type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-700"
                  />
                  <label for="follow_up_required" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $t('crm.follow_up_required') }}
                  </label>
                </div>
              </div>

              <div v-if="form.follow_up_required" class="mb-4">
                <label for="follow_up_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('crm.follow_up_date') }}
                </label>
                <input
                  id="follow_up_date"
                  v-model="form.follow_up_date"
                  type="date"
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                />
              </div>

              <div v-if="interaction.follow_up_completed_at" class="text-sm text-green-600 dark:text-green-400">
                {{ $t('crm.follow_up_completed_on') }}
                {{ new Date(interaction.follow_up_completed_at).toLocaleDateString() }}
              </div>
            </div>

            <!-- Interaction History -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.interaction_history') }}
              </h3>
              <div class="space-y-3 text-sm">
                <div>
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.created') }}:</span>
                  <div class="text-gray-900 dark:text-gray-100">
                    {{ new Date(interaction.created_at).toLocaleDateString() }} {{ $t('words.by') }}
                    {{ interaction.user?.name || 'Unknown' }}
                  </div>
                </div>
                <div>
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.updated') }}:</span>
                  <div class="text-gray-900 dark:text-gray-100">
                    {{ new Date(interaction.updated_at).toLocaleDateString() }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <div class="flex space-x-3">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 bg-blue-500 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded inline-flex items-center justify-center"
                >
                  <IconPencil class="w-4 h-4 mr-2" />
                  {{ form.processing ? $t('words.updating') : $t('words.update') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
