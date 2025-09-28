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
  permissions: Object,
});

const form = useForm({
  name: props.team.name,
});

const updateTeamName = () => {
  form.put(route('teams.update', props.team), {
    errorBag: 'updateTeamName',
    preserveScroll: true,
  });
};
</script>

<template>
  <FormSection @submitted="updateTeamName">
    <template #title>{{ trans('words.team_name') }}</template>

    <template #description>{{ trans('words.team_name_description') }}</template>

    <template #form>
      <!-- Team Owner Information -->
      <div class="col-span-6">
        <InputLabel :value="trans('words.team_owner')" />

        <div class="flex items-center mt-2">
          <img class="size-12 rounded-full object-cover" :src="team.owner.profile_photo_url" :alt="team.owner.name" />

          <div class="ms-4 leading-tight">
            <div class="text-gray-900 dark:text-white">
              {{ team.owner.name }}
            </div>
            <div class="text-gray-700 dark:text-gray-300 text-sm">
              {{ team.owner.email }}
            </div>
          </div>
        </div>
      </div>

      <!-- Team Name -->
      <div class="col-span-6 sm:col-span-4">
        <InputLabel for="name" :value="trans('words.team_name')" />

        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          :disabled="!permissions.canUpdateTeam"
        />

        <InputError :message="form.errors.name" class="mt-2" />
      </div>
    </template>

    <template v-if="permissions.canUpdateTeam" #actions>
      <ActionMessage :on="form.recentlySuccessful" class="me-3">
        {{ trans('words.saved') }}
      </ActionMessage>

      <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        {{ trans('words.save') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
