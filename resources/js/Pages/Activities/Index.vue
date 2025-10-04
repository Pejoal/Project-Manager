<script setup>
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  activities: Object,
  pagination: Object,
  filters: Object,
  filterOptions: Object,
});

const activeTab = ref(Object.keys(props.activities)[0] || 'All');

const setActiveTab = (tab) => {
  activeTab.value = tab;
};

const getActivityIcon = (event) => {
  if (event === 'created') return '➕';
  if (event === 'updated') return '✏️';
  if (event === 'deleted') return '🗑️';
  return '📝';
};

const getActivityColor = (event) => {
  if (event === 'created') return 'bg-green-100 dark:bg-green-900/30 border-green-500 dark:border-green-400';
  if (event === 'updated') return 'bg-blue-100 dark:bg-blue-900/30 border-blue-500 dark:border-blue-400';
  if (event === 'deleted') return 'bg-red-100 dark:bg-red-900/30 border-red-500 dark:border-red-400';
  return 'bg-gray-100 dark:bg-gray-900/30 border-gray-500 dark:border-gray-400';
};

const formatProperties = (properties) => {
  if (!properties || typeof properties !== 'object') return null;

  const formatted = [];

  if (properties.attributes) {
    formatted.push({
      label: 'New Values',
      data: properties.attributes,
    });
  }

  if (properties.old) {
    formatted.push({
      label: 'Old Values',
      data: properties.old,
    });
  }

  return formatted.length > 0 ? formatted : null;
};
</script>

<template>
  <Head :title="$t('words.activities')" />
  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $t('words.activity_log') }}</h1>
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
            {{ key || 'General' }}
            <span class="ml-2 text-xs opacity-75">({{ activityGroup.length }})</span>
          </button>
        </div>
      </div>

      <!-- Activity timeline -->
      <section v-if="activities[activeTab]" class="p-6">
        <div class="space-y-6">
          <div v-for="activity in activities[activeTab]" :key="activity.id" class="relative pl-8 pb-6 last:pb-0">
            <!-- Timeline line -->
            <div
              class="absolute left-[15px] top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"
              :class="{ hidden: activity === activities[activeTab][activities[activeTab].length - 1] }"
            ></div>

            <!-- Timeline dot -->
            <div
              class="absolute left-0 top-1 w-8 h-8 rounded-full flex items-center justify-center border-2"
              :class="getActivityColor(activity.event)"
            >
              <span class="text-sm">{{ getActivityIcon(activity.event) }}</span>
            </div>

            <!-- Activity card -->
            <div
              class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 hover:shadow-md transition-shadow duration-200 border border-gray-200 dark:border-gray-700"
            >
              <div class="flex items-start space-x-4">
                <!-- User avatar -->
                <div v-if="activity.causer" class="flex-shrink-0">
                  <img
                    :src="activity.causer.profile_photo_url"
                    :alt="activity.causer.name"
                    class="h-12 w-12 rounded-full ring-2 ring-white dark:ring-gray-800"
                  />
                </div>

                <!-- Activity details -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <p v-if="activity.causer" class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ activity.causer.name }}
                      </p>
                      <span v-if="activity.causer" class="text-gray-400 dark:text-gray-600">•</span>
                      <p v-if="activity.causer" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ activity.causer.email }}
                      </p>
                    </div>
                    <time class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                      {{ new Date(activity.created_at).toLocaleString() }}
                    </time>
                  </div>

                  <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                    {{ activity.description }}
                  </p>

                  <div class="flex items-center gap-2 flex-wrap">
                    <span
                      v-if="activity.subject_type"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                    >
                      {{ activity.subject_type }}
                    </span>
                    <span
                      v-if="activity.subject_id"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                    >
                      {{ $t('words.id') }}: {{ activity.subject_id }}
                    </span>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="{
                        'bg-green-200 dark:bg-green-900/30 text-green-800 dark:text-green-200':
                          activity.event === 'created',
                        'bg-blue-200 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200':
                          activity.event === 'updated',
                        'bg-red-200 dark:bg-red-900/30 text-red-800 dark:text-red-200': activity.event === 'deleted',
                      }"
                    >
                      {{ activity.event }}
                    </span>
                  </div>

                  <!-- Properties (changed values) -->
                  <div v-if="formatProperties(activity.properties)" class="mt-3 space-y-2">
                    <div
                      v-for="(section, index) in formatProperties(activity.properties)"
                      :key="index"
                      class="text-xs bg-white dark:bg-gray-800 rounded p-2 border border-gray-200 dark:border-gray-700"
                    >
                      <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ section.label }}:</p>
                      <div class="space-y-1">
                        <div v-for="(value, key) in section.data" :key="key" class="flex items-start gap-2">
                          <span class="font-medium text-gray-600 dark:text-gray-400">{{ key }}:</span>
                          <span class="text-gray-800 dark:text-gray-200">{{ value }}</span>
                        </div>
                      </div>
                    </div>
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
        <p class="text-gray-500 dark:text-gray-400">{{ $t('words.no_activities') }}</p>
      </div>

      <!-- Pagination -->
      <div
        v-if="activities[activeTab] && activities[activeTab].length > 0"
        class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-4"
      >
        <Pagination :pagination="pagination" />
      </div>
    </div>
  </main>
</template>
