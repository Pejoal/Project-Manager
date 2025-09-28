<script setup>
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  team: Object,
  availableRoles: Array,
});

const addTeamMemberForm = useForm({
  email: '',
  role: null,
});

const addTeamMember = () => {
  addTeamMemberForm.post(route('team-members.store', props.team), {
    errorBag: 'addTeamMember',
    preserveScroll: true,
    onSuccess: () => addTeamMemberForm.reset(),
  });
};
</script>

<template>
  <FormSection @submitted="addTeamMember">
    <template #title>{{ trans('words.add_team_member') }}</template>

    <template #description>{{ trans('words.add_team_member_description') }}</template>

    <template #form>
      <div class="col-span-6">
        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
          {{ trans('words.provide_email_address_team') }}
        </div>
      </div>

      <!-- Member Email -->
      <div class="col-span-6 sm:col-span-4">
        <InputLabel for="email" :value="trans('words.email')" />
        <TextInput id="email" v-model="addTeamMemberForm.email" type="email" class="mt-1 block w-full" />
        <InputError :message="addTeamMemberForm.errors.email" class="mt-2" />
      </div>

      <!-- Role -->
      <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
        <InputLabel for="roles" value="Role" />
        <InputError :message="addTeamMemberForm.errors.role" class="mt-2" />

        <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer">
          <button
            v-for="(role, i) in availableRoles"
            :key="role.key"
            type="button"
            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600"
            :class="{
              'border-t border-gray-200 dark:border-gray-700 focus:border-none rounded-t-none': i > 0,
              'rounded-b-none': i != Object.keys(availableRoles).length - 1,
            }"
            @click="addTeamMemberForm.role = role.key"
          >
            <div
              :class="{
                'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key,
              }"
            >
              <!-- Role Name -->
              <div class="flex items-center">
                <div
                  class="text-sm text-gray-600 dark:text-gray-400"
                  :class="{
                    'font-semibold': addTeamMemberForm.role == role.key,
                  }"
                >
                  {{ role.name }}
                </div>

                <svg
                  v-if="addTeamMemberForm.role == role.key"
                  class="ms-2 size-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>

              <!-- Role Description -->
              <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 text-start">
                {{ role.description }}
              </div>
            </div>
          </button>
        </div>
      </div>
    </template>

    <template #actions>
      <ActionMessage :on="addTeamMemberForm.recentlySuccessful" class="me-3">
        {{ trans('words.added') }}
      </ActionMessage>

      <PrimaryButton :class="{ 'opacity-25': addTeamMemberForm.processing }" :disabled="addTeamMemberForm.processing">
        {{ trans('words.add') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
