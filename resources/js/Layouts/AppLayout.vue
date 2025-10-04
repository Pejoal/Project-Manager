<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Breadcrumps from '@/Components/Breadcrumps.vue';
import SettingsDropdown from '@/Components/Navigation/SettingsDropdown.vue';
import TeamsDropdown from '@/Components/Navigation/TeamsDropdown.vue';
import SidebarDropdown from '@/Components/SidebarDropdown.vue';
import SidebarLink from '@/Components/SidebarLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

// --- Assets: SVG Icons for Sidebar & Header ---
const IconDashboard = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>`,
};
const IconClients = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.224-1.25-.62-1.722M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.224-1.25.62-1.722M12 12a3 3 0 100-6 3 3 0 000 6z"></path></svg>`,
};
const IconProjects = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>`,
};
const IconTasks = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>`,
};
const IconPayroll = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>`,
};
const IconChevronLeft = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>`,
};
const IconSun = {
  template: `<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-15.66l-.707.707M4.34 19.66l-.707.707M21 12h-1M4 12H3m15.66 8.66l-.707-.707M4.34 4.34l-.707-.707"></path></svg>`,
};
const IconMoon = {
  template: `<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>`,
};

// --- Main Layout Logic ---
const page = usePage();
const showingMobileMenu = ref(false);

// Dark Mode Logic
const isDarkMode = ref(localStorage.getItem('theme') === 'dark');
document.documentElement.classList.toggle('dark', isDarkMode.value);
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  const theme = isDarkMode.value ? 'dark' : 'light';
  document.documentElement.classList.toggle('dark', isDarkMode.value);
  localStorage.setItem('theme', theme);
};

// Sidebar Collapse Logic
const isSidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true');
const toggleSidebarCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  localStorage.setItem('sidebarCollapsed', isSidebarCollapsed.value);
};

// Locales Dropdown Logic
const isLocalesOpen = ref(false);
const localesDropdownRef = ref(null);
const activeLocaleCode = ref(page.props.active_locale_code);

if (activeLocaleCode.value) {
  loadLanguageAsync(activeLocaleCode.value);
}

const handleLocaleClick = (code, url) => {
  activeLocaleCode.value = code;
  loadLanguageAsync(code);
};

const handleClickOutsideLocales = (event) => {
  if (localesDropdownRef.value && !localesDropdownRef.value.contains(event.target)) {
    isLocalesOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutsideLocales));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutsideLocales));

// --- Role-based Access & Active State Logic ---
const hasRole = (role) => page.props.user.roles.includes(role);
const hasAnyRole = (roles) => roles.some((r) => hasRole(r));
const isPayrollActive = computed(
  () =>
    route().current('payroll.*') ||
    route().current('employee-profiles.*') ||
    route().current('payslips.*') ||
    route().current('time-entries.*')
);

const handleToggleSidebarCollapse = () => {
  if (isSidebarCollapsed.value) {
    toggleSidebarCollapse();
  }
};
</script>

