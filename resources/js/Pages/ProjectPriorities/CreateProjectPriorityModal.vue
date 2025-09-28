<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
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
  color: priority.value ? priority.value.color : '#000000',
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
  if (priority.value?.name) {
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

const closeModal = () => {
  form.reset();
  emit('close');
};
</script>

<template>
  <DialogModal :show="props.show" @close="closeModal">
    <template #title>{{ priority?.name ? trans('words.edit_priority') : trans('words.create_priority') }}</template>
    <template #content>
      <form id="form" @submit.prevent="submit" class="space-y-4">
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
          <InputLabel for="color" :value="trans('words.color')" />
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
      <SecondaryButton @click="closeModal">
        {{ trans('words.cancel') }}
      </SecondaryButton>
      <PrimaryButton form="form" :disabled="form.processing" type="submit" class="ms-3">
        {{ priority?.name ? trans('words.edit') : trans('words.create') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
