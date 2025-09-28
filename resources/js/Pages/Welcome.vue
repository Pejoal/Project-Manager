<script setup>
import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
});

defineOptions({
  layout: WelcomeLayout,
});
</script>

<template>
  <Head :title="trans('words.welcome')" />
  <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
    <img
      id="background"
      class="absolute -left-20 top-0 max-w-[877px] max-h-full"
      src="https://laravel.com/assets/img/welcome/background.svg"
    />
    <div
      class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
    >
      <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
          {{ trans('words.welcome_title') }}
        </h1>
        <p class="text-lg text-gray-700 dark:text-gray-300 mb-8">
          {{ trans('words.welcome_description') }}
        </p>

        <nav v-if="canLogin" class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="rounded-md px-6 py-3 bg-[#FF2D20] text-white ring-1 ring-transparent transition hover:bg-[#e0261c] focus:outline-none focus-visible:ring-[#FF2D20] dark:bg-[#FF2D20] dark:hover:bg-[#e0261c] dark:focus-visible:ring-white"
          >
            {{ trans('words.dashboard') }}
          </Link>

          <template v-else>
            <Link
              :href="route('login')"
              class="rounded-md px-6 py-3 bg-[#FF2D20] text-white ring-1 ring-transparent transition hover:bg-[#e0261c] focus:outline-none focus-visible:ring-[#FF2D20] dark:bg-[#FF2D20] dark:hover:bg-[#e0261c] dark:focus-visible:ring-white"
            >
              {{ trans('words.login') }}
            </Link>

            <Link
              v-if="canRegister"
              :href="route('register')"
              class="rounded-md px-6 py-3 bg-gray-900 text-white ring-1 ring-transparent transition hover:bg-gray-700 focus:outline-none focus-visible:ring-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus-visible:ring-white"
            >
              {{ trans('words.register') }}
            </Link>
          </template>
        </nav>
      </div>
    </div>
  </div>
</template>
