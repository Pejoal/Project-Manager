<script setup>
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
  status: String,
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <Head :title="trans('words.forgot_password')" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
      {{ trans('words.forgot_password') }}? {{ trans('words.email_password_reset_link') }}
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" :value="trans('words.email')" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          {{ trans('words.email_password_reset_link') }}
        </PrimaryButton>
      </div>
    </form>
  </AuthenticationCard>
</template>
