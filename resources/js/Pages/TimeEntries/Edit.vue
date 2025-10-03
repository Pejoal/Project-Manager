<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  timeEntry: Object,
});

const form = useForm({
  _method: 'PUT',
  start_datetime: props.timeEntry.start_datetime,
  end_datetime: props.timeEntry.end_datetime,
  description: props.timeEntry.description,
});

const submit = () => {
  form.post(route('time-entries.update', props.timeEntry.id));
};
</script>

<template>
  <Head title="Edit Time Entry" />
  <header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Time Entry</h2>
  </header>
  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <InputLabel for="start_datetime" value="Start Time" />
              <TextInput
                id="start_datetime"
                v-model="form.start_datetime"
                type="datetime-local"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.start_datetime" class="mt-2" />
            </div>
            <div>
              <InputLabel for="end_datetime" value="End Time" />
              <TextInput
                id="end_datetime"
                v-model="form.end_datetime"
                type="datetime-local"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.end_datetime" class="mt-2" />
            </div>
          </div>
          <div>
            <InputLabel for="description" value="Description (Optional)" />
            <textarea
              v-model="form.description"
              id="description"
              rows="4"
              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
            ></textarea>
            <InputError :message="form.errors.description" class="mt-2" />
          </div>
          <div class="flex justify-end">
            <PrimaryButton :disabled="form.processing">Update Entry</PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
