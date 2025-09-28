<script setup>
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  team: Object,
});

const confirmingTeamDeletion = ref(false);
const form = useForm({});

const confirmTeamDeletion = () => {
  confirmingTeamDeletion.value = true;
};

const deleteTeam = () => {
  form.delete(route('teams.destroy', props.team), {
    errorBag: 'deleteTeam',
  });
};
</script>

<template>
  <ActionSection>
    <template #title>
      {{ trans('words.delete_team') }}
    </template>

    <template #description>
      {{ trans('words.delete_team_desc') }}
    </template>

    <template #content>
      <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
        {{ trans('words.delete_team_warning') }}
      </div>

      <div class="mt-5">
        <DangerButton @click="confirmTeamDeletion">
          {{ trans('words.delete_team') }}
        </DangerButton>
      </div>

      <!-- Delete Team Confirmation Modal -->
      <ConfirmationModal :show="confirmingTeamDeletion" @close="confirmingTeamDeletion = false">
        <template #title>
          {{ trans('words.delete_team') }}
        </template>

        <template #content>
          {{ trans('words.delete_team_confirmation') }}
        </template>

        <template #footer>
          <SecondaryButton @click="confirmingTeamDeletion = false">
            {{ trans('words.cancel') }}
          </SecondaryButton>

          <DangerButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="deleteTeam"
          >
            {{ trans('words.delete_team') }}
          </DangerButton>
        </template>
      </ConfirmationModal>
    </template>
  </ActionSection>
</template>
