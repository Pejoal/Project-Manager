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
  contact: {
    type: Object,
    default: null,
  },
  users: {
    type: Array,
    default: () => [],
  },
});

const isEditing = computed(() => !!props.contact);

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  mobile: '',
  company: '',
  job_title: '',
  department: '',
  address: '',
  city: '',
  state: '',
  country: '',
  postal_code: '',
  website: '',
  linkedin: '',
  twitter: '',
  type: 'prospect',
  status: 'active',
  notes: '',
  assigned_to: null,
  birthday: '',
});

// Watch for contact changes to populate form when editing
watch(
  () => props.contact,
  (newContact) => {
    if (newContact) {
      Object.keys(form.data()).forEach((key) => {
        if (key === 'assigned_to') {
          form[key] = newContact.assigned_to?.id || null;
        } else {
          form[key] = newContact[key] || '';
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
    form.put(route('contacts.update', props.contact.id), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  } else {
    form.post(route('contacts.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  }
};

const typeOptions = [
  { value: 'customer', label: 'Customer' },
  { value: 'prospect', label: 'Prospect' },
  { value: 'partner', label: 'Partner' },
  { value: 'vendor', label: 'Vendor' },
  { value: 'other', label: 'Other' },
];

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'do_not_contact', label: 'Do Not Contact' },
];
</script>

<template>
  <DialogModal :show="show" @close="emit('close')" max-width="6xl">
    <template #title>
      {{ isEditing ? $t('crm.contacts.edit') : $t('crm.contacts.create') }}
    </template>

    <template #content>
      <form id="contact-form" @submit.prevent="submit" class="space-y-6">
        <!-- Personal Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.personal_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div>
              <InputLabel for="mobile" :value="$t('crm.mobile')" />
              <TextInput id="mobile" v-model="form.mobile" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.mobile" />
            </div>
            <div>
              <InputLabel for="birthday" :value="$t('crm.birthday')" />
              <TextInput id="birthday" v-model="form.birthday" type="date" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.birthday" />
            </div>
          </div>
        </div>

        <!-- Company Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.company_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div>
              <InputLabel for="department" :value="$t('crm.department')" />
              <TextInput id="department" v-model="form.department" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.department" />
            </div>
            <div>
              <InputLabel for="website" :value="$t('crm.website')" />
              <TextInput id="website" v-model="form.website" type="url" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.website" />
            </div>
            <div>
              <InputLabel for="linkedin" :value="$t('crm.linkedin')" />
              <TextInput id="linkedin" v-model="form.linkedin" type="url" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.linkedin" />
            </div>
            <div>
              <InputLabel for="twitter" :value="$t('crm.twitter')" />
              <TextInput id="twitter" v-model="form.twitter" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.twitter" />
            </div>
          </div>
        </div>

        <!-- Address Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.address_information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <InputLabel for="address" :value="$t('words.address')" />
              <TextInput id="address" v-model="form.address" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.address" />
            </div>
            <div>
              <InputLabel for="city" :value="$t('words.city')" />
              <TextInput id="city" v-model="form.city" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.city" />
            </div>
            <div>
              <InputLabel for="state" :value="$t('words.state')" />
              <TextInput id="state" v-model="form.state" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.state" />
            </div>
            <div>
              <InputLabel for="country" :value="$t('words.country')" />
              <TextInput id="country" v-model="form.country" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.country" />
            </div>
            <div>
              <InputLabel for="postal_code" :value="$t('words.postal_code')" />
              <TextInput id="postal_code" v-model="form.postal_code" type="text" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.postal_code" />
            </div>
          </div>
        </div>

        <!-- Contact Details -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ $t('crm.contact_details') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            :placeholder="$t('crm.contact_notes_placeholder')"
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
        form="contact-form"
        :disabled="form.processing"
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        {{ isEditing ? $t('words.update') : $t('words.create') }}
      </button>
    </template>
  </DialogModal>
</template>
