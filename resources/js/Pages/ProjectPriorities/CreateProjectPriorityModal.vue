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
  priority: {
    type: Object,
    default: null,
  },
});

const priority = ref(props.priority);

const form = useForm({
  name: priority.value ? priority.value.name : '',
  color: priority.value ? priority.value.color : null,
});

watch(
  () => props.priority,
  (newPriority) => {
    if (newPriority) {
      priority.value = newPriority;
      form.name = newPriority.name;
      form.color = newPriority.color;
    }
  }
);

const submit = () => {
  if (priority.value) {
    form.put(route('project-priorities.update', priority.value.id), {
      onSuccess: () => {
        form.reset();
        priority.value = null;
        emit('close');
      },
    });
  } else {
    form.post(route('project-priorities.store'), {
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
      >{{ priority ? 'Edit' : 'Create' }} Project Priority</template
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
        {{ priority ? 'Edit' : 'Create' }}
      </button>
    </template>
  </DialogModal>
</template>
