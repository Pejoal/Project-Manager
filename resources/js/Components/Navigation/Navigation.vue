<script setup>
import Locales from '@/Components/Locales.vue';
import NavLink from '@/Components/NavLink.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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
