<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
});

const form = useForm({
  name: '',
  email: '',
  phone: '',
});

const submit = () => {
  form.post(route('clients.store'), {
    onSuccess: () => emit('close'),
  });
};
</script>

<template>
  <DialogModal :show="props.show" @close="emit('close')">
    <template #title>Create Client</template>
    <template #content>
      <form @submit.prevent="submit">
        <div class="mb-4">
          <InputLabel for="name" value="Name" />
          <TextInput
            id="name"
            required
            v-model="form.name"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="mb-4">
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            required
            v-model="form.email"
            type="email"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>
        <div class="mb-4">
          <InputLabel for="phone" value="Phone" />
          <TextInput
            id="phone"
            v-model="form.phone"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>
      </form>
    </template>
    <template #footer>
      <button
        @click="emit('close')"
        class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700"
      >
        Cancel
      </button>
      <button
        @click="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        Create
      </button>
    </template>
  </DialogModal>
</template>