<template>
  <div>
    <Banner />

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
      <!-- Sidebar Component -->
      <aside
        class="hidden md:flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300"
        :class="isSidebarCollapsed ? 'w-20' : 'w-64'"
      >
        <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
          <Link :href="route('dashboard')">
            <ApplicationMark class="block h-8 w-auto" />
          </Link>
        </div>

        <nav class="flex-1 px-3 py-4 overflow-y-auto">
          <ul class="space-y-2">
            <li>
              <SidebarLink
                :href="route('dashboard')"
                :active="route().current('dashboard')"
                :is-collapsed="isSidebarCollapsed"
              >
                <template #icon><IconDashboard /></template>
                Dashboard
              </SidebarLink>
            </li>
            <li>
              <SidebarLink
                :href="route('clients.index')"
                :active="route().current('clients.*')"
                :is-collapsed="isSidebarCollapsed"
              >
                <template #icon><IconClients /></template>
                Clients
              </SidebarLink>
            </li>
            <li>
              <SidebarLink
                :href="route('projects.index')"
                :active="route().current('projects.*')"
                :is-collapsed="isSidebarCollapsed"
              >
                <template #icon><IconProjects /></template>
                Projects
              </SidebarLink>
            </li>
            <li>
              <SidebarLink
                :href="route('tasks.all')"
                :active="route().current('tasks.all')"
                :is-collapsed="isSidebarCollapsed"
              >
                <template #icon><IconTasks /></template>
                All Tasks
              </SidebarLink>
            </li>

            <!-- Payroll Dropdown (Visible to admin and manager) -->
            <li v-if="hasAnyRole(['admin', 'manager'])">
              <SidebarDropdown
                @toggled="handleToggleSidebarCollapse"
                title="Payroll"
                :active="isPayrollActive"
                :is-collapsed="isSidebarCollapsed"
              >
                <template #icon><IconPayroll /></template>
                <li v-if="hasRole('admin')">
                  <SidebarLink :href="route('payroll.dashboard')" :active="route().current('payroll.dashboard')"
                    >Dashboard</SidebarLink
                  >
                </li>
                <li v-if="hasAnyRole(['admin', 'manager', 'employee'])">
                  <SidebarLink :href="route('time-entries.index')" :active="route().current('time-entries.*')"
                    >Time Entries</SidebarLink
                  >
                </li>
                <li v-if="hasRole('admin')">
                  <SidebarLink :href="route('employee-profiles.index')" :active="route().current('employee-profiles.*')"
                    >Employees</SidebarLink
                  >
                </li>
                <li v-if="hasRole('admin')">
                  <SidebarLink :href="route('payslips.index')" :active="route().current('payslips.*')"
                    >Payslips</SidebarLink
                  >
                </li>
                <li v-if="hasRole('admin')">
                  <SidebarLink :href="route('payroll.settings')" :active="route().current('payroll.settings')"
                    >Settings</SidebarLink
                  >
                </li>
              </SidebarDropdown>
            </li>
          </ul>
        </nav>

        <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
          <button
            @click="toggleSidebarCollapse"
            class="w-full flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <IconChevronLeft
              class="w-6 h-6 transition-transform duration-300"
              :class="{ 'rotate-180': isSidebarCollapsed }"
            />
          </button>
        </div>
      </aside>

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
              <!-- Hamburger for Mobile -->
              <div class="flex items-center md:hidden">
                <button
                  @click="showingMobileMenu = !showingMobileMenu"
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500"
                >
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                  </svg>
                </button>
              </div>

              <!-- Breadcrumbs for Desktop -->
              <div class="hidden md:flex items-center">
                <Breadcrumps />
              </div>

              <!-- Right side of Top Bar -->
              <div class="flex items-center space-x-3">
                <button
                  @click="toggleDarkMode"
                  class="p-2 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                  <IconSun v-if="isDarkMode" />
                  <IconMoon v-else />
                </button>

                <!-- Locales Dropdown -->
                <div ref="localesDropdownRef" class="relative">
                  <button
                    @click="isLocalesOpen = !isLocalesOpen"
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                  >
                    <span
                      v-if="$page.props.locales && $page.props.locales[$page.props.active_locale_code]"
                      class="fi"
                      :class="`fi-${$page.props.locales[$page.props.active_locale_code].flag}`"
                    ></span>
                    <svg
                      class="ms-2 -me-0.5 h-4 w-4"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                  </button>
                  <div
                    v-show="isLocalesOpen"
                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                  >
                    <div class="py-1" role="none">
                      <button
                        v-for="locale in $page.props.locales"
                        :key="locale.code"
                        :href="locale.url"
                        @click.prevent="handleLocaleClick(locale.code, locale.url)"
                        class="w-full text-left flex items-center px-4 py-2 text-sm"
                        :class="[
                          activeLocaleCode === locale.code
                            ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white'
                            : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800',
                        ]"
                      >
                        <span class="fi mr-3" :class="`fi-${locale.flag}`"></span>
                        {{ locale.native }}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="ms-3 relative">
                  <TeamsDropdown />
                </div>
                <div class="ms-3 relative">
                  <SettingsDropdown />
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs for Mobile -->
            <Breadcrumps class="mb-4 md:hidden" />
            <slot />
          </div>
        </main>
      </div>

      <!-- Mobile Sidebar (Off-canvas) -->
      <div v-show="showingMobileMenu" class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true">
        <div
          v-show="showingMobileMenu"
          class="fixed inset-0 bg-gray-600 bg-opacity-75"
          @click="showingMobileMenu = false"
        ></div>
        <aside class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800">
          <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button
              @click="showingMobileMenu = false"
              class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            >
              <span class="sr-only">Close sidebar</span>
              <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div
            class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0"
          >
            <Link :href="route('dashboard')">
              <ApplicationMark class="block h-8 w-auto" />
            </Link>
          </div>
          <nav class="flex-1 px-3 py-4 overflow-y-auto">
            <ul class="space-y-2">
              <li>
                <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')"
                  ><template #icon><IconDashboard /></template>Dashboard</SidebarLink
                >
              </li>
              <li>
                <SidebarLink :href="route('clients.index')" :active="route().current('clients.*')"
                  ><template #icon><IconClients /></template>Clients</SidebarLink
                >
              </li>
              <li>
                <SidebarLink :href="route('projects.index')" :active="route().current('projects.*')"
                  ><template #icon><IconProjects /></template>Projects</SidebarLink
                >
              </li>
              <li>
                <SidebarLink :href="route('tasks.all')" :active="route().current('tasks.all')"
                  ><template #icon><IconTasks /></template>All Tasks</SidebarLink
                >
              </li>
              <li v-if="hasAnyRole(['admin', 'manager'])">
                <SidebarDropdown title="Payroll" @toggled="handleToggleSidebarCollapse" :active="isPayrollActive">
                  <template #icon><IconPayroll /></template>
                  <li v-if="hasRole('admin')">
                    <SidebarLink :href="route('payroll.dashboard')" :active="route().current('payroll.dashboard')"
                      >Dashboard</SidebarLink
                    >
                  </li>
                  <li v-if="hasAnyRole(['admin', 'manager', 'employee'])">
                    <SidebarLink :href="route('time-entries.index')" :active="route().current('time-entries.*')"
                      >Time Entries</SidebarLink
                    >
                  </li>
                  <li v-if="hasRole('admin')">
                    <SidebarLink
                      :href="route('employee-profiles.index')"
                      :active="route().current('employee-profiles.*')"
                      >Employees</SidebarLink
                    >
                  </li>
                  <li v-if="hasRole('admin')">
                    <SidebarLink :href="route('payslips.index')" :active="route().current('payslips.*')"
                      >Payslips</SidebarLink
                    >
                  </li>
                  <li v-if="hasRole('admin')">
                    <SidebarLink :href="route('payroll.settings')" :active="route().current('payroll.settings')"
                      >Settings</SidebarLink
                    >
                  </li>
                </SidebarDropdown>
              </li>
            </ul>
          </nav>
        </aside>
      </div>
    </div>
  </div>
</template>
