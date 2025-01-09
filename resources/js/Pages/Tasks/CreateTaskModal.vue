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
  project: {
    type: Object,
    default: {},
  },
  projects: {
    type: Array,
    default: [],
  },
  users: {
    type: Array,
    default: [],
  },
});

const form = useForm({
  name: '',
  project_slug: null,
  description: '',
  assigned_to: [],
});

const submit = () => {
  const slug = props.project.slug ? props.project.slug : form.project_slug;
  if (!slug) {
    alert('Project is required');
    return;
  }
  form.post(
    route('tasks.store', {
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
    <template #title>Create Task</template>
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
            class="text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
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
        </div>
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
          <InputLabel for="description" value="Description" />
          <TextInput
            id="description"
            v-model="form.description"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.description" />
        </div>
        <div class="mb-4">
          <InputLabel for="assigned_to" value="Assigned To" />
          <vSelect
            id="assigned_to"
            v-model="form.assigned_to"
            :options="props.users"
            :reduce="(user) => user.id"
            label="name"
            multiple
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>
          <InputError class="mt-2" :message="form.errors.assigned_to" />
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
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        Create
      </button>
    </template>
  </DialogModal>
</template>
<style>
@media (prefers-color-scheme: dark) {
  #project .vs__selected {
    color: white;
  }
  .vs__search {
    color: white;
  }
  #vs2__listbox {
    color: black;
  }
}
</style>
