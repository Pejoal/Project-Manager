<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

const props = defineProps({
  confirmsTwoFactorAuthentication: Boolean,
  sessions: Array,
  translations: Object,
});
</script>

<template>
  <AppLayout :title="translations.profile">
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        {{ translations.profile }}
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div v-if="$page.props.jetstream.canUpdateProfileInformation">
          <UpdateProfileInformationForm :user="$page.props.auth.user" :translations="translations" />

          <SectionBorder />
        </div>

        <div v-if="$page.props.jetstream.canUpdatePassword">
          <UpdatePasswordForm class="mt-10 sm:mt-0" :translations="translations" />

          <SectionBorder />
        </div>

        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
          <TwoFactorAuthenticationForm
            :requires-confirmation="confirmsTwoFactorAuthentication"
            class="mt-10 sm:mt-0"
            :translations="translations"
          />

          <SectionBorder />
        </div>

        <LogoutOtherBrowserSessionsForm
          :sessions="sessions"
          class="mt-10 sm:mt-0"
          :translations="translations"
        />

        <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
          <SectionBorder />

          <DeleteUserForm class="mt-10 sm:mt-0" :translations="translations" />
        </template>
      </div>
    </div>
  </AppLayout>
</template>
