<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  activities: Object,
});
</script>

<template>
  <Head title="Activities" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Activity Log
      </h1>
    </template>

    <main class="bg-zinc-900 shadow-md rounded-lg p-4">
      <div
        v-for="activity in activities.data"
        :key="activity.id"
        class="border-b border-gray-200 py-2"
      >
        <p class="dark:text-gray-200">
          <strong>{{ activity.user.name }}: </strong>
          <span class="text-gray-300">{{ activity.description }}</span>
        </p>
        <p class="text-sm dark:text-gray-400" v-if="activity.subject">
          <Link
            :href="route('projects.show', activity?.subject?.slug)"
            class="text-blue-500 hover:underline"
          >
            {{ activity?.subject?.name }}
          </Link>
          - {{ new Date(activity.created_at).toLocaleString() }}
        </p>
      </div>

      <!-- Pagination Controls -->
      <section class="flex items-center justify-between my-2">
        <button
          v-if="props.activities.prev_page_url"
          @click="fetchPage(props.activities.prev_page_url)"
          class="px-4 py-2 bg-blue-500 dark:text-white rounded-lg"
        >
          Previous
        </button>
        <span class="mx-2 dark:text-white"
          >{{ props.activities.current_page }} /
          {{ props.activities.last_page }}</span
        >
        <span class="mx-2 dark:text-white"
          >Total: {{ props.activities.total }}</span
        >
        <button
          v-if="props.activities.next_page_url"
          @click="fetchPage(props.activities.next_page_url)"
          class="px-4 py-2 bg-blue-500 dark:text-white rounded-lg"
        >
          Next
        </button>
      </section>
    </main>
  </AppLayout>
</template>
