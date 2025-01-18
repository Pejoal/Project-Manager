<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  projects: {
    type: Array,
    default: [],
  },
  project: {
    type: Object,
    default: {},
  },
});

const form = useForm({
  name: '',
  description: '',
  project_slug: null,
});

const submit = () => {
  const slug = props.project.slug ? props.project.slug : form.project_slug;
  if (!slug) {
    form.setError('project', 'Project is required.');
    return;
  }
  form.post(
    route('phases.store', {
      project: slug,
    }),
    {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    }
  );
};
</script>

<template>
  <DialogModal :show="props.show" @close="emit('close')">
    <template #title>Create Phase</template>
    <template #content>
      <form id="form" @submit.prevent="submit" class="space-y-4">
        <!-- Project Selection -->
        <div>
          <InputLabel for="project" value="Project" />
          <vSelect
            v-if="props.projects.length > 0"
            id="project"
            v-model="form.project_slug"
            :options="props.projects"
            :reduce="(project) => project.slug"
            label="name"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>
          <TextInput
            v-else
            id="project"
            :value="props.project.name"
            readonly
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-gray-200 dark:bg-zinc-600"
          />
          <InputError class="mt-2" :message="form.errors.project" />
        </div>
        <div>
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
        <div>
          <InputLabel for="description" value="Description" />
          <TextInput
            id="description"
            v-model="form.description"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.description" />
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
        form="form"
        :disabled="form.processing"
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        Create
      </button>
    </template>
  </DialogModal>
</template>
