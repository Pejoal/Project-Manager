<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  project: Object,
  clients: Object,
  statuses: Array,
  priorities: Array,
});

const form = useForm({
  name: props.project.name,
  description: props.project.description,
  clients: props.project.clients ? props.project.clients.map((client) => client.id) : [],
  status_id: props.project.status_id,
  priority_id: props.project.priority_id,
  start_date: props.project.start_date,
  end_date: props.project.end_date,
});

const endDateError = ref('');

watch([() => form.start_date, () => form.end_date], ([newStartDate, newEndDate]) => {
  if (newEndDate && newStartDate && newEndDate < newStartDate) {
    endDateError.value = 'End date must be after start date';
  } else {
    endDateError.value = '';
  }
});

const submit = () => {
  form.put(route('projects.update', { project: props.project.slug }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="`${trans('words.edit')} ${trans('words.project')} - ${project.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ trans('words.edit') }} {{ trans('words.project') }}
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <form @submit.prevent="submit">
        <div class="mb-4">
          <InputLabel for="id" :value="trans('words.id')" />
          <TextInput
            id="id"
            :value="project.id"
            readonly
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-gray-200 dark:bg-zinc-600"
          />
        </div>
        <div class="mb-4">
          <InputLabel for="name" :value="trans('words.name')" />
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
          <InputLabel for="description" :value="trans('words.description')" />
          <TextInput
            id="description"
            v-model="form.description"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.description" />
        </div>
        <div class="mb-4">
          <InputLabel for="clients" :value="trans('words.clients')" />
          <vSelect
            id="clients"
            v-model="form.clients"
            :options="props.clients"
            :reduce="(client) => client.id"
            label="name"
            multiple
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            :placeholder="trans('words.select_option')"
          >
          </vSelect>
          <InputError class="mt-2" :message="form.errors.clients" />
        </div>
        <div class="mb-4">
          <InputLabel for="start_date" :value="trans('words.start_date')" />
          <TextInput
            id="start_date"
            v-model="form.start_date"
            type="date"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.start_date" />
        </div>
        <div class="mb-4">
          <InputLabel for="end_date" :value="trans('words.end_date')" />
          <TextInput
            id="end_date"
            v-model="form.end_date"
            type="date"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.end_date || endDateError" />
        </div>
        <div>
          <InputLabel for="status" :value="trans('words.status')" />
          <vSelect
            v-if="props.statuses.length > 0"
            id="status"
            v-model="form.status_id"
            :options="props.statuses"
            :reduce="(status) => status.id"
            label="name"
            class="single-select text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            :placeholder="trans('words.select_option')"
          >
          </vSelect>
        </div>
        <div>
          <InputLabel for="priority" :value="trans('words.priority')" />
          <vSelect
            v-if="props.priorities.length > 0"
            id="priority"
            v-model="form.priority_id"
            :options="props.priorities"
            :reduce="(priority) => priority.id"
            label="name"
            class="single-select text-gray-700 mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            :placeholder="trans('words.select_option')"
          >
          </vSelect>
        </div>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 mt-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
        >
          {{ trans('words.update') }}
        </button>
        <Link
          :href="route('projects.show', { project: project.slug })"
          class="ml-4 text-blue-500 dark:text-blue-400 hover:underline"
        >
          {{ trans('words.show') }} {{ trans('words.project') }}
        </Link>
      </form>
    </div>
  </AppLayout>
</template>
