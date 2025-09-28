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
    <NavLink :href="route('dashboard')" :active="current.endsWith('dashboard')">
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

    <!-- Payroll Dropdown -->
    <div class="relative self-center">
      <Dropdown align="left" width="60">
        <template #trigger>
          <button
            type="button"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
            :class="{
              'text-gray-900 dark:text-gray-100':
                route().current('dashboard') ||
                route().current('dashboard') ||
                route().current('dashboard'),
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
            <div class="block px-4 py-2 text-xs text-gray-400">Payroll Details</div>

            <DropdownLink :href="route('dashboard')" :active="route().current('dashboard')">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  ></path>
                </svg>
                Link 1
              </div>
            </DropdownLink>

            <DropdownLink :href="route('dashboard')" :active="route().current('dashboard')">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                  ></path>
                </svg>
                Link 2
              </div>
            </DropdownLink>

            <DropdownLink :href="route('dashboard')" :active="route().current('dashboard')">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  ></path>
                </svg>
                Link 3
              </div>
            </DropdownLink>
          </div>
        </template>
      </Dropdown>
    </div>

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
