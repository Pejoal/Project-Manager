<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  project: Object,
  phase: Object,
});

const form = useForm({
  name: props.phase.name,
  description: props.phase.description,
});

const submit = () => {
  form.put(
    route('phases.update', {
      project: props.project.slug,
      phase: props.phase.id,
    }),
    {
      preserveScroll: true,
    }
  );
};
</script>

<template>
  <Head :title="`Edit phase - ${phase.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Phase</h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <form @submit.prevent="submit">
        <div class="mb-4">
          <InputLabel for="id" value="ID" />
          <TextInput
            id="id"
            :value="phase.id"
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

        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 mt-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
        >
          Update
        </button>
        <Link
          :href="route('phases.show', { project: project.slug, phase: phase.id })"
          class="ml-4 text-blue-500 dark:text-blue-400 hover:underline"
        >
          Show phase
        </Link>
      </form>
    </div>
  </AppLayout>
</template>
