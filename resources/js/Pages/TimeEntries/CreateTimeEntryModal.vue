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
  projects: {
    type: Array,
    default: () => [],
  },
  task: {
    type: Object,
    default: null,
  },
  timeEntry: {
    type: Object,
    default: null,
  },
  projectId: {
    type: Number,
    default: null,
  },
  taskId: {
    type: Number,
    default: null,
  },
  startDatetime: {
    type: String,
    default: null,
  },
  endDatetime: {
    type: String,
    default: null,
  },
});

const tasks = ref([]);
const projects = ref([...props.projects]);
const isLoadingTasks = ref(false);
const isLoadingProjects = ref(false);

// Initialize form with provided data or defaults
const initializeForm = () => {
  return {
    project_id: props.timeEntry?.project_id || props.task?.project_id || props.projectId || null,
    task_id: props.timeEntry?.task_id || props.task?.id || props.taskId || null,
    start_datetime:
      props.timeEntry?.start_datetime?.slice(0, 16) ||
      props.task?.start_datetime?.slice(0, 16) ||
      props.startDatetime?.slice(0, 16) ||
      '',
    end_datetime:
      props.timeEntry?.end_datetime?.slice(0, 16) ||
      props.task?.end_datetime?.slice(0, 16) ||
      props.endDatetime?.slice(0, 16) ||
      '',
    description: props.timeEntry?.description || '',
  };
};

const form = useForm(initializeForm());

// Lazy load projects if not provided
const loadProjects = async () => {
  if (projects.value.length > 0) return;

  isLoadingProjects.value = true;
  try {
    const response = await axios.get(route('projects.data'), {
      // params: { per_page: 1000 }, // Get all projects
    });
    // Extract projects from paginated response
    projects.value = response.data || [];
  } catch (error) {
    console.error('Error loading projects:', error);
    projects.value = [];
  } finally {
    isLoadingProjects.value = false;
  }
};

// Watch for project selection to fetch its tasks
watch(
  () => form.project_id,
  async (newProjectId) => {
    if (newProjectId) {
      isLoadingTasks.value = true;
      // Don't clear task_id if it was pre-selected
      const shouldKeepTaskId = form.task_id && (props.task?.id === form.task_id || props.taskId === form.task_id);
      if (!shouldKeepTaskId) {
        form.task_id = null;
      }
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

// Watch for task selection to auto-fill start and end times
watch(
  () => form.task_id,
  (newTaskId) => {
    if (newTaskId && !props.timeEntry) {
      // Find the selected task from the tasks array
      const selectedTask = tasks.value.find(task => task.id === newTaskId);
      
      if (selectedTask) {
        // Auto-fill start and end datetime from the task
        if (selectedTask.start_datetime) {
          form.start_datetime = selectedTask.start_datetime.slice(0, 16);
        }
        if (selectedTask.end_datetime) {
          form.end_datetime = selectedTask.end_datetime.slice(0, 16);
        }
      }
    }
  }
);

// Watch for modal opening to load data and reset form
watch(
  () => props.show,
  async (isShowing) => {
    if (isShowing) {
      // Load projects if not available
      await loadProjects();

      // Reset form with current props
      Object.assign(form, initializeForm());

      // Load tasks if we have a project
      const projectId = props.timeEntry?.project_id || props.task?.project_id || props.projectId;
      if (projectId) {
        isLoadingTasks.value = true;
        try {
          const response = await axios.get(route('time-entries.tasks-for-project', { project: projectId }));
          tasks.value = response.data;

          // Set task_id after tasks are loaded
          if (props.timeEntry?.task_id) {
            form.task_id = props.timeEntry.task_id;
          } else if (props.task?.id) {
            form.task_id = props.task.id;
          } else if (props.taskId) {
            form.task_id = props.taskId;
          }
        } catch (error) {
          console.error('Error fetching tasks:', error);
          tasks.value = [];
        } finally {
          isLoadingTasks.value = false;
        }
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
  tasks.value = [];
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
    <template #title>{{ timeEntry ? $t('payroll.time_entries.edit') : $t('payroll.time_entries.create') }}</template>

    <template #content>
      <form
        :id="timeEntry ? 'edit-time-entry-form' : 'create-time-entry-form'"
        @submit.prevent="submit"
        class="space-y-4"
      >
        <div>
          <InputLabel for="project" :value="$t('words.project')" />
          <vSelect
            id="project"
            v-model="form.project_id"
            :options="projects"
            :reduce="(project) => project.id"
            label="name"
            :placeholder="$t('words.select_project')"
            :disabled="!!task || !!timeEntry || !!projectId"
            :loading="isLoadingProjects"
          />
          <InputError class="mt-2" :message="form.errors.project_id" />
        </div>

        <div>
          <InputLabel for="task" :value="$t('words.task')" />
          <vSelect
            id="task"
            v-model="form.task_id"
            :options="tasks"
            :reduce="(task) => task.id"
            label="name"
            :placeholder="$t('words.select_task')"
            :loading="isLoadingTasks"
            :disabled="!form.project_id || !!task || !!taskId"
          />
          <InputError class="mt-2" :message="form.errors.task_id" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <InputLabel for="start_datetime" :value="$t('payroll.time_entries.start_time')" />
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
            <InputLabel for="end_datetime" :value="$t('payroll.time_entries.end_time')" />
            <TextInput id="end_datetime" v-model="form.end_datetime" type="datetime-local" class="w-full" required />
            <InputError class="mt-2" :message="form.errors.end_datetime" />
          </div>
        </div>

        <div>
          <InputLabel for="description" :value="$t('words.description')" />
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div v-if="form.start_datetime && form.end_datetime" class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
          <strong>{{ $t('payroll.time_entries.hours_worked') }}:</strong> {{ calculateHours() }}
        </div>
      </form>
    </template>

    <template #footer>
      <button @click="closeModal" class="text-gray-900 dark:text-gray-100 mr-3">{{ $t('words.cancel') }}</button>
      <button
        :form="timeEntry ? 'edit-time-entry-form' : 'create-time-entry-form'"
        type="submit"
        :disabled="form.processing"
        class="text-green-600 dark:text-green-500"
      >
        {{ timeEntry ? $t('words.update') : $t('words.create') }}
      </button>
    </template>
  </DialogModal>
</template>
