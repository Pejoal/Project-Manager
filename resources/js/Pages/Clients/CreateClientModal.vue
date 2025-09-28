<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  projects: {
    type: Array,
    default: [],
  },
});

const form = useForm({
  name: '',
  email: '',
  phone: '',
  projects: [],
});

const submit = () => {
  form.post(route('clients.store'), {
    onSuccess: () => {
      emit('close');
      form.reset();
    },
  });
};
</script>

<template>
  <DialogModal :show="props.show" @close="emit('close')">
    <template #title>{{ trans('words.create_client') }}</template>
    <template #content>
      <form id="form" @submit.prevent="submit">
        <div class="mb-4">
          <InputLabel for="name" :value="trans('words.name')" />
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
          <InputLabel for="email" :value="trans('words.email')" />
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
          <InputLabel for="phone" :value="trans('words.phone')" />
          <TextInput
            id="phone"
            v-model="form.phone"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>
        <div>
          <InputLabel for="projects" :value="trans('words.projects')" />
          <vSelect
            id="projects"
            v-model="form.projects"
            :options="props.projects"
            :reduce="(project) => project.id"
            label="name"
            multiple
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>
          <InputError class="mt-2" :message="form.errors.projects" />
        </div>
      </form>
    </template>
    <template #footer>
      <button
        @click="emit('close')"
        class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700"
      >
        {{ trans('words.cancel') }}
      </button>
      <button
        form="form"
        :disabled="form.processing"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        {{ trans('words.create') }}
      </button>
    </template>
  </DialogModal>
</template>
