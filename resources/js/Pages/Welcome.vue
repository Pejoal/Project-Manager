<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
});
</script>

<template>
  <Head title="Welcome" />
  <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
    <img
      id="background"
      class="absolute -left-20 top-0 max-w-[877px]"
      src="https://laravel.com/assets/img/welcome/background.svg"
    />
    <div
      class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
    >
      <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
        <h1 class="text-2xl font-medium text-gray-900 dark:text-white">
          Welcome to your Company Management System!
        </h1>

        <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
          <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
          >
            Dashboard
          </Link>

          <template v-else>
            <Link
              :href="route('login')"
              class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
              Log in
            </Link>

            <Link
              v-if="canRegister"
              :href="route('register')"
              class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
              Register
            </Link>
          </template>
        </nav>
      </div>
    </div>
  </div>
</template>
