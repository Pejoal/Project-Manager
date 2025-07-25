<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { computed, defineProps, ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  task: Object,
  assigned_to: Object,
  project: Object,
  users: Array,
  statuses: Array,
  priorities: Array,
});

const form = useForm({
  name: props.task.name,
  description: props.task.description,
  assigned_to: props.task.assigned_to ? props.task.assigned_to.map((user) => user.id) : [],
  status_id: props.task.status_id,
  priority_id: props.task.priority_id,
  phase_id: props.task.phase_id,
  milestone_id: props.task.milestone_id,
  start_datetime: props.task.start_datetime,
  end_datetime: props.task.end_datetime,
});

const endDateTimeError = ref('');

watch([() => form.start_datetime, () => form.end_datetime], ([newStartDateTime, newEndDateTime]) => {
  if (newEndDateTime && newStartDateTime && newEndDateTime < newStartDateTime) {
    endDateTimeError.value = 'End datetime must be after start datetime';
  } else {
    endDateTimeError.value = '';
  }
});
const milestones = computed(() => {
  if (form.phase_id) {
    const selectedPhase = props.project.phases.find((phase) => phase.id === form.phase_id);
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
  form.put(route('tasks.update', { project: props.project.slug, task: props.task.id }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="`Edit Task - ${task.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Task</h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <form @submit.prevent="submit">
        <div class="mb-4">
          <InputLabel for="id" value="ID" />
          <TextInput
            id="id"
            :value="task.id"
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
        <div>
          <InputLabel for="status" value="Status" />
          <vSelect
            v-if="props.statuses.length > 0"
            id="status"
            v-model="form.status_id"
            :options="props.statuses"
            :reduce="(status) => status.id"
            label="name"
            class="single-select text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>
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
            class="single-select text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>
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
        <div class="mb-4">
          <InputLabel for="start_date" value="Start Date" />
          <TextInput
            id="start_date"
            v-model="form.start_datetime"
            type="datetime-local"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.start_datetime" />
        </div>
        <div class="mb-4">
          <InputLabel for="end_date" value="End Date" />
          <TextInput
            id="end_date"
            v-model="form.end_datetime"
            type="datetime-local"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.end_datetime || endDateTimeError" />
        </div>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 mt-4 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
        >
          Update
        </button>
        <Link
          :href="route('tasks.show', { project: project.slug, task: task.id })"
          class="ml-4 text-blue-500 dark:text-blue-400 hover:underline"
        >
          Show Task
        </Link>
      </form>
    </div>
  </AppLayout>
</template>
