<script setup>
import ActionSection from '@/Components/ActionSection.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  team: Object,
  userPermissions: Object,
});

const cancelTeamInvitation = (invitation) => {
  router.delete(route('team-invitations.destroy', invitation), {
    preserveScroll: true,
  });
};
</script>

<template>
  <ActionSection class="mt-10 sm:mt-0">
    <template #title>{{ trans('words.pending_team_invitations') }}</template>

    <template #description>
      {{ trans('words.pending_team_invitations_description') }}
    </template>

    <!-- Pending Team Member Invitation List -->
    <template #content>
      <div class="space-y-6">
        <div v-for="invitation in team.team_invitations" :key="invitation.id" class="flex items-center justify-between">
          <div class="text-gray-600 dark:text-gray-400">
            {{ invitation.email }}
          </div>

          <div class="flex items-center">
            <!-- Cancel Team Invitation -->
            <button
              v-if="userPermissions.canRemoveTeamMembers"
              class="cursor-pointer ms-6 text-sm text-red-500 focus:outline-none"
              @click="cancelTeamInvitation(invitation)"
            >
              {{ trans('words.cancel') }}
            </button>
          </div>
        </div>
      </div>
    </template>
  </ActionSection>
</template>
