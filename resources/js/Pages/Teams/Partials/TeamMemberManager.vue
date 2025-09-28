<script setup>
import SectionBorder from '@/Components/SectionBorder.vue';
import AddTeamMemberForm from './AddTeamMemberForm.vue';
import TeamInvitations from './TeamInvitations.vue';
import TeamMembersList from './TeamMembersList.vue';

defineProps({
  team: Object,
  availableRoles: Array,
  userPermissions: Object,
});

const handleRoleUpdated = () => {
  // Handle any post-update logic if needed
};
</script>

<template>
  <div>
    <!-- Add Team Member Form -->
    <div v-if="userPermissions.canAddTeamMembers">
      <SectionBorder />
      <AddTeamMemberForm :team="team" :available-roles="availableRoles" />
    </div>

    <!-- Team Invitations -->
    <div v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers">
      <SectionBorder />
      <TeamInvitations :team="team" :user-permissions="userPermissions" />
    </div>

    <!-- Team Members List -->
    <div v-if="team.users.length > 0">
      <SectionBorder />
      <TeamMembersList
        :team="team"
        :available-roles="availableRoles"
        :user-permissions="userPermissions"
        @role-updated="handleRoleUpdated"
      />
    </div>
  </div>
</template>
