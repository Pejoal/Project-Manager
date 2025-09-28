<script setup>
import Locales from '@/Components/Locales.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { router } from '@inertiajs/vue3';

defineProps({
  showingNavigationDropdown: Boolean,
  isDarkMode: Boolean,
});

defineEmits(['toggle-dark-mode']);

const switchToTeam = (team) => {
  router.put(
    route('current-team.update'),
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    }
  );
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <!-- Responsive Navigation Menu -->
  <div
    :class="{
      block: showingNavigationDropdown,
      hidden: !showingNavigationDropdown,
    }"
    class="md:hidden"
  >
    <div class="pt-2 pb-3 space-y-1">
      <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
        {{ trans('words.dashboard') }}
      </ResponsiveNavLink>

      <ResponsiveNavLink :href="route('clients.index')" :active="route().current('clients.*')">
        {{ trans('words.clients') }}
      </ResponsiveNavLink>

      <ResponsiveNavLink :href="route('projects.index')" :active="route().current('projects.*')">
        {{ trans('words.projects') }}
      </ResponsiveNavLink>

      <ResponsiveNavLink :href="route('tasks.all')" :active="route().current('tasks.*')">
        {{ trans('words.tasks') }}
      </ResponsiveNavLink>

      <Locales />
    </div>

    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
      <div class="flex items-center px-4">
        <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
          <img
            class="size-10 rounded-full object-cover"
            :src="$page.props.auth.user.profile_photo_url"
            :alt="$page.props.auth.user.name"
          />
        </div>

        <div>
          <div class="font-medium text-base text-gray-800 dark:text-gray-200">
            {{ $page.props.auth.user.name }}
          </div>
          <div class="font-medium text-sm text-gray-500">
            {{ $page.props.auth.user.email }}
          </div>
        </div>
      </div>

      <div class="mt-3 space-y-1">
        <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
          {{ trans('words.profile') }}
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 dark:border-gray-600" />

        <ResponsiveNavLink :href="route('project-statuses.index')" :active="route().current('project-statuses.index')">
          {{ trans('words.project_statuses') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
          :href="route('project-priorities.index')"
          :active="route().current('project-priorities.index')"
        >
          {{ trans('words.project_priorities') }}
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 dark:border-gray-600" />

        <ResponsiveNavLink :href="route('task-statuses.index')" :active="route().current('task-statuses.index')">
          {{ trans('words.task_statuses') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('task-priorities.index')" :active="route().current('task-priorities.index')">
          {{ trans('words.task_priorities') }}
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 dark:border-gray-600" />

        <ResponsiveNavLink :href="route('activities.index')" :active="route().current('activities.index')">
          {{ trans('words.activities') }}
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 dark:border-gray-600" />

        <ResponsiveNavLink :href="route('maps.index')" :active="route().current('maps.index')">
          {{ trans('words.map') }}
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 dark:border-gray-600" />

        <ResponsiveNavLink
          v-if="$page.props.jetstream.hasApiFeatures"
          :href="route('api-tokens.index')"
          :active="route().current('api-tokens.index')"
        >
          {{ trans('words.api_tokens') }}
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 dark:border-gray-600" />

        <ResponsiveNavLink :href="route('terms.show')" :active="route().current('terms.show')">
          {{ trans('words.terms_of_service') }}
        </ResponsiveNavLink>

        <ResponsiveNavLink :href="route('policy.show')" :active="route().current('policy.show')">
          {{ trans('words.privacy_policy') }}
        </ResponsiveNavLink>

        <div class="ms-3 relative">
          <button
            @click="$emit('toggle-dark-mode')"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
          >
            <span v-if="isDarkMode">{{ trans('words.light_mode') }}</span>
            <span v-else>{{ trans('words.dark_mode') }}</span>
          </button>
        </div>

        <!-- Authentication -->
        <form method="POST" @submit.prevent="logout">
          <ResponsiveNavLink as="button">{{ trans('words.logout') }}</ResponsiveNavLink>
        </form>

        <!-- Team Management -->
        <template v-if="$page.props.jetstream.hasTeamFeatures">
          <div class="border-t border-gray-200 dark:border-gray-600" />

          <div class="block px-4 py-2 text-xs text-gray-400">{{ trans('words.manage_team') }}</div>

          <!-- Team Settings -->
          <ResponsiveNavLink
            :href="route('teams.show', $page.props.auth.user.current_team)"
            :active="route().current('teams.show')"
          >
            {{ trans('words.team_settings') }}
          </ResponsiveNavLink>

          <ResponsiveNavLink
            v-if="$page.props.jetstream.canCreateTeams"
            :href="route('teams.create')"
            :active="route().current('teams.create')"
          >
            {{ trans('words.create_new_team') }}
          </ResponsiveNavLink>

          <!-- Team Switcher -->
          <template v-if="$page.props.auth.user.all_teams.length > 1">
            <div class="border-t border-gray-200 dark:border-gray-600" />

            <div class="block px-4 py-2 text-xs text-gray-400">{{ trans('words.switch_teams') }}</div>

            <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
              <form @submit.prevent="switchToTeam(team)">
                <ResponsiveNavLink as="button">
                  <div class="flex items-center">
                    <svg
                      v-if="team.id == $page.props.auth.user.current_team_id"
                      class="me-2 size-5 text-green-400"
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
                    <div>{{ team.name }}</div>
                  </div>
                </ResponsiveNavLink>
              </form>
            </template>
          </template>
        </template>
      </div>
    </div>
  </div>
</template>
