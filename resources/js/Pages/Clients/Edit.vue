<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  client: Object,
  projects: Object,
});

const form = useForm({
  name: props.client.name,
  email: props.client.email,
  phone: props.client.phone,
  projects: props.client.projects ? props.client.projects.map((project) => project.id) : [],
});

const submit = () => {
  form.put(route('clients.update', props.client.id));
};
</script>

<template>
  <Head :title="`Edit Client - ${client.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Client</h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <form @submit.prevent="submit">
        <div class="mb-4">
          <InputLabel for="id" value="ID" />
          <TextInput
            id="id"
            :value="client.id"
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
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            required
            v-model="form.email"
            type="email"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>
        <div class="mb-4">
          <InputLabel for="phone" value="Phone" />
          <TextInput
            id="phone"
            v-model="form.phone"
            type="text"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>
        <div class="mb-4">
          <InputLabel for="projects" value="Projects" />
          <vSelect
            id="projects"
            v-model="form.projects"
            :options="props.projects"
            :reduce="(project) => project.id"
            label="name"
            multiple
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
            placeholder="Select an option"
          >
          </vSelect>
          <InputError class="mt-2" :message="form.errors.projects" />
        </div>
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 mt-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
        >
          Update
        </button>
        <Link
          :href="route('clients.show', { client: client.id })"
          class="ml-4 text-blue-500 dark:text-blue-400 hover:underline"
        >
          Show Client
        </Link>
      </form>
    </div>
  </AppLayout>
</template>
