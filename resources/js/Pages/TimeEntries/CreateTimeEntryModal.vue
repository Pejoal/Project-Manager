<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import vSelect from 'vue-select';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  task: {
    type: Object,
    default: null,
  },
});

const projects = ref([]);
const tasks = ref([]);
const endDateTimeError = ref('');

const form = useForm({
  task_id: props.task?.id || '',
  start_datetime: '',
  end_datetime: '',
  description: '',
});

// Load projects on mount
onMounted(async () => {
  try {
    const response = await axios.get(route('projects.index'));
    projects.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching projects:', error);
  }
});

// Watch for task selection to load project tasks
watch(
  () => form.project_id,
  async (newProjectId) => {
    if (newProjectId) {
      try {
        const response = await axios.get(route('projects.tasks', { project: newProjectId }));
        tasks.value = response.data || [];
        form.task_id = '';
      } catch (error) {
        console.error('Error fetching tasks:', error);
        tasks.value = [];
      }
    } else {
      tasks.value = [];
      form.task_id = '';
    }
  }
);

// Watch for datetime validation
watch([() => form.start_datetime, () => form.end_datetime], ([newStartDateTime, newEndDateTime]) => {
  if (newEndDateTime && newStartDateTime && newEndDateTime < newStartDateTime) {
    endDateTimeError.value = 'End datetime must be after start datetime';
  } else {
    endDateTimeError.value = '';
  }
});

// If task is pre-selected, set up the form
watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      form.task_id = newTask.id;
      form.project_id = newTask.project_id;
      // Pre-fill with task datetime if available
      if (newTask.start_datetime) {
        form.start_datetime = newTask.start_datetime.slice(0, 16); // Format for datetime-local
      }
      if (newTask.end_datetime) {
        form.end_datetime = newTask.end_datetime.slice(0, 16);
      }
    }
  }
);

const submit = () => {
  if (endDateTimeError.value) {
    return;
  }

  form.post(route('time-entries.store'), {
    onSuccess: () => {
      form.reset();
      emit('close');
    },
  });
};

const closeModal = () => {
  form.reset();
  form.clearErrors();
  endDateTimeError.value = '';
  emit('close');
};
</script>

<template>
  <DialogModal :show="props.show" @close="closeModal">
    <template #title>
      {{ $t('payroll.time_entries.create') }}
    </template>

    <template #content>
      <form id="create-time-entry-form" @submit.prevent="submit" class="space-y-4">
        <!-- Project Selection -->
        <div>
          <InputLabel for="project" :value="$t('words.project')" />
          <vSelect
            id="project"
            v-model="form.project_id"
            :options="projects"
            :reduce="(project) => project.id"
            label="name"
            placeholder="Select a project"
            class="text-gray-700"
            :disabled="!!task"
          />
          <InputError class="mt-2" :message="form.errors.project_id" />
        </div>

        <!-- Task Selection -->
        <div>
          <InputLabel for="task" :value="$t('words.task')" />
          <vSelect
            id="task"
            v-model="form.task_id"
            :options="tasks"
            :reduce="(task) => task.id"
            label="name"
            placeholder="Select a task"
            class="text-gray-700"
            :disabled="tasks.length === 0 || !!task"
          />
          <InputError class="mt-2" :message="form.errors.task_id" />
          <p v-if="form.project_id && tasks.length === 0" class="mt-1 text-sm text-gray-500">
            No tasks found for selected project, or you may not be assigned to any tasks in this project.
          </p>
        </div>

        <!-- Start DateTime -->
        <div>
          <InputLabel for="start_datetime" :value="$t('payroll.time_entries.start_datetime')" />
          <TextInput
            id="start_datetime"
            v-model="form.start_datetime"
            type="datetime-local"
            class="mt-1 block w-full"
            required
          />
          <InputError class="mt-2" :message="form.errors.start_datetime" />
        </div>

        <!-- End DateTime -->
        <div>
          <InputLabel for="end_datetime" :value="$t('payroll.time_entries.end_datetime')" />
          <TextInput
            id="end_datetime"
            v-model="form.end_datetime"
            type="datetime-local"
            class="mt-1 block w-full"
            required
          />
          <InputError class="mt-2" :message="form.errors.end_datetime || endDateTimeError" />
        </div>

        <!-- Description -->
        <div>
          <InputLabel for="description" :value="$t('payroll.time_entries.description')" />
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
            placeholder="Optional description of work performed..."
          />
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <!-- Hours Calculation Preview -->
        <div
          v-if="form.start_datetime && form.end_datetime && !endDateTimeError"
          class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
        >
          <div class="text-sm text-gray-600 dark:text-gray-300">
            <strong>{{ $t('payroll.time_entries.hours_worked') }}:</strong>
            {{ calculateHours() }} hours
          </div>
        </div>
      </form>
    </template>

    <template #footer>
      <button
        @click="closeModal"
        class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 mr-3"
      >
        {{ $t('words.cancel') }}
      </button>
      <button
        form="create-time-entry-form"
        type="submit"
        :disabled="form.processing || !!endDateTimeError"
        class="px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-50"
      >
        {{ $t('payroll.actions.create') }}
      </button>
    </template>
  </DialogModal>
</template>

<script>
export default {
  methods: {
    calculateHours() {
      if (!this.form.start_datetime || !this.form.end_datetime) return 0;

      const start = new Date(this.form.start_datetime);
      const end = new Date(this.form.end_datetime);

      if (end <= start) return 0;

      const diffMs = end - start;
      const hours = diffMs / (1000 * 60 * 60);
      return hours.toFixed(2);
    },
  },
};
</script>
