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
  campaign: {
    type: Object,
    default: null,
  },
  users: {
    type: Array,
    default: () => [],
  },
});

const isEditing = computed(() => !!props.campaign);

const form = useForm({
  name: '',
  description: '',
  type: 'email',
  status: 'draft',
  budget: '',
  actual_cost: 0,
  start_date: '',
  end_date: '',
  target_audience_size: 0,
  actual_audience_size: 0,
  leads_generated: 0,
  opportunities_created: 0,
  revenue_generated: 0,
  goals: [],
  channels: [],
  demographics: {},
  content: '',
  created_by: null,
});

// Watch for campaign changes to populate form when editing
watch(
  () => props.campaign,
  (newCampaign) => {
    if (newCampaign) {
      Object.keys(form.data()).forEach((key) => {
        if (key === 'created_by') {
          form[key] = newCampaign.created_by?.id || null;
        } else if (key === 'goals' || key === 'channels') {
          form[key] = newCampaign[key] || [];
        } else if (key === 'demographics') {
          form[key] = newCampaign[key] || {};
        } else {
          form[key] = newCampaign[key] || (typeof form[key] === 'number' ? 0 : '');
        }
      });
    } else {
      form.reset();
    }
  },
  { immediate: true }
);

const submit = () => {
  if (isEditing.value) {
    form.put(route('campaigns.update', props.campaign.id), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  } else {
    form.post(route('campaigns.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  }
};

const typeOptions = [
  { value: 'email', label: 'Email Campaign' },
  { value: 'social_media', label: 'Social Media' },
  { value: 'webinar', label: 'Webinar' },
  { value: 'trade_show', label: 'Trade Show' },
  { value: 'direct_mail', label: 'Direct Mail' },
  { value: 'telemarketing', label: 'Telemarketing' },
  { value: 'other', label: 'Other' },
];

const statusOptions = [
  { value: 'draft', label: 'Draft' },
  { value: 'active', label: 'Active' },
  { value: 'paused', label: 'Paused' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
];

const channelOptions = [
  { value: 'email', label: 'Email' },
  { value: 'facebook', label: 'Facebook' },
  { value: 'instagram', label: 'Instagram' },
  { value: 'linkedin', label: 'LinkedIn' },
  { value: 'twitter', label: 'Twitter' },
  { value: 'google_ads', label: 'Google Ads' },
  { value: 'print', label: 'Print' },
  { value: 'radio', label: 'Radio' },
  { value: 'tv', label: 'Television' },
  { value: 'outdoor', label: 'Outdoor' },
];

const goalOptions = [
  { value: 'brand_awareness', label: 'Brand Awareness' },
  { value: 'lead_generation', label: 'Lead Generation' },
  { value: 'customer_acquisition', label: 'Customer Acquisition' },
  { value: 'customer_retention', label: 'Customer Retention' },
  { value: 'sales', label: 'Sales' },
  { value: 'engagement', label: 'Engagement' },
  { value: 'website_traffic', label: 'Website Traffic' },
];
</script>

<template>
  <DialogModal :show="show" @close="emit('close')" max-width="5xl">
    <template #title>
      {{ isEditing ? $t('crm.campaigns.edit') : $t('crm.campaigns.create') }}
    </template>

    <template #content>
      <form id="campaign-form" @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.basic_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <InputLabel for="name" :value="$t('crm.campaign_name')" />
              <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
              <InputLabel for="type" :value="$t('crm.campaign_type')" />
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
            <div class="md:col-span-2">
              <InputLabel for="description" :value="$t('words.description')" />
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                :placeholder="$t('crm.campaign_description_placeholder')"
              ></textarea>
              <InputError class="mt-2" :message="form.errors.description" />
            </div>
          </div>
        </div>

        <!-- Budget & Timeline -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.budget_timeline') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="budget" :value="$t('crm.budget')" />
              <TextInput
                id="budget"
                v-model="form.budget"
                type="number"
                step="0.01"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.budget" />
            </div>
            <div>
              <InputLabel for="actual_cost" :value="$t('crm.actual_cost')" />
              <TextInput
                id="actual_cost"
                v-model="form.actual_cost"
                type="number"
                step="0.01"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.actual_cost" />
            </div>
            <div>
              <InputLabel for="start_date" :value="$t('crm.start_date')" />
              <TextInput id="start_date" v-model="form.start_date" type="date" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.start_date" />
            </div>
            <div>
              <InputLabel for="end_date" :value="$t('crm.end_date')" />
              <TextInput id="end_date" v-model="form.end_date" type="date" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.end_date" />
            </div>
          </div>
        </div>

        <!-- Campaign Goals & Channels -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.goals_channels') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="goals" :value="$t('crm.campaign_goals')" />
              <vSelect
                id="goals"
                v-model="form.goals"
                :options="goalOptions"
                :reduce="(goal) => goal.value"
                label="label"
                multiple
                :placeholder="$t('crm.select_goals')"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.goals" />
            </div>
            <div>
              <InputLabel for="channels" :value="$t('crm.marketing_channels')" />
              <vSelect
                id="channels"
                v-model="form.channels"
                :options="channelOptions"
                :reduce="(channel) => channel.value"
                label="label"
                multiple
                :placeholder="$t('crm.select_channels')"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.channels" />
            </div>
          </div>
        </div>

        <!-- Target Audience -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.target_audience') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="target_audience_size" :value="$t('crm.target_audience_size')" />
              <TextInput
                id="target_audience_size"
                v-model="form.target_audience_size"
                type="number"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.target_audience_size" />
            </div>
            <div>
              <InputLabel for="actual_audience_size" :value="$t('crm.actual_audience_size')" />
              <TextInput
                id="actual_audience_size"
                v-model="form.actual_audience_size"
                type="number"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.actual_audience_size" />
            </div>
          </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.performance_metrics') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <InputLabel for="leads_generated" :value="$t('crm.leads_generated')" />
              <TextInput
                id="leads_generated"
                v-model="form.leads_generated"
                type="number"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.leads_generated" />
            </div>
            <div>
              <InputLabel for="opportunities_created" :value="$t('crm.opportunities_created')" />
              <TextInput
                id="opportunities_created"
                v-model="form.opportunities_created"
                type="number"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.opportunities_created" />
            </div>
            <div>
              <InputLabel for="revenue_generated" :value="$t('crm.revenue_generated')" />
              <TextInput
                id="revenue_generated"
                v-model="form.revenue_generated"
                type="number"
                step="0.01"
                min="0"
                class="mt-1 block w-full"
              />
              <InputError class="mt-2" :message="form.errors.revenue_generated" />
            </div>
          </div>
        </div>

        <!-- Campaign Content -->
        <div>
          <InputLabel for="content" :value="$t('crm.campaign_content')" />
          <textarea
            id="content"
            v-model="form.content"
            rows="6"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            :placeholder="$t('crm.campaign_content_placeholder')"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.content" />
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
        form="campaign-form"
        :disabled="form.processing"
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        {{ isEditing ? $t('words.update') : $t('words.create') }}
      </button>
    </template>
  </DialogModal>
</template>
