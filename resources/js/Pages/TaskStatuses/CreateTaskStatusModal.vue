<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { watch } from 'vue';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  status: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  name: props.status ? props.status.name : '',
});

watch(
  () => props.status,
  (newStatus) => {
    form.name = newStatus ? newStatus.name : '';
  }
);

const submit = () => {
  if (props.status) {
    form.put(route('task-statuses.update', props.status.id), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  } else {
    form.post(route('task-statuses.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  }
};
</script>

<template>
  <DialogModal :show="props.show" @close="emit('close')">
    <template #title>{{ status ? 'Edit' : 'Create' }} Task Status</template>
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
        {{ status ? 'Edit' : 'Create' }}
      </button>
    </template>
  </DialogModal>
</template>
