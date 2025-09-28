<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import vSelect from 'vue-select';

const props = defineProps({
  project: Object,
  phases: Array,
  milestone: Object,
});

const form = useForm({
  name: props.milestone.name,
  description: props.milestone.description,
  phase_id: props.milestone.phase_id,
});

const submit = () => {
  form.put(
    route('milestones.update', {
      project: props.project.slug,
      milestone: props.milestone.id,
    }),
    {
      preserveScroll: true,
    }
  );
};
</script>

<template>
  <Head :title="`${trans('words.edit')} ${trans('words.milestone')} - ${milestone.name}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ trans('words.edit') }} {{ trans('words.milestone') }}
      </h1>
    </div>
  </header>
  <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <form class="space-y-4" @submit.prevent="submit">
      <div>
        <InputLabel for="id" :value="trans('words.id')" />
        <TextInput
          id="id"
          :value="milestone.id"
          readonly
          type="text"
          class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-gray-200 dark:bg-zinc-600"
        />
      </div>
      <div>
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
      <div>
        <InputLabel for="description" :value="trans('words.description')" />
        <TextInput
          id="description"
          v-model="form.description"
          type="text"
          class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
        />
        <InputError class="mt-2" :message="form.errors.description" />
      </div>
      <div>
        <InputLabel for="phase" :value="trans('words.phase')" />
        <vSelect
          v-if="props.phases.length > 0"
          id="phase"
          v-model="form.phase_id"
          :options="props.phases"
          :reduce="(phase) => phase.id"
          label="name"
          class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          :placeholder="trans('words.select_option')"
        >
        </vSelect>

        <InputError class="mt-2" :message="form.errors.phase" />
      </div>
      <button
        type="submit"
        :disabled="form.processing"
        class="px-4 py-2 mt-2 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700"
      >
        {{ trans('words.update') }}
      </button>
      <Link
        :href="
          route('milestones.show', {
            project: project.slug,
            milestone: milestone.id,
          })
        "
        class="ml-4 text-blue-500 dark:text-blue-400 hover:underline"
      >
        {{ trans('words.show') }} {{ trans('words.milestone') }}
      </Link>
    </form>
  </div>
</template>
