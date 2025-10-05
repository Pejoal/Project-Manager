<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import vSelect from 'vue-select';

const emit = defineEmits(['close', 'refresh']);

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  contacts: {
    type: Array,
    default: () => [],
  },
  users: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  subject: '',
  description: '',
  priority: 'medium',
  status: 'open',
  contact_id: null,
  assigned_to: null,
  category: '',
});

const priorities = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'urgent', label: 'Urgent' },
];

const statuses = [
  { value: 'open', label: 'Open' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'pending', label: 'Pending' },
  { value: 'resolved', label: 'Resolved' },
  { value: 'closed', label: 'Closed' },
];

const categories = [
  { value: 'technical', label: 'Technical Support' },
  { value: 'billing', label: 'Billing' },
  { value: 'general', label: 'General Inquiry' },
  { value: 'feature_request', label: 'Feature Request' },
  { value: 'bug_report', label: 'Bug Report' },
];

const submit = () => {
  form.post(route('support-tickets.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      emit('close');
      emit('refresh');
    },
  });
};

const close = () => {
  form.reset();
  emit('close');
};
</script>

<template>
  <DialogModal :show="show" @close="close" max-width="2xl">
    <template #title>
      {{ $t('words.create_support_ticket') }}
    </template>

    <template #content>
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Subject -->
        <div>
          <InputLabel for="subject" :value="$t('words.subject')" />
          <TextInput
            id="subject"
            v-model="form.subject"
            type="text"
            class="mt-1 block w-full"
            required
            :placeholder="$t('words.enter_ticket_subject')"
          />
          <InputError class="mt-2" :message="form.errors.subject" />
        </div>

        <!-- Description -->
        <div>
          <InputLabel for="description" :value="$t('words.description')" />
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            required
            :placeholder="$t('words.describe_the_issue')"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <!-- Priority -->
        <div>
          <InputLabel for="priority" :value="$t('words.priority')" />
          <vSelect
            id="priority"
            v-model="form.priority"
            :options="priorities"
            :reduce="(option) => option.value"
            label="label"
            class="mt-1"
            :placeholder="$t('words.select_priority')"
          />
          <InputError class="mt-2" :message="form.errors.priority" />
        </div>

        <!-- Status -->
        <div>
          <InputLabel for="status" :value="$t('words.status')" />
          <vSelect
            id="status"
            v-model="form.status"
            :options="statuses"
            :reduce="(option) => option.value"
            label="label"
            class="mt-1"
            :placeholder="$t('words.select_status')"
          />
          <InputError class="mt-2" :message="form.errors.status" />
        </div>

        <!-- Category -->
        <div>
          <InputLabel for="category" :value="$t('words.category')" />
          <vSelect
            id="category"
            v-model="form.category"
            :options="categories"
            :reduce="(option) => option.value"
            label="label"
            class="mt-1"
            :placeholder="$t('words.select_category')"
          />
          <InputError class="mt-2" :message="form.errors.category" />
        </div>

        <!-- Contact -->
        <div v-if="contacts.length > 0">
          <InputLabel for="contact_id" :value="$t('words.contact')" />
          <vSelect
            id="contact_id"
            v-model="form.contact_id"
            :options="contacts"
            :reduce="(contact) => contact.id"
            label="name"
            class="mt-1"
            :placeholder="$t('words.select_contact')"
          />
          <InputError class="mt-2" :message="form.errors.contact_id" />
        </div>

        <!-- Assigned To -->
        <div v-if="users.length > 0">
          <InputLabel for="assigned_to" :value="$t('words.assigned_to')" />
          <vSelect
            id="assigned_to"
            v-model="form.assigned_to"
            :options="users"
            :reduce="(user) => user.id"
            label="name"
            class="mt-1"
            :placeholder="$t('words.select_user')"
          />
          <InputError class="mt-2" :message="form.errors.assigned_to" />
        </div>
      </form>
    </template>

    <template #footer>
      <SecondaryButton @click="close">
        {{ $t('words.cancel') }}
      </SecondaryButton>

      <PrimaryButton
        class="ms-3"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="submit"
      >
        {{ $t('words.create_ticket') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
