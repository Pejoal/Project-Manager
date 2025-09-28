<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  status: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close']);

const status = ref(props.status);

const form = useForm({
  name: status.value ? status.value.name : '',
  color: status.value ? status.value.color : '#000000',
  order: status.value ? status.value.order : '',
});

watch(
  () => props.status,
  (newStatus) => {
    if (newStatus) {
      status.value = newStatus;
      form.name = newStatus.name;
      form.color = newStatus.color;
      form.order = newStatus.order;
    }
  }
);

const createStatus = () => {
  form.post(route('project-statuses.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      emit('close');
    },
  });
};

const updateStatus = () => {
  form.put(route('project-statuses.update', status.value.id), {
    onSuccess: () => {
      form.reset();
      status.value = null;
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
  <DialogModal :show="props.show" @close="closeModal">
    <template #title>
      {{ status?.name ? trans('words.edit_status') : trans('words.create_status') }}
    </template>

    <template #content>
      <form id="form" @submit.prevent="status?.name ? updateStatus : createStatus" class="space-y-4">
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
        <div>
          <InputLabel for="order" :value="trans('words.order')" />
          <TextInput
            id="order"
            required
            v-model="form.order"
            type="number"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
          />
          <InputError class="mt-2" :message="form.errors.order" />
        </div>
      </form>
    </template>
    <template #footer>
      <SecondaryButton @click="closeModal">
        {{ trans('words.cancel') }}
      </SecondaryButton>
      <PrimaryButton form="form" :disabled="form.processing" type="submit" class="ms-3">
        {{ status?.name ? trans('words.edit') : trans('words.create') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
