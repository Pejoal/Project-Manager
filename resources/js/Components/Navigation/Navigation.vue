<script setup>
import Locales from '@/Components/Locales.vue';
import NavLink from '@/Components/NavLink.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Dropdown from '../Dropdown.vue';
import DropdownLink from '../DropdownLink.vue';

defineProps({
  isDarkMode: Boolean,
});

defineEmits(['toggle-dark-mode']);

const page = usePage();
const current = ref(route().current());

watch(
  () => page.props.ziggy.location,
  (newLocation) => {
    current.value = newLocation;
  }
);
</script>

<template>
  <!-- Navigation Links -->
  <div class="hidden space-x-2 sm:-my-px sm:ms-4 md:flex">
    <NavLink :href="route('dashboard')" :active="current.endsWith('dashboard') && route().current('dashboard')">
      {{ trans('words.dashboard') }}
    </NavLink>
    <NavLink :href="route('clients.index')" :active="route().current('clients.*')">
      {{ trans('words.clients') }}
    </NavLink>
    <NavLink :href="route('projects.index')" :active="route().current('projects.*')">
      {{ trans('words.projects') }}
    </NavLink>
    <NavLink :href="route('tasks.all')" :active="route().current('tasks.*')">
      {{ trans('words.tasks') }}
    </NavLink>

    <Dropdown align="left" width="60" class="self-center">
      <template #trigger>
        <button
          :class="
            route().current('payroll.*') ||
            route().current('employee-profiles.*') ||
            route().current('payslips.*') ||
            route().current('time-entries.*')
              ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
              : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out'
          "
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            ></path>
          </svg>
          Payroll
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
          <div class="block px-4 py-2 text-xs text-gray-400">Payroll Management</div>

          <DropdownLink
            v-if="page.props?.user?.roles?.includes('admin')"
            :href="route('payroll.dashboard')"
            :active="route().current('payroll.dashboard')"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                ></path>
              </svg>
              Dashboard
            </div>
          </DropdownLink>

          <DropdownLink
            v-if="page.props?.user?.roles?.includes('admin')"
            :href="route('payroll.index')"
            :active="route().current('payroll.index')"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                ></path>
              </svg>
              Payrolls
            </div>
          </DropdownLink>

          <!-- Time Entries Link - Visible to employees, managers, and admins -->
          <DropdownLink :href="route('time-entries.index')" :active="route().current('time-entries.*')">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

          <DropdownLink
            v-if="page.props?.user?.roles?.includes('admin')"
            :href="route('employee-profiles.index')"
            :active="route().current('employee-profiles.*')"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.224-1.25-.62-1.722M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.224-1.25.62-1.722M12 12a3 3 0 100-6 3 3 0 000 6z"
                />
              </svg>
              Employee Profiles
            </div>
          </DropdownLink>

          <DropdownLink
            v-if="page.props?.user?.roles?.includes('admin')"
            :href="route('payslips.index')"
            :active="route().current('payslips.index')"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                ></path>
              </svg>
              Payslips
            </div>
          </DropdownLink>

          <div class="border-t border-gray-200 dark:border-gray-600"></div>

          <DropdownLink
            v-if="page.props?.user?.roles?.includes('admin')"
            :href="route('payroll.reports')"
            :active="route().current('payroll.reports')"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 17v-4m3 4v-2m3 2v-6m-9 0h12a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2"
                />
              </svg>
              Reports
            </div>
          </DropdownLink>

          <DropdownLink
            v-if="page.props?.user?.roles?.includes('admin')"
            :href="route('payroll.settings')"
            :active="route().current('payroll.settings')"
          >
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
              Settings
            </div>
          </DropdownLink>
        </div>
      </template>
    </Dropdown>

    <div class="flex items-center">
      <button
        @click="$emit('toggle-dark-mode')"
        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
      >
        <span v-if="isDarkMode">{{ trans('words.light_mode') }}</span>
        <span v-else>{{ trans('words.dark_mode') }}</span>
      </button>
    </div>

    <Locales />
  </div>
</template>
