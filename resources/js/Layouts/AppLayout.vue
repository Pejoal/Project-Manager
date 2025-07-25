<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Locales from '@/Components/Locales.vue';

defineProps({
  title: String,
});

const showingNavigationDropdown = ref(false);

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

const isDarkMode = ref(localStorage.getItem('theme') === 'dark');
document.documentElement.classList.toggle('dark', isDarkMode.value);

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  const theme = isDarkMode.value ? 'dark' : 'light';
  document.documentElement.classList.toggle('dark', isDarkMode.value);
  localStorage.setItem('theme', theme);
};
</script>

<template>
  <div>
    <Head :title="title" />

    <Banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
      <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-1 sm:px-2 lg:px-3">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('dashboard')">
                  <ApplicationMark class="block h-8 w-auto" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <div class="hidden space-x-2 sm:-my-px sm:ms-4 md:flex">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')"> Dashboard </NavLink>
                <NavLink :href="route('clients.index')" :active="route().current('clients.*')"> Clients </NavLink>
                <NavLink :href="route('projects.index')" :active="route().current('projects.*')"> Projects </NavLink>
                <NavLink :href="route('tasks.all')" :active="route().current('tasks.*')"> Tasks </NavLink>

                <!-- Time Tracking Dropdown -->
                <div class="relative self-center">
                  <Dropdown align="left" width="60">
                    <template #trigger>
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                        :class="{
                          'text-gray-900 dark:text-gray-100':
                            route().current('time-tracking.*') ||
                            route().current('work-logs.*') ||
                            route().current('time-reports.*'),
                        }"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                          ></path>
                        </svg>
                        Time Tracking
                        <svg
                          class="ml-1 -mr-0.5 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                      </button>
                    </template>

                    <template #content>
                      <div class="w-60">
                        <div class="block px-4 py-2 text-xs text-gray-400">Time Tracking & Productivity</div>

                        <DropdownLink :href="route('time-tracking.index')" :active="route().current('time-tracking.*')">
                          <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                              ></path>
                            </svg>
                            Time Entries
                          </div>
                        </DropdownLink>

                        <DropdownLink :href="route('work-logs.index')" :active="route().current('work-logs.*')">
                          <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                              ></path>
                            </svg>
                            Work Logs
                          </div>
                        </DropdownLink>

                        <DropdownLink :href="route('time-reports.index')" :active="route().current('time-reports.*')">
                          <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                              ></path>
                            </svg>
                            Time Reports
                          </div>
                        </DropdownLink>
                      </div>
                    </template>
                  </Dropdown>
                </div>

                <div class="flex items-center">
                  <button
                    @click="toggleDarkMode"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                  >
                    <span v-if="isDarkMode">☀️ Light Mode</span>
                    <span v-else>🌙 Dark Mode</span>
                  </button>
                </div>

                <Locales />
              </div>
            </div>

            <!-- Teams & Settings Dropdowns -->
            <div class="hidden md:flex sm:items-center sm:ms-6">
              <div class="ms-3 relative">
                <!-- Teams Dropdown -->
                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                      >
                        {{ $page.props.auth.user.current_team.name }}

                        <svg
                          class="ms-2 -me-0.5 size-4"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                          />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <div class="w-60">
                      <!-- Team Management -->
                      <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                      <!-- Team Settings -->
                      <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                        Team Settings
                      </DropdownLink>

                      <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                        Create New Team
                      </DropdownLink>

                      <!-- Team Switcher -->
                      <template v-if="$page.props.auth.user.all_teams.length > 1">
                        <div class="border-t border-gray-200 dark:border-gray-600" />

                        <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>

                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                          <form @submit.prevent="switchToTeam(team)">
                            <DropdownLink as="button">
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
                            </DropdownLink>
                          </form>
                        </template>
                      </template>
                    </div>
                  </template>
                </Dropdown>
              </div>

              <!-- Settings Dropdown -->
              <div class="ms-3 relative">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      v-if="$page.props.jetstream.managesProfilePhotos"
                      class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                    >
                      <img
                        class="size-8 rounded-full object-cover"
                        :src="$page.props.auth.user.profile_photo_url"
                        :alt="$page.props.auth.user.name"
                      />
                    </button>

                    <span v-else class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                      >
                        {{ $page.props.auth.user.name }}

                        <svg
                          class="ms-2 -me-0.5 size-4"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>

                    <DropdownLink :href="route('profile.show')"> Profile </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <DropdownLink :href="route('project-statuses.index')"> Project Statuses </DropdownLink>
                    <DropdownLink :href="route('project-priorities.index')"> Project Priorities </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <DropdownLink :href="route('task-statuses.index')"> Task Statuses </DropdownLink>
                    <DropdownLink :href="route('task-priorities.index')"> Task Priorities </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <DropdownLink :href="route('activities.index')"> Activities </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <DropdownLink :href="route('maps.index')"> Map </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                      API Tokens
                    </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <DropdownLink :href="route('terms.show')" :active="route().current('terms.show')">
                      Terms of Service
                    </DropdownLink>

                    <DropdownLink :href="route('policy.show')" :active="route().current('policy.show')">
                      Privacy Policy
                    </DropdownLink>

                    <div class="border-t border-gray-200 dark:border-gray-600" />

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                      <DropdownLink as="button"> Log Out </DropdownLink>
                    </form>
                  </template>
                </Dropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
              <button
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
              >
                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

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
              Dashboard
            </ResponsiveNavLink>

            <ResponsiveNavLink :href="route('clients.index')" :active="route().current('clients.*')">
              Clients
            </ResponsiveNavLink>

            <ResponsiveNavLink :href="route('projects.index')" :active="route().current('projects.*')">
              Projects
            </ResponsiveNavLink>

            <ResponsiveNavLink :href="route('tasks.all')" :active="route().current('tasks.*')">
              Tasks
            </ResponsiveNavLink>

            <!-- Time Tracking Mobile Links -->
            <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
              <div class="block px-4 py-2 text-xs text-gray-400">Time Tracking & Productivity</div>

              <ResponsiveNavLink :href="route('time-tracking.index')" :active="route().current('time-tracking.*')">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                  </svg>
                  Time Entries
                </div>
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('work-logs.index')" :active="route().current('work-logs.*')">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    ></path>
                  </svg>
                  Work Logs
                </div>
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('time-reports.index')" :active="route().current('time-reports.*')">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    ></path>
                  </svg>
                  Time Reports
                </div>
              </ResponsiveNavLink>
            </div>

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
                Profile
              </ResponsiveNavLink>

              <div class="border-t border-gray-200 dark:border-gray-600" />

              <ResponsiveNavLink
                :href="route('project-statuses.index')"
                :active="route().current('project-statuses.index')"
              >
                Project Statuses
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('project-priorities.index')"
                :active="route().current('project-priorities.index')"
              >
                Project Priorities
              </ResponsiveNavLink>

              <div class="border-t border-gray-200 dark:border-gray-600" />

              <ResponsiveNavLink :href="route('task-statuses.index')" :active="route().current('task-statuses.index')">
                Task Statuses
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('task-priorities.index')"
                :active="route().current('task-priorities.index')"
              >
                Task Priorities
              </ResponsiveNavLink>

              <div class="border-t border-gray-200 dark:border-gray-600" />

              <ResponsiveNavLink :href="route('activities.index')" :active="route().current('activities.index')">
                Activities
              </ResponsiveNavLink>

              <div class="border-t border-gray-200 dark:border-gray-600" />

              <ResponsiveNavLink :href="route('maps.index')" :active="route().current('maps.index')">
                Map
              </ResponsiveNavLink>

              <div class="border-t border-gray-200 dark:border-gray-600" />

              <ResponsiveNavLink
                v-if="$page.props.jetstream.hasApiFeatures"
                :href="route('api-tokens.index')"
                :active="route().current('api-tokens.index')"
              >
                API Tokens
              </ResponsiveNavLink>

              <div class="border-t border-gray-200 dark:border-gray-600" />

              <ResponsiveNavLink :href="route('terms.show')" :active="route().current('terms.show')">
                Terms of Service
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('policy.show')" :active="route().current('policy.show')">
                Privacy Policy
              </ResponsiveNavLink>

              <div class="ms-3 relative">
                <button
                  @click="toggleDarkMode"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                >
                  <span v-if="isDarkMode">☀️ Light Mode</span>
                  <span v-else>🌙 Dark Mode</span>
                </button>
              </div>

              <!-- Authentication -->
              <form method="POST" @submit.prevent="logout">
                <ResponsiveNavLink as="button"> Log Out </ResponsiveNavLink>
              </form>

              <!-- Team Management -->
              <template v-if="$page.props.jetstream.hasTeamFeatures">
                <div class="border-t border-gray-200 dark:border-gray-600" />

                <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                <!-- Team Settings -->
                <ResponsiveNavLink
                  :href="route('teams.show', $page.props.auth.user.current_team)"
                  :active="route().current('teams.show')"
                >
                  Team Settings
                </ResponsiveNavLink>

                <ResponsiveNavLink
                  v-if="$page.props.jetstream.canCreateTeams"
                  :href="route('teams.create')"
                  :active="route().current('teams.create')"
                >
                  Create New Team
                </ResponsiveNavLink>

                <!-- Team Switcher -->
                <template v-if="$page.props.auth.user.all_teams.length > 1">
                  <div class="border-t border-gray-200 dark:border-gray-600" />

                  <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>

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
      </nav>

      <!-- Page Heading -->
      <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="pb-6">
        <slot />
      </main>
    </div>
  </div>
</template>
<style>
@media (prefers-color-scheme: dark) {
  .vs__actions {
    background: white;
  }
  .vs__dropdown-toggle,
  .vs__search,
  .vs__selected {
    color: white !important;
  }

  .vs__dropdown-menu,
  .vs--multiple .vs__selected {
    color: black !important;
  }
}
</style>
