<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch } from 'vue';
import vSelect from 'vue-select';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  projects: Array,
  task: {
    type: Object,
    default: null,
  },
  timeEntry: {
    type: Object,
    default: null,
  },
});

const tasks = ref([]);
const isLoadingTasks = ref(false);

const form = useForm({
  project_id: props.timeEntry?.project_id || props.task?.project_id || null,
  task_id: props.timeEntry?.task_id || props.task?.id || null,
  start_datetime: props.timeEntry?.start_datetime
    ? props.timeEntry.start_datetime.slice(0, 16)
    : props.task?.start_datetime
      ? props.task.start_datetime.slice(0, 16)
      : '',
  end_datetime: props.timeEntry?.end_datetime
    ? props.timeEntry.end_datetime.slice(0, 16)
    : props.task?.end_datetime
      ? props.task.end_datetime.slice(0, 16)
      : '',
  description: props.timeEntry?.description || '',
});

// Watch for project selection to fetch its tasks
watch(
  () => form.project_id,
  async (newProjectId) => {
    if (newProjectId) {
      isLoadingTasks.value = true;
      form.task_id = null;
      try {
        const response = await axios.get(route('time-entries.tasks-for-project', { project: newProjectId }));
        tasks.value = response.data;
      } catch (error) {
        console.error('Error fetching tasks:', error);
        tasks.value = [];
      } finally {
        isLoadingTasks.value = false;
      }
    } else {
      tasks.value = [];
    }
  }
);

// Load tasks when editing
watch(
  () => props.show,
  async (isShowing) => {
    if (isShowing && props.timeEntry && props.timeEntry.project_id) {
      isLoadingTasks.value = true;
      try {
        const response = await axios.get(
          route('time-entries.tasks-for-project', { project: props.timeEntry.project_id })
        );
        tasks.value = response.data;
        form.task_id = props.timeEntry.task_id;
      } catch (error) {
        console.error('Error fetching tasks:', error);
        tasks.value = [];
      } finally {
        isLoadingTasks.value = false;
      }
    }
  }
);

const submit = () => {
  if (props.timeEntry) {
    // Update existing time entry
    form.put(route('time-entries.update', props.timeEntry.id), {
      onSuccess: () => closeModal(),
    });
  } else {
    // Create new time entry
    form.post(route('time-entries.store'), {
      onSuccess: () => closeModal(),
    });
  }
};

const closeModal = () => {
  form.reset();
  form.clearErrors();
  emit('close');
};

const calculateHours = () => {
  if (!form.start_datetime || !form.end_datetime) return '0.00';
  const start = new Date(form.start_datetime);
  const end = new Date(form.end_datetime);
  if (end <= start) return '0.00';
  const diffMs = end - start;
  return (diffMs / (1000 * 60 * 60)).toFixed(2);
};
</script>

<template>
  <DialogModal :show="props.show" @close="closeModal">
    <template #title>{{ timeEntry ? 'Edit Time Entry' : 'Create Time Entry' }}</template>

    <template #content>
      <form
        :id="timeEntry ? 'edit-time-entry-form' : 'create-time-entry-form'"
        @submit.prevent="submit"
        class="space-y-4"
      >
        <div>
          <InputLabel for="project" value="Project" />
          <vSelect
            id="project"
            v-model="form.project_id"
            :options="projects"
            :reduce="(project) => project.id"
            label="name"
            placeholder="Select a project"
            :disabled="!!task || !!timeEntry"
          />
          <InputError class="mt-2" :message="form.errors.project_id" />
        </div>

        <div>
          <InputLabel for="task" value="Task" />
          <vSelect
            id="task"
            v-model="form.task_id"
            :options="tasks"
            :reduce="(task) => task.id"
            label="name"
            placeholder="Select a task"
            :loading="isLoadingTasks"
            :disabled="!form.project_id || !!task"
          />
          <InputError class="mt-2" :message="form.errors.task_id" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <InputLabel for="start_datetime" value="Start Time" />
            <TextInput
              id="start_datetime"
              v-model="form.start_datetime"
              type="datetime-local"
              class="w-full"
              required
            />
            <InputError class="mt-2" :message="form.errors.start_datetime" />
          </div>
          <div>
            <InputLabel for="end_datetime" value="End Time" />
            <TextInput id="end_datetime" v-model="form.end_datetime" type="datetime-local" class="w-full" required />
            <InputError class="mt-2" :message="form.errors.end_datetime" />
          </div>
        </div>

        <div>
          <InputLabel for="description" value="Description" />
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div v-if="form.start_datetime && form.end_datetime" class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
          <strong>Hours Worked:</strong> {{ calculateHours() }}
        </div>
      </form>
    </template>

    <template #footer>
      <button @click="closeModal" class="btn-secondary mr-3 text-gray-900 dark:text-gray-200">Cancel</button>
      <button
        :form="timeEntry ? 'edit-time-entry-form' : 'create-time-entry-form'"
        type="submit"
        :disabled="form.processing"
        class="text-gray-900 dark:text-gray-200"
      >
        {{ timeEntry ? 'Update' : 'Create' }}
      </button>
    </template>
  </DialogModal>
</template>
