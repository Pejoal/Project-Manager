<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
  duration_minutes: props.interaction.duration_minutes || '',
  outcome: props.interaction.outcome || '',
  follow_up_required: props.interaction.follow_up_required,
  follow_up_date: props.interaction.follow_up_date
    ? new Date(props.interaction.follow_up_date).toISOString().slice(0, 16)
    : '',
  follow_up_notes: props.interaction.follow_up_notes || '',
  contact_details: props.interaction.contact_details || {},
  attachments: props.interaction.attachments || [],
});

const typeOptions = [
  { value: 'call', label: 'Call', icon: '📞' },
  { value: 'email', label: 'Email', icon: '📧' },
  { value: 'meeting', label: 'Meeting', icon: '🤝' },
  { value: 'note', label: 'Note', icon: '📝' },
  { value: 'sms', label: 'SMS', icon: '💬' },
  { value: 'social_media', label: 'Social Media', icon: '📱' },
  { value: 'webinar', label: 'Webinar', icon: '💻' },
  { value: 'demo', label: 'Demo', icon: '🎥' },
  { value: 'other', label: 'Other', icon: '📄' },
];

const directionOptions = [
  { value: 'inbound', label: 'Inbound', description: 'Customer contacted us' },
  { value: 'outbound', label: 'Outbound', description: 'We contacted customer' },
];

const outcomeOptions = [
  { value: 'positive', label: 'Positive', color: 'text-green-600' },
  { value: 'neutral', label: 'Neutral', color: 'text-gray-600' },
  { value: 'negative', label: 'Negative', color: 'text-red-600' },
];

const submit = () => {
  form.put(route('interactions.update', props.interaction.id), {
    onSuccess: () => {
      // Redirect handled by backend
    },
  });
};

// Contact details management
const contactDetailKey = ref('');
const contactDetailValue = ref('');

const addContactDetail = () => {
  if (contactDetailKey.value && contactDetailValue.value) {
    form.contact_details[contactDetailKey.value] = contactDetailValue.value;
    contactDetailKey.value = '';
    contactDetailValue.value = '';
  }
};

const removeContactDetail = (key) => {
  delete form.contact_details[key];
};

// Attachments management
const newAttachment = ref('');

const addAttachment = () => {
  if (newAttachment.value) {
    form.attachments.push(newAttachment.value);
    newAttachment.value = '';
  }
};

