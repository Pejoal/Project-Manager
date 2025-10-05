<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import vSelect from 'vue-select';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  opportunity: {
    type: Object,
    default: null,
  },
  users: {
    type: Array,
    default: () => [],
  },
  contacts: {
    type: Array,
    default: () => [],
  },
});

const isEditing = computed(() => !!props.opportunity);

const form = useForm({
  name: '',
  description: '',
  value: '',
  probability: 10,
  stage: 'prospecting',
  expected_close_date: '',
  actual_close_date: '',
  type: 'new_business',
  lead_source: '',
  next_steps: '',
  competitors: [],
  loss_reason: '',
  contact_id: null,
  assigned_to: null,
});

// Watch for opportunity changes to populate form when editing
watch(
  () => props.opportunity,
  (newOpportunity) => {
    if (newOpportunity) {
      form.name = newOpportunity.name || '';
      form.description = newOpportunity.description || '';
      form.value = newOpportunity.value || '';
      form.probability = newOpportunity.probability || 10;
      form.stage = newOpportunity.stage || 'prospecting';
      form.expected_close_date = newOpportunity.expected_close_date || '';
      form.actual_close_date = newOpportunity.actual_close_date || '';
      form.type = newOpportunity.type || 'new_business';
      form.lead_source = newOpportunity.lead_source || '';
      form.next_steps = newOpportunity.next_steps || '';
      form.competitors = newOpportunity.competitors || [];
      form.loss_reason = newOpportunity.loss_reason || '';
      form.contact_id = newOpportunity.contact_id || null;
      form.assigned_to = newOpportunity.assigned_to?.id || null;
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

const submit = () => {
  if (isEditing.value) {
    form.put(route('opportunities.update', props.opportunity.id), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  } else {
    form.post(route('opportunities.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  }
};

const stageOptions = [
  { value: 'prospecting', label: 'Prospecting' },
  { value: 'qualification', label: 'Qualification' },
  { value: 'needs_analysis', label: 'Needs Analysis' },
  { value: 'proposal', label: 'Proposal' },
  { value: 'negotiation', label: 'Negotiation' },
  { value: 'closed_won', label: 'Closed Won' },
  { value: 'closed_lost', label: 'Closed Lost' },
];

const typeOptions = [
  { value: 'new_business', label: 'New Business' },
  { value: 'existing_business', label: 'Existing Business' },
  { value: 'renewal', label: 'Renewal' },
];

const leadSourceOptions = [
  { value: 'website', label: 'Website' },
  { value: 'referral', label: 'Referral' },
  { value: 'social_media', label: 'Social Media' },
  { value: 'email_campaign', label: 'Email Campaign' },
  { value: 'cold_call', label: 'Cold Call' },
  { value: 'trade_show', label: 'Trade Show' },
  { value: 'webinar', label: 'Webinar' },
  { value: 'advertisement', label: 'Advertisement' },
  { value: 'other', label: 'Other' },
];

// Auto-update probability based on stage
const updateProbabilityByStage = (stage) => {
  const stageProbabilities = {
    prospecting: 10,
    qualification: 25,
    needs_analysis: 50,
    proposal: 75,
    negotiation: 90,
    closed_won: 100,
    closed_lost: 0,
  };

  form.probability = stageProbabilities[stage] || form.probability;
};

watch(
  () => form.stage,
  (newStage) => {
    updateProbabilityByStage(newStage);
  }
);
</script>

<template>
  <DialogModal :show="show" @close="emit('close')" max-width="5xl">
    <template #title>
      {{ isEditing ? $t('crm.opportunities.edit') : $t('crm.opportunities.create') }}
    </template>

    <template #content>
      <form id="opportunity-form" @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.basic_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <InputLabel for="name" :value="$t('crm.opportunity_name')" />
              <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
              <InputLabel for="contact_id" :value="$t('crm.contact')" />
              <vSelect
                id="contact_id"
                v-model="form.contact_id"
                :options="contacts"
                :reduce="(contact) => contact.id"
                :get-option-label="
                  (contact) => `${contact.first_name} ${contact.last_name} (${contact.company || 'No Company'})`
                "
                :placeholder="$t('crm.select_contact')"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.contact_id" />
            </div>
            <div>
              <InputLabel for="assigned_to" :value="$t('words.assigned_to')" />
              <vSelect
                id="assigned_to"
                v-model="form.assigned_to"
                :options="users"
                :reduce="(user) => user.id"
                label="name"
                :placeholder="$t('words.select_user')"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.assigned_to" />
            </div>
            <div class="md:col-span-2">
              <InputLabel for="description" :value="$t('words.description')" />
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                :placeholder="$t('crm.opportunity_description_placeholder')"
              ></textarea>
              <InputError class="mt-2" :message="form.errors.description" />
            </div>
          </div>
        </div>

        <!-- Sales Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.sales_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <InputLabel for="value" :value="$t('crm.opportunity_value')" />
              <TextInput
                id="value"
                v-model="form.value"
                type="number"
                step="0.01"
                min="0"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.value" />
            </div>
            <div>
              <InputLabel for="stage" :value="$t('crm.sales_stage')" />
              <vSelect
                id="stage"
                v-model="form.stage"
                :options="stageOptions"
                :reduce="(stage) => stage.value"
                label="label"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.stage" />
            </div>
            <div>
              <InputLabel for="probability" :value="$t('crm.probability')" />
              <div class="flex items-center space-x-2">
                <TextInput
                  id="probability"
                  v-model="form.probability"
                  type="number"
                  min="0"
                  max="100"
                  class="mt-1 block w-full"
                />
                <span class="text-sm text-gray-600 dark:text-gray-400">%</span>
              </div>
              <InputError class="mt-2" :message="form.errors.probability" />
            </div>
            <div>
              <InputLabel for="type" :value="$t('words.type')" />
              <vSelect
                id="type"
                v-model="form.type"
                :options="typeOptions"
                :reduce="(type) => type.value"
                label="label"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.type" />
            </div>
            <div>
              <InputLabel for="lead_source" :value="$t('crm.lead_source')" />
              <vSelect
                id="lead_source"
                v-model="form.lead_source"
                :options="leadSourceOptions"
                :reduce="(source) => source.value"
                label="label"
                :placeholder="$t('crm.select_lead_source')"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.lead_source" />
            </div>
            <div>
              <InputLabel for="expected_close_date" :value="$t('crm.expected_close_date')" />
              <TextInput
                id="expected_close_date"
                v-model="form.expected_close_date"
                type="date"
                class="mt-1 block w-full"
                required
              />
              <InputError class="mt-2" :message="form.errors.expected_close_date" />
            </div>
          </div>
        </div>

        <!-- Additional Details -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.additional_details') }}
          </h3>
          <div class="grid grid-cols-1 gap-4">
            <div>
              <InputLabel for="next_steps" :value="$t('crm.next_steps')" />
              <textarea
                id="next_steps"
                v-model="form.next_steps"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                :placeholder="$t('crm.next_steps_placeholder')"
              ></textarea>
              <InputError class="mt-2" :message="form.errors.next_steps" />
            </div>

            <!-- Show loss reason only for closed lost opportunities -->
            <div v-if="form.stage === 'closed_lost'">
              <InputLabel for="loss_reason" :value="$t('crm.loss_reason')" />
              <textarea
                id="loss_reason"
                v-model="form.loss_reason"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                :placeholder="$t('crm.loss_reason_placeholder')"
              ></textarea>
              <InputError class="mt-2" :message="form.errors.loss_reason" />
            </div>

            <!-- Show actual close date for closed opportunities -->
            <div v-if="form.stage === 'closed_won' || form.stage === 'closed_lost'">
              <InputLabel for="actual_close_date" :value="$t('crm.actual_close_date')" />
              <TextInput
                id="actual_close_date"
                v-model="form.actual_close_date"
                type="date"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.actual_close_date" />
            </div>
          </div>
        </div>
      </form>
    </template>

    <template #footer>
      <button
        @click="emit('close')"
        class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700"
      >
        {{ $t('words.cancel') }}
      </button>
      <button
        form="opportunity-form"
        :disabled="form.processing"
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        {{ isEditing ? $t('words.update') : $t('words.create') }}
      </button>
    </template>
  </DialogModal>
</template>
