<script setup>
import SectionBorder from '@/Components/SectionBorder.vue';
import DeleteTeamForm from '@/Pages/Teams/Partials/DeleteTeamForm.vue';
import TeamMemberManager from '@/Pages/Teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from '@/Pages/Teams/Partials/UpdateTeamNameForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  team: Object,
  availableRoles: Array,
  permissions: Object,
});
</script>

<template>
  <Head :title="trans('words.team_settings')" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ trans('words.team_settings') }}
      </h2>
    </div>
  </header>

  <div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <UpdateTeamNameForm :team="team" :permissions="permissions" />

      <div v-if="$page.props.jetstream.canAddTeamMembers">
        <SectionBorder />

        <TeamMemberManager
          class="mt-10 sm:mt-0"
          :team="team"
          :available-roles="availableRoles"
          :user-permissions="permissions"
        />
      </div>

      <template v-if="permissions.canDeleteTeam && !team.personal_team">
        <SectionBorder />

        <div class="mt-10 sm:mt-0">
          <DeleteTeamForm :team="team" />
        </div>
      </template>
    </div>
  </div>
</template>
