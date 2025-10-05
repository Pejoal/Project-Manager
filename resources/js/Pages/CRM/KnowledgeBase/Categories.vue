<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { IconArrowLeft, IconFileText, IconFolder } from '@tabler/icons-vue';
import { computed } from 'vue';

const props = defineProps({
  categories: Array,
});

const totalArticles = computed(() => {
  return props.categories.reduce((total, category) => total + category.article_count, 0);
});

const sortedCategories = computed(() => {
  return [...props.categories].sort((a, b) => b.article_count - a.article_count);
});
</script>

<template>
  <Head title="Knowledge Base Categories" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('knowledge-base.index')"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <IconArrowLeft class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ $t('crm.knowledge_base_categories') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ categories.length }} {{ $t('crm.categories') }} • {{ totalArticles }} {{ $t('crm.total_articles') }}
            </p>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('knowledge-base.index')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.all_articles') }}
          </Link>
          <Link
            :href="route('knowledge-base.create')"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.create_article') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Categories Overview -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          {{ $t('crm.categories_overview') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
            <div class="flex items-center">
              <IconFolder class="w-8 h-8 text-blue-600 dark:text-blue-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                  {{ $t('crm.total_categories') }}
                </p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  {{ categories.length }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
            <div class="flex items-center">
              <IconFileText class="w-8 h-8 text-green-600 dark:text-green-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">
                  {{ $t('crm.total_articles') }}
                </p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{ totalArticles }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-purple-600 dark:bg-purple-400 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">⌀</span>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-purple-900 dark:text-purple-100">
                  {{ $t('crm.avg_articles_per_category') }}
                </p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  {{ categories.length > 0 ? Math.round(totalArticles / categories.length) : 0 }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="category in sortedCategories"
          :key="category.category"
          class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <IconFolder class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                <div class="ml-3">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ category.category }}
                  </h3>
                </div>
              </div>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
              >
                {{ category.article_count }} {{ category.article_count === 1 ? $t('crm.article') : $t('crm.articles') }}
              </span>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
              <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                <span>{{ $t('crm.article_distribution') }}</span>
                <span>{{ Math.round((category.article_count / totalArticles) * 100) }}%</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div
                  class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all"
                  :style="{ width: `${(category.article_count / totalArticles) * 100}%` }"
                ></div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
              <Link
                :href="route('knowledge-base.index', { category: category.category })"
                class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center text-sm"
              >
                {{ $t('crm.view_articles') }}
              </Link>
              <Link
                :href="route('knowledge-base.create', { category: category.category })"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-3 rounded text-sm"
              >
                +
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="categories.length === 0" class="col-span-full">
          <div class="text-center py-12">
            <IconFolder class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ $t('crm.no_categories') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ $t('crm.no_categories_description') }}
            </p>
            <div class="mt-6">
              <Link
                :href="route('knowledge-base.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                {{ $t('crm.create_first_article') }}
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Management Tips -->
      <div class="mt-8 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
              {{ $t('crm.category_management_tips') }}
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
              <ul class="list-disc list-inside space-y-1">
                <li>{{ $t('crm.tip_organize_logically') }}</li>
                <li>{{ $t('crm.tip_use_clear_names') }}</li>
                <li>{{ $t('crm.tip_avoid_too_many') }}</li>
                <li>{{ $t('crm.tip_review_regularly') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
