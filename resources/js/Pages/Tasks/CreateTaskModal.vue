<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import vSelect from 'vue-select';
import { computed, ref, watch } from 'vue';

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
  statuses: {
    type: Array,
    default: [],
  },
  priorities: {
    type: Array,
    default: [],
  },
});

const form = useForm({
  name: '',
  project_slug: null,
  phase_id: null,
  milestone_id: null,
  description: '',
  assigned_to: [],
  status_id: null,
  priority_id: null,
  start_datetime: '',
  end_datetime: '',
});

const endDateTimeError = ref('');

watch([() => form.start_datetime, () => form.end_datetime], ([newStartDateTime, newEndDateTime]) => {
  if (newEndDateTime && newStartDateTime && newEndDateTime < newStartDateTime) {
    endDateTimeError.value = 'End datetime must be after start datetime';
  } else {
    endDateTimeError.value = '';
  }
});

const project = ref(JSON.parse(JSON.stringify(props.project)));

watch(
  () => form.project_slug,
  async (newProjectSlug) => {
    if (newProjectSlug) {
      try {
        const response = await axios.get(route('projects.single', { project: newProjectSlug }));
        project.value = response.data;
        form.phase_id = null;
        form.milestone_id = null;
      } catch (error) {
        console.error('Error fetching project data:', error);
      }
    }
  }
);

const milestones = computed(() => {
  if (form.phase_id) {
    const selectedPhase = project.value.phases.find((phase) => phase.id === form.phase_id);
    return selectedPhase ? selectedPhase.milestones : [];
  }
  return [];
});

watch(
  () => form.phase_id,
  (newPhaseId) => {
    form.milestone_id = null;
  }
);

const submit = () => {
  const slug = props.project.slug ? props.project.slug : form.project_slug;
  if (!slug) {
    form.setError('project', 'Project is required.');
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
          <InputError class="mt-2" :message="form.errors.project_slug" />
        </div>
        <div>
          <InputLabel for="phase" value="Phase" />
          <vSelect
            v-if="project.phases?.length > 0"
            id="phase"
            v-model="form.phase_id"
            :options="project.phases"
            :reduce="(phase) => phase.id"
            label="name"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>

          <InputError class="mt-2" :message="form.errors.phase_id" />
        </div>

        <div>
          <InputLabel for="milestone" value="Milestone" />
          <vSelect
            v-if="milestones.length > 0"
            id="milestone"
            v-model="form.milestone_id"
            :options="milestones"
            :reduce="(milestone) => milestone.id"
            label="name"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>

          <InputError class="mt-2" :message="form.errors.milestone_id" />
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
        <div>
          <InputLabel for="start_datetime" value="Start DateTime" />
          <TextInput
            id="start_datetime"
            v-model="form.start_datetime"
            type="datetime-local"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.start_datetime" />
        </div>
        <div>
          <InputLabel for="end_datetime" value="End DateTime" />
          <TextInput
            id="end_datetime"
            v-model="form.end_datetime"
            type="datetime-local"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.end_datetime || endDateTimeError" />
        </div>
        <div>
          <InputLabel for="status" value="Status" />
          <vSelect
            v-if="props.statuses.length > 0"
            id="status"
            v-model="form.status_id"
            :options="props.statuses"
            :reduce="(status) => status.id"
            label="name"
            class="text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
            <template #search="{ attributes, events }">
              <input class="vs__search" :required="!form.status_id" v-bind="attributes" v-on="events" />
            </template>
          </vSelect>
          <InputError class="mt-2" :message="form.errors.status_id" />
        </div>
        <div>
          <InputLabel for="priority" value="Priority" />
          <vSelect
            v-if="props.priorities.length > 0"
            id="priority"
            v-model="form.priority_id"
            :options="props.priorities"
            :reduce="(priority) => priority.id"
            label="name"
            class="text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
            <template #search="{ attributes, events }">
              <input class="vs__search" :required="!form.priority_id" v-bind="attributes" v-on="events" />
            </template>
          </vSelect>
          <InputError class="mt-2" :message="form.errors.priority_id" />
        </div>
        <div>
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
        :disabled="form.processing"
        type="submit"
        class="ms-3 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        Create
      </button>
    </template>
  </DialogModal>
</template>
