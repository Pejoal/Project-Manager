<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
  project: Object,
  phases: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
  name: '',
  description: '',
  due_date: '',
  phase_id: '',
  project_id: props.project?.id || '',
});

const createMilestone = () => {
  form.post(route('milestones.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      emit('close');
    },
  });
};

const closeModal = () => {
  form.reset();
  emit('close');
};
</script>

<template>
  <DialogModal :show="show" @close="closeModal">
    <template #title>
      {{ trans('words.create_milestone') }}
    </template>

    <template #content>
      <div class="mt-4">
        <InputLabel for="name" :value="trans('words.name')" />
        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <div class="mt-4">
        <InputLabel for="description" :value="trans('words.description')" />
        <TextArea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
        <InputError :message="form.errors.description" class="mt-2" />
      </div>

      <div class="mt-4">
        <InputLabel for="due_date" :value="trans('words.due_date')" />
        <TextInput id="due_date" v-model="form.due_date" type="date" class="mt-1 block w-full" />
        <InputError :message="form.errors.due_date" class="mt-2" />
      </div>

      <div v-if="phases && phases.length > 0" class="mt-4">
        <InputLabel for="phase_id" :value="trans('words.phase')" />
        <select
          id="phase_id"
          v-model="form.phase_id"
          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        >
          <option value="">{{ trans('words.select_phase') }}</option>
          <option v-for="phase in phases" :key="phase.id" :value="phase.id">
            {{ phase.name }}
          </option>
        </select>
        <InputError :message="form.errors.phase_id" class="mt-2" />
      </div>
    </template>

    <template #footer>
      <SecondaryButton @click="closeModal">
        {{ trans('words.cancel') }}
      </SecondaryButton>

      <PrimaryButton
        class="ms-3"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="createMilestone"
      >
        {{ trans('words.create') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