const removeAttachment = (index) => {
  form.attachments.splice(index, 1);
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
</script>

<template>
  <Head :title="`Edit Interaction: ${interaction.subject}`" />

  <div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <Link
                :href="route('interactions.show', interaction.id)"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </Link>
              <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Interaction</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Interaction #{{ interaction.id }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Content -->
        <div class="p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Related Entity (Read-only) -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md">
              <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Related to:</h4>
              <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">{{ getRelatedEntityType() }}:</span>
                {{ getRelatedEntityName() }}
              </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Type -->
              <div>
                <InputLabel for="type" value="Interaction Type" />
                <select
                  id="type"
                  v-model="form.type"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                    {{ option.icon }} {{ option.label }}
                  </option>
                </select>
                <div v-if="form.errors.type" class="text-red-600 text-sm mt-1">
                  {{ form.errors.type }}
                </div>
              </div>

              <!-- Direction -->
              <div>
                <InputLabel for="direction" value="Direction" />
                <select
                  id="direction"
                  v-model="form.direction"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option v-for="option in directionOptions" :key="option.value" :value="option.value">
                    {{ option.label }} - {{ option.description }}
                  </option>
                </select>
                <div v-if="form.errors.direction" class="text-red-600 text-sm mt-1">
                  {{ form.errors.direction }}
                </div>
              </div>
            </div>

            <!-- Subject -->
            <div>
              <InputLabel for="subject" value="Subject" />
              <TextInput
                id="subject"
                v-model="form.subject"
                type="text"
                class="mt-1 block w-full"
                placeholder="Brief description of the interaction"
                required
              />
              <div v-if="form.errors.subject" class="text-red-600 text-sm mt-1">
                {{ form.errors.subject }}
              </div>
            </div>

            <!-- Description -->
            <div>
              <InputLabel for="description" value="Description" />
              <TextArea
                id="description"
                v-model="form.description"
                class="mt-1 block w-full"
                rows="4"
                placeholder="Detailed description of what happened during this interaction..."
                required
              />
              <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">
                {{ form.errors.description }}
              </div>
            </div>

            <!-- Date and Duration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Interaction Date -->
              <div>
                <InputLabel for="interaction_date" value="Interaction Date & Time" />
                <input
                  id="interaction_date"
                  v-model="form.interaction_date"
                  type="datetime-local"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                />
                <div v-if="form.errors.interaction_date" class="text-red-600 text-sm mt-1">
                  {{ form.errors.interaction_date }}
                </div>
              </div>

              <!-- Duration -->
              <div>
                <InputLabel for="duration_minutes" value="Duration (minutes)" />
                <TextInput
                  id="duration_minutes"
                  v-model="form.duration_minutes"
                  type="number"
                  min="0"
                  class="mt-1 block w-full"
                  placeholder="e.g., 30"
                />
                <p class="text-sm text-gray-500 mt-1">Leave empty if not applicable</p>
                <div v-if="form.errors.duration_minutes" class="text-red-600 text-sm mt-1">
                  {{ form.errors.duration_minutes }}
                </div>
              </div>
            </div>

            <!-- Outcome -->
            <div>
              <InputLabel for="outcome" value="Outcome" />
              <select
                id="outcome"
                v-model="form.outcome"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">Select outcome...</option>
                <option
                  v-for="option in outcomeOptions"
                  :key="option.value"
                  :value="option.value"
                  :class="option.color"
                >
                  {{ option.label }}
                </option>
              </select>
              <div v-if="form.errors.outcome" class="text-red-600 text-sm mt-1">
                {{ form.errors.outcome }}
              </div>
            </div>

            <!-- Contact Details -->
            <div>
              <InputLabel value="Contact Details Used" />
              <div class="mt-2 space-y-3">
                <!-- Existing contact details -->
                <div v-if="Object.keys(form.contact_details).length > 0" class="space-y-2">
                  <div
                    v-for="(value, key) in form.contact_details"
                    :key="key"
                    class="flex items-center justify-between bg-gray-50 dark:bg-gray-900 p-3 rounded-md"
                  >
                    <div>
                      <span class="font-medium capitalize">{{ key.replace('_', ' ') }}:</span>
                      <span class="ml-2">{{ value }}</span>
                    </div>
                    <button type="button" @click="removeContactDetail(key)" class="text-red-600 hover:text-red-900">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"
                        />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Add new contact detail -->
                <div class="flex space-x-3">
                  <TextInput
                    v-model="contactDetailKey"
                    type="text"
                    placeholder="Detail type (e.g., phone, email)"
                    class="flex-1"
                  />
                  <TextInput v-model="contactDetailValue" type="text" placeholder="Value" class="flex-1" />
                  <SecondaryButton @click="addContactDetail" type="button"> Add </SecondaryButton>
                </div>
              </div>
            </div>

            <!-- Attachments -->
            <div>
              <InputLabel value="Attachments" />
              <div class="mt-2 space-y-3">
                <!-- Existing attachments -->
                <div v-if="form.attachments.length > 0" class="space-y-2">
                  <div
                    v-for="(attachment, index) in form.attachments"
                    :key="index"
                    class="flex items-center justify-between bg-gray-50 dark:bg-gray-900 p-3 rounded-md"
                  >
                    <span>{{ attachment.name || attachment }}</span>
                    <button type="button" @click="removeAttachment(index)" class="text-red-600 hover:text-red-900">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"
                        />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Add new attachment -->
                <div class="flex space-x-3">
                  <TextInput v-model="newAttachment" type="text" placeholder="Attachment name or URL" class="flex-1" />
                  <SecondaryButton @click="addAttachment" type="button"> Add </SecondaryButton>
                </div>
              </div>
            </div>

            <!-- Follow-up Section -->
            <div class="space-y-4">
              <!-- Follow-up Required -->
              <div class="flex items-center">
                <input
                  id="follow_up_required"
                  v-model="form.follow_up_required"
                  type="checkbox"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <InputLabel for="follow_up_required" value="Follow-up required" class="ml-2" />
              </div>

              <!-- Follow-up Details -->
              <div v-if="form.follow_up_required" class="space-y-4 pl-6 border-l-2 border-indigo-200">
                <!-- Follow-up Date -->
                <div>
                  <InputLabel for="follow_up_date" value="Follow-up Date" />
                  <input
                    id="follow_up_date"
                    v-model="form.follow_up_date"
                    type="datetime-local"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.follow_up_date" class="text-red-600 text-sm mt-1">
                    {{ form.errors.follow_up_date }}
                  </div>
                </div>

                <!-- Follow-up Notes -->
                <div>
                  <InputLabel for="follow_up_notes" value="Follow-up Notes" />
                  <Textarea
                    id="follow_up_notes"
                    v-model="form.follow_up_notes"
                    class="mt-1 block w-full"
                    rows="3"
                    placeholder="What needs to be done in the follow-up?"
                  />
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
              <Link
                :href="route('interactions.show', interaction.id)"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Cancel
              </Link>
              <PrimaryButton :disabled="form.processing">
                <svg
                  v-if="form.processing"
                  class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                {{ form.processing ? 'Updating...' : 'Update Interaction' }}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
