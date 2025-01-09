<template>
  <Head :title="`Edit Task - ${task.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Edit Task
      </h1>
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
        <div class="mb-4">
          <InputLabel for="status" value="Status" />
          <select
            id="status"
            v-model="form.status_id"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          >
            <option value="To Do">To Do</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
          </select>
          <InputError class="mt-2" :message="form.errors.status" />
        </div>
        <div class="mb-4">
          <InputLabel for="priority" value="Priority" />
          <select
            id="priority"
            v-model="form.priority_id"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          >
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
          </select>
          <InputError class="mt-2" :message="form.errors.priority" />
        </div>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
        >
          Update
        </button>
      </form>
    </div>
  </AppLayout>
</template>

<style>
@media (prefers-color-scheme: dark) {
  #assigned_to .vs__search {
    color: white;
  }
}
</style>

<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';
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
});

const form = useForm({
  name: props.task.name,
  description: props.task.description,
  assigned_to: props.task.assigned_to
    ? props.task.assigned_to.map((user) => user.id)
    : [],
  status_id: props.task.status_id,
  priority_id: props.task.priority_id,
});

const submit = () => {
  form.put(
    route('tasks.update', { project: props.project.slug, task: props.task.id })
  );
};
</script>
