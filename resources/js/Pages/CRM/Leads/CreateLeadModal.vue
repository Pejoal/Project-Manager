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
  lead: {
    type: Object,
    default: null,
  },
  users: {
    type: Array,
    default: () => [],
  },
  campaigns: {
    type: Array,
    default: () => [],
  },
});

const isEditing = computed(() => !!props.lead);

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  company: '',
  job_title: '',
  status: 'new',
  source: 'website',
  score: 0,
  estimated_value: '',
  notes: '',
  assigned_to: null,
  campaign_id: null,
  tags: [],
});

// Watch for lead changes to populate form when editing
watch(
  () => props.lead,
  (newLead) => {
    if (newLead) {
      form.first_name = newLead.first_name || '';
      form.last_name = newLead.last_name || '';
      form.email = newLead.email || '';
      form.phone = newLead.phone || '';
      form.company = newLead.company || '';
      form.job_title = newLead.job_title || '';
      form.status = newLead.status || 'new';
      form.source = newLead.source || 'website';
      form.score = newLead.score || 0;
      form.estimated_value = newLead.estimated_value || '';
      form.notes = newLead.notes || '';
      form.assigned_to = newLead.assigned_to?.id || null;
      form.campaign_id = newLead.campaign_id || null;
      form.tags = newLead.tags || [];
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

const submit = () => {
  if (isEditing.value) {
    form.put(route('leads.update', props.lead.id), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  } else {
    form.post(route('leads.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  }
};

const statusOptions = [
  { value: 'new', label: 'New' },
  { value: 'contacted', label: 'Contacted' },
  { value: 'qualified', label: 'Qualified' },
  { value: 'unqualified', label: 'Unqualified' },
  { value: 'converted', label: 'Converted' },
  { value: 'lost', label: 'Lost' },
];

const sourceOptions = [
  { value: 'website', label: 'Website' },
  { value: 'referral', label: 'Referral' },
  { value: 'social_media', label: 'Social Media' },
  { value: 'email_campaign', label: 'Email Campaign' },
  { value: 'phone_call', label: 'Phone Call' },
  { value: 'trade_show', label: 'Trade Show' },
  { value: 'advertisement', label: 'Advertisement' },
  { value: 'other', label: 'Other' },
];
</script>

<template>
  <DialogModal :show="show" @close="emit('close')" max-width="4xl">
    <template #title>
      {{ isEditing ? $t('crm.leads.edit') : $t('crm.leads.create') }}
    </template>

    <template #content>
      <form id="lead-form" @submit.prevent="submit" class="space-y-6">
        <!-- Personal Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.personal_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="first_name" :value="$t('words.first_name')" />
              <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" required />
              <InputError class="mt-2" :message="form.errors.first_name" />
            </div>
            <div>
              <InputLabel for="last_name" :value="$t('words.last_name')" />
              <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" required />
              <InputError class="mt-2" :message="form.errors.last_name" />
            </div>
            <div>
              <InputLabel for="email" :value="$t('words.email')" />
              <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
              <InputLabel for="phone" :value="$t('words.phone')" />
              <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.phone" />
            </div>
          </div>
        </div>

        <!-- Company Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.company_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="company" :value="$t('words.company')" />
              <TextInput id="company" v-model="form.company" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.company" />
            </div>
            <div>
              <InputLabel for="job_title" :value="$t('words.job_title')" />
              <TextInput id="job_title" v-model="form.job_title" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.job_title" />
            </div>
          </div>
        </div>

        <!-- Lead Details -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.lead_details') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="status" :value="$t('words.status')" />
              <vSelect
                id="status"
                v-model="form.status"
                :options="statusOptions"
                :reduce="(status) => status.value"
                label="label"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.status" />
            </div>
            <div>
              <InputLabel for="source" :value="$t('words.source')" />
              <vSelect
                id="source"
                v-model="form.source"
                :options="sourceOptions"
                :reduce="(source) => source.value"
                label="label"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.source" />
            </div>
            <div>
              <InputLabel for="score" :value="$t('crm.lead_score')" />
              <TextInput id="score" v-model="form.score" type="number" min="0" max="100" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.score" />
            </div>
            <div>
              <InputLabel for="estimated_value" :value="$t('crm.estimated_value')" />
              <TextInput
                id="estimated_value"
                v-model="form.estimated_value"
                type="number"
                step="0.01"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.estimated_value" />
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
            <div>
              <InputLabel for="campaign_id" :value="$t('crm.campaign')" />
              <vSelect
                id="campaign_id"
                v-model="form.campaign_id"
                :options="campaigns"
                :reduce="(campaign) => campaign.id"
                label="name"
                :placeholder="$t('crm.select_campaign')"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.campaign_id" />
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <InputLabel for="notes" :value="$t('words.notes')" />
          <textarea
            id="notes"
            v-model="form.notes"
            rows="4"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            :placeholder="$t('crm.lead_notes_placeholder')"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.notes" />
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
        form="lead-form"
        :disabled="form.processing"
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        {{ isEditing ? $t('words.update') : $t('words.create') }}
      </button>
    </template>
  </DialogModal>
</template>
