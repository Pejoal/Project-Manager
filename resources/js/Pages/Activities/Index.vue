<script setup>
import { Link, Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  activities: Object,
});

const activeTab = ref(Object.keys(props.activities)[0]);

const setActiveTab = (tab) => {
  activeTab.value = tab;
};


const form = useForm({
});

const fetchPage = (url) => {
  form.get(url, {
    preserveState: true,
    preserveScroll: true,
  });
};
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
      <!-- Tabs for activity groups -->
      <div class="flex space-x-4 mb-4">
        <button
          v-for="(activityGroup, key) in activities"
          :key="key"
          @click="setActiveTab(key)"
          :class="{
            'bg-blue-500 text-white': activeTab === key,
            'bg-gray-200 dark:bg-gray-700 dark:text-white': activeTab !== key,
          }"
          class="px-4 py-2 rounded-lg"
        >
          {{ key }}
        </button>
      </div>

      <!-- Activity timeline -->
      <section v-if="activities[activeTab]" class="space-y-4">
        <div
          v-for="activity in activities[activeTab]"
          :key="activity.id"
          class="border-b border-gray-200 py-2"
        >
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <img
                :src="activity.user.avatar"
                alt="User Avatar"
                class="h-10 w-10 rounded-full"
              />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ activity.user.name }}
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ activity.description }}
              </p>
              <p class="text-sm text-gray-400 dark:text-gray-500">
                {{ new Date(activity.created_at).toLocaleString() }}
              </p>
            </div>
          </div>
        </div>
      </section>

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
