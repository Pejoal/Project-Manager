<script setup>
import { ref, watch } from 'vue';
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
  description: '',
  status_id: null,
  priority_id: null,
  start_date: '',
  end_date: '',
});

const submit = () => {
  form.post(route('projects.store'), {
    onSuccess: () => {
      form.reset();
      emit('close');
    },
  });
};

const endDateError = ref('');

watch([() => form.start_date, () => form.end_date], ([newStartDate, newEndDate]) => {
  if (newEndDate && newStartDate && newEndDate < newStartDate) {
    endDateError.value = 'End date must be after start date';
  } else {
    endDateError.value = '';
  }
});
</script>

<template>
  <DialogModal :show="props.show" @close="emit('close')">
    <template #title>Create Project</template>
    <template #content>
      <form id="form" @submit.prevent="submit" class="space-y-4">
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
          <InputLabel for="start_date" value="Start Date" />
          <TextInput
            id="start_date"
            v-model="form.start_date"
            type="date"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.start_date" />
        </div>
        <div>
          <InputLabel for="end_date" value="End Date" />
          <TextInput
            id="end_date"
            v-model="form.end_date"
            type="date"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.end_date || endDateError" />
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
              <input
                class="vs__search"
                :required="!form.status_id"
                v-bind="attributes"
                v-on="events"
              />
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
              <input
                class="vs__search"
                :required="!form.priority_id"
                v-bind="attributes"
                v-on="events"
              />
            </template>
          </vSelect>
          <InputError class="mt-2" :message="form.errors.priority_id" />
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
