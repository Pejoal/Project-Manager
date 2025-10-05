<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Access the shared breadcrumbs data from the page props
const breadcrumbs = computed(() => usePage().props.breadcrumbs || []);
</script>

<template>
  <!-- Only render the navigation if there is more than one breadcrumb (e.g., more than just the Dashboard) -->
  <nav v-if="breadcrumbs.length > 1" class="text-sm font-medium" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
      <li v-for="(breadcrumb, index) in breadcrumbs" :key="breadcrumb.title">
        <div class="flex items-center">
          <!-- Separator Icon (for all but the first item) -->
          <svg
            v-if="index > 0"
            class="h-5 w-5 flex-shrink-0 text-gray-300 dark:text-gray-500"
            fill="currentColor"
            viewBox="0 0 20 20"
            aria-hidden="true"
          >
            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
          </svg>

          <!-- Link for all items except the very last one -->
          <Link
            v-if="index < breadcrumbs.length - 1 && breadcrumb.url"
            :href="breadcrumb.url"
            class="ml-2 hover:text-gray-700 dark:hover:text-gray-200 hover:underline"
          >
            {{ breadcrumb.title }}
          </Link>

          <!-- The current page (the last item, not a link) -->
          <span v-else class="ml-2 text-gray-700 dark:text-gray-200" aria-current="page">
            {{ breadcrumb.title }}
          </span>
        </div>
      </li>
    </ol>
  </nav>
  <section v-else>
    <!-- Mobile Design (below md: breakpoint, <768px) -->
    <div class="flex items-center justify-center gap-1 md:hidden">
      <h1 class="text-xl font-serif font-semibold text-gray-800 dark:text-gray-200">
        {{ trans('words.welcome') }}
      </h1>
      <p class="text-lg text-gray-600 dark:text-gray-300 font-medium">
        {{ $page.props.auth.user.name }}
      </p>
    </div>

    <!-- Desktop/Tablet Design (md: and above, ≥768px) -->
    <div class="hidden md:flex items-center justify-center">
      <h1 class="text-lg font-serif font-bold text-gray-800 dark:text-gray-100">
        {{ trans('words.welcome') }}, {{ $page.props.auth.user.name }}!
      </h1>
    </div>
  </section>
</template>
