<script setup>
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  activities: Object,
  pagination: Object,
});

const activeTab = ref(Object.keys(props.activities)[0]);

const setActiveTab = (tab) => {
  activeTab.value = tab;
};

const getActivityIcon = (type) => {
  if (type.includes('created')) return '➕';
  if (type.includes('updated')) return '✏️';
  if (type.includes('deleted')) return '🗑️';
  return '📝';
};
</script>

<template>
  <Head :title="trans('words.activities')" />
  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ trans('words.activity_log') }}</h1>
    </div>
  </header>

  <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
      <!-- Tabs for activity groups -->
      <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
        <div class="flex space-x-1 overflow-x-auto p-2">
          <button
            v-for="(activityGroup, key) in activities"
            :key="key"
            @click="setActiveTab(key)"
            :class="{
              'bg-blue-500 text-white shadow-sm': activeTab === key,
              'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700': activeTab !== key,
            }"
            class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 whitespace-nowrap"
          >
            {{ key.split('\\').pop() }}
          </button>
        </div>
      </div>

      <!-- Activity timeline -->
      <section v-if="activities[activeTab]" class="p-6">
        <div class="space-y-6">
          <div 
            v-for="activity in activities[activeTab]" 
            :key="activity.id" 
            class="relative pl-8 pb-6 last:pb-0"
          >
            <!-- Timeline line -->
            <div 
              class="absolute left-[15px] top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700 last:hidden"
              :class="{ 'hidden': activity === activities[activeTab][activities[activeTab].length - 1] }"
            ></div>
            
            <!-- Timeline dot -->
            <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center border-2 border-blue-500 dark:border-blue-400">
              <span class="text-sm">{{ getActivityIcon(activity.type) }}</span>
            </div>

            <!-- Activity card -->
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 hover:shadow-md transition-shadow duration-200 border border-gray-200 dark:border-gray-700">
              <div class="flex items-start space-x-4">
                <!-- User avatar -->
                <div class="flex-shrink-0">
                  <img
                    :src="activity.user.profile_photo_url"
                    :alt="activity.user.name"
                    class="h-12 w-12 rounded-full ring-2 ring-white dark:ring-gray-800"
                  />
                </div>

                <!-- Activity details -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ activity.user.name }}
                      </p>
                      <span class="text-gray-400 dark:text-gray-600">•</span>
                      <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ activity.user.email }}
                      </p>
                    </div>
                    <time class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                      {{ new Date(activity.created_at).toLocaleString() }}
                    </time>
                  </div>

                  <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ activity.description }}
                  </p>

                  <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                      {{ trans('words.id') }}: {{ activity.subject_id }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Empty state -->
      <div v-else class="p-12 text-center">
        <div class="text-gray-400 dark:text-gray-500 text-5xl mb-4">📋</div>
        <p class="text-gray-500 dark:text-gray-400">{{ trans('words.no_activities') }}</p>
      </div>

      <!-- Pagination -->
      <div v-if="activities[activeTab] && activities[activeTab].length > 0" class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4">
        <Pagination :pagination="pagination" />
      </div>
    </div>
  </main>
</template>
