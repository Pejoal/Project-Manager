<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
  show: Boolean,
  users: Array,
  related: Object,
  relatedType: String,
});

const emit = defineEmits(['close']);

const form = useForm({
  type: 'note',
  direction: 'outbound',
  subject: '',
  description: '',
  interaction_date: new Date().toISOString().slice(0, 16),
  duration_minutes: '',
  outcome: '',
  follow_up_required: false,
  follow_up_date: '',
  follow_up_notes: '',
  contact_details: {},
  attachments: [],
  interactable_type: '',
  interactable_id: '',
});

// Watch for related entity changes
watch(
  () => props.related,
  (newRelated) => {
    if (newRelated) {
      form.interactable_type =
        newRelated.type || `App\\Models\\${props.relatedType?.charAt(0).toUpperCase() + props.relatedType?.slice(1)}`;
      form.interactable_id = newRelated.id;
    }
  },
  { immediate: true }
);

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

// Entity type options for manual selection
const entityTypeOptions = [
  { value: 'App\\Models\\Contact', label: 'Contact' },
  { value: 'App\\Models\\Lead', label: 'Lead' },
  { value: 'App\\Models\\Opportunity', label: 'Opportunity' },
  { value: 'App\\Models\\SupportTicket', label: 'Support Ticket' },
  { value: 'App\\Models\\Campaign', label: 'Campaign' },
];

const submit = () => {
  form.post(route('interactions.store'), {
    onSuccess: () => {
      emit('close');
      form.reset();
    },
  });
};

const close = () => {
  emit('close');
  form.reset();
};

// Auto-generate subject based on type and direction
const generateSubject = () => {
  if (!form.subject) {
    const typeLabel = typeOptions.find((t) => t.value === form.type)?.label || 'Interaction';
    const directionLabel = form.direction === 'inbound' ? 'from' : 'to';
    const entityName = props.related?.name || props.related?.first_name || 'customer';
    form.subject = `${typeLabel} ${directionLabel} ${entityName}`;
  }
};
</script>

<template>
  <Modal :show="show" @close="close" max-width="4xl">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Create New Interaction</h2>
        <button @click="close" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Type -->
          <div>
            <InputLabel for="type" value="Interaction Type" />
            <select
              id="type"
              v-model="form.type"
              @change="generateSubject"
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
              @change="generateSubject"
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
            <option v-for="option in outcomeOptions" :key="option.value" :value="option.value" :class="option.color">
              {{ option.label }}
            </option>
          </select>
          <div v-if="form.errors.outcome" class="text-red-600 text-sm mt-1">
            {{ form.errors.outcome }}
          </div>
        </div>

        <!-- Related Entity -->
        <div v-if="!related" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Entity Type -->
          <div>
            <InputLabel for="interactable_type" value="Related to" />
            <select
              id="interactable_type"
              v-model="form.interactable_type"
              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            >
              <option value="">Select entity type...</option>
              <option v-for="option in entityTypeOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <div v-if="form.errors.interactable_type" class="text-red-600 text-sm mt-1">
              {{ form.errors.interactable_type }}
            </div>
          </div>

          <!-- Entity ID -->
          <div>
            <InputLabel for="interactable_id" value="Entity ID" />
            <TextInput
              id="interactable_id"
              v-model="form.interactable_id"
              type="number"
              min="1"
              class="mt-1 block w-full"
              placeholder="Enter the ID of the related entity"
              required
            />
            <div v-if="form.errors.interactable_id" class="text-red-600 text-sm mt-1">
              {{ form.errors.interactable_id }}
            </div>
          </div>
        </div>

        <!-- Related Entity Display (when provided) -->
        <div v-else class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md">
          <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Related to:</h4>
          <div class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium">{{ relatedType?.charAt(0).toUpperCase() + relatedType?.slice(1) }}:</span>
            {{ related.name || related.first_name + ' ' + related.last_name || related.subject || `#${related.id}` }}
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
          <SecondaryButton @click="close"> Cancel </SecondaryButton>
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
            {{ form.processing ? 'Creating...' : 'Create Interaction' }}
          </PrimaryButton>
        </div>
      </form>
    </div>
  </Modal>
</template>
