<script setup>
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  activities: Object,
  pagination: Object,
});

const activeTab = ref(Object.keys(props.activities)[0]);

const setActiveTab = (tab) => {
  activeTab.value = tab;
};
</script>

<template>
  <Head :title="trans('words.activities')" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ trans('words.activity_log') }}</h1>
    </template>

    <main class="bg-zinc-900 shadow-md rounded-lg p-4">
      <!-- Tabs for activity groups -->
      <div class="flex space-x-4 overflow-x-auto mb-4">
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
          {{ key.split('\\').pop() }}
        </button>
      </div>

      <!-- Activity timeline -->
      <section v-if="activities[activeTab]" class="space-y-4">
        <div v-for="activity in activities[activeTab]" :key="activity.id" class="border-b border-gray-200 py-2">
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <img
                :src="activity.user.profile_photo_url"
                :alt="trans('words.user_name')"
                class="h-10 w-10 rounded-full"
              />
            </div>
            <div>
              <section class="flex gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                <p>
                  {{ activity.user.name }}
                </p>
                <p class="dark:text-gray-400">
                  {{ activity.user.email }}
                </p>
              </section>
              <p class="text-sm text-gray-500 dark:text-gray-300">
                {{ activity.description }}
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ trans('words.id') }}: {{ activity.subject_id }}</p>
              <p class="text-sm text-gray-400 dark:text-gray-500">
                {{ new Date(activity.created_at).toLocaleString() }}
              </p>
            </div>
          </div>
        </div>
      </section>
      <Pagination :pagination="pagination" />
    </main>
  </AppLayout>
</template>
