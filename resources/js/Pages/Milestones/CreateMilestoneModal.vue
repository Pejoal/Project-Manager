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
  project: {
    type: Object,
    default: {},
  },
  phases: Array,
});

const form = useForm({
  name: '',
  description: '',
  phase_id: '',
});

const submit = () => {
  if (!props.project.slug) {
    form.setError('project', 'Project is required.');
    return;
  }
  if (!form.phase_id) {
    form.setError('phase', 'Phase is required.');
    return;
  }
  form.post(
    route('milestones.store', {
      project: props.project.slug,
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
    <template #title>Create Milestone</template>
    <template #content>
      <form id="form" @submit.prevent="submit" class="space-y-4">
        <!-- Project Selection -->
        <div>
          <InputLabel for="project" value="Project" />
          <TextInput
            id="project"
            :value="props.project.name"
            readonly
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-gray-200 dark:bg-zinc-600"
          />
          <InputError class="mt-2" :message="form.errors.project" />
        </div>
        <div>
          <InputLabel for="phase" value="Phase" />
          <vSelect
            v-if="props.phases.length > 0"
            id="phase"
            v-model="form.phase_id"
            :options="props.phases"
            :reduce="(phase) => phase.id"
            label="name"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>

          <InputError class="mt-2" :message="form.errors.phase" />
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
