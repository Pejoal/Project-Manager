<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';

const emit = defineEmits(['close']);
const props = defineProps({
  show: Boolean,
  status: {
    type: Object,
    default: null,
  },
});

const status = ref(props.status);

const form = useForm({
  name: status.value ? status.value.name : '',
  color: status.value ? status.value.color : '#000000',
});

watch(
  () => props.status,
  (newStatus) => {
    if (newStatus) {
      status.value = newStatus;
      form.name = newStatus.name;
      form.color = newStatus.color;
    }
  }
);

const submit = () => {
  if (status.value?.name) {
    form.put(route('task-statuses.update', status.value.id), {
      onSuccess: () => {
        form.reset();
        status.value = null;
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
    <template #title
      >{{ status?.name ? 'Edit' : 'Create' }} Task Status</template
    >
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
          <InputLabel for="color" value="Color" />
          <TextInput
            id="color"
            required
            v-model="form.color"
            type="color"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.color" />
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
        {{ status?.name ? 'Edit' : 'Create' }}
      </button>
    </template>
  </DialogModal>
</template>
