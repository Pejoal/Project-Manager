<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  href: String,
  as: String,
  active: Boolean, // New prop to toggle active styling
});
</script>

<template>
  <div
    :class="[
      'block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out',
      active ? 'font-bold underline text-gray-900 dark:text-white' : '',
    ]"
  >
    <button v-if="as == 'button'" type="submit">
      <slot />
    </button>

    <a v-else-if="as == 'a'">
      <slot />
    </a>

    <Link v-else :href="href">
      <slot />
    </Link>
  </div>
</template>
