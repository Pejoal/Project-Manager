<template>
  <Head title="Welcome" />
  <div
    class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100 min-h-screen flex flex-col"
  >
    <nav
      v-if="canLogin"
      class="flex justify-end p-4 bg-white shadow-lg dark:bg-gray-800"
    >
      <Link
        v-if="$page.props.auth.user"
        :href="route('dashboard')"
        class="text-gray-800 hover:text-blue-600 dark:text-gray-100 dark:hover:text-blue-400 font-medium"
      >
        Dashboard
      </Link>
      <template v-else>
        <Link
          :href="route('login')"
          class="ml-4 text-gray-800 hover:text-blue-600 dark:text-gray-100 dark:hover:text-blue-400 font-medium"
        >
          Log in
        </Link>
        <Link
          v-if="canRegister"
          :href="route('register')"
          class="ml-4 text-gray-800 hover:text-blue-600 dark:text-gray-100 dark:hover:text-blue-400 font-medium"
        >
          Register
        </Link>
      </template>
    </nav>
    <div class="flex-grow flex items-center justify-center">
      <h1 class="text-3xl font-extrabold text-center px-6 leading-relaxed">
        Stay Organized and Achieve More: Your Ultimate To-Do List App!
      </h1>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
});
</script>
