<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { IconArrowLeft, IconChartBar, IconEye, IconFileText, IconStar } from '@tabler/icons-vue';
import { computed } from 'vue';

const props = defineProps({
  stats: Object,
  topArticles: Array,
  categoriesStats: Array,
  monthlyViews: Array,
});

const formatNumber = (number) => {
  return number?.toLocaleString() || 0;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const getStatusColor = (status) => {
  const colors = {
    published: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    archived: 'bg-gray-100 text-gray-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const truncateContent = (content, length = 100) => {
  return content && content.length > length ? content.substring(0, length) + '...' : content;
};

const totalCategoryViews = computed(() => {
  return props.categoriesStats.reduce((total, category) => total + (category.total_views || 0), 0);
});

const maxCategoryViews = computed(() => {
  return Math.max(...props.categoriesStats.map((category) => category.total_views || 0));
});

const getCategoryBarWidth = (views) => {
  return maxCategoryViews.value > 0 ? (views / maxCategoryViews.value) * 100 : 0;
};

const maxMonthlyViews = computed(() => {
  return Math.max(...props.monthlyViews.map((month) => month.views || 0));
});

const getMonthlyBarHeight = (views) => {
  return maxMonthlyViews.value > 0 ? (views / maxMonthlyViews.value) * 100 : 0;
};

const formatMonth = (monthString) => {
  const date = new Date(monthString + '-01');
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
};
</script>

<template>
  <Head title="Knowledge Base Analytics" />

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
              {{ $t('crm.knowledge_base_analytics') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ $t('crm.analytics_overview') }}
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
            :href="route('knowledge-base.popular')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.popular_articles') }}
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
      <!-- Key Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <IconFileText class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $t('crm.total_articles') }}
              </p>
              <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ formatNumber(stats.total_articles) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-green-600 dark:bg-green-400 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">✓</span>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $t('crm.published') }}
              </p>
              <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ formatNumber(stats.published_articles) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-yellow-600 dark:bg-yellow-400 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">📝</span>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $t('crm.drafts') }}
              </p>
              <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                {{ formatNumber(stats.draft_articles) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <IconEye class="w-8 h-8 text-purple-600 dark:text-purple-400" />
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $t('crm.total_views') }}
              </p>
              <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                {{ formatNumber(stats.total_views) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-indigo-600 dark:bg-indigo-400 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-bold">⌀</span>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $t('crm.avg_views') }}
              </p>
              <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ Math.round(stats.avg_views_per_article || 0) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <div class="flex items-center">
            <IconStar class="w-8 h-8 text-orange-600 dark:text-orange-400" />
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $t('crm.featured') }}
              </p>
              <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                {{ formatNumber(stats.featured_articles) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Articles -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
              <IconChartBar class="w-5 h-5 mr-2" />
              {{ $t('crm.top_performing_articles') }}
            </h2>
          </div>
          <div class="p-6">
            <div v-if="topArticles.length > 0" class="space-y-4">
              <div
                v-for="(article, index) in topArticles"
                :key="article.id"
                class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
              >
                <div class="flex-shrink-0">
                  <span
                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm font-medium"
                  >
                    {{ index + 1 }}
                  </span>
                </div>
                <div class="flex-1 min-w-0">
                  <Link :href="route('knowledge-base.show', article.id)" class="block group">
                    <h3
                      class="text-sm font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-1"
                    >
                      {{ article.title }}
                    </h3>
                  </Link>
                  <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                    <span
                      :class="`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${getStatusColor(article.status)}`"
                    >
                      {{ article.status.toUpperCase() }}
                    </span>
                    <span>{{ article.category }}</span>
                    <span>{{ $t('words.by') }} {{ article.author?.name }}</span>
                  </div>
                </div>
                <div class="flex-shrink-0 text-right">
                  <div class="flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400">
                    <IconEye class="w-4 h-4 mr-1" />
                    {{ formatNumber(article.view_count) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $t('crm.views') }}
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <IconFileText class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $t('crm.no_articles_yet') }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('crm.create_first_article_to_see_analytics') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Categories Performance -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
              <div class="w-5 h-5 bg-green-600 dark:bg-green-400 rounded mr-2"></div>
              {{ $t('crm.categories_performance') }}
            </h2>
          </div>
          <div class="p-6">
            <div v-if="categoriesStats.length > 0" class="space-y-4">
              <div v-for="category in categoriesStats" :key="category.category" class="space-y-2">
                <div class="flex justify-between items-center">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ category.category }}
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ category.count }} {{ category.count === 1 ? $t('crm.article') : $t('crm.articles') }}
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                      {{ formatNumber(category.total_views) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ $t('crm.views') }}
                    </div>
                  </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div
                    class="bg-green-600 dark:bg-green-400 h-2 rounded-full transition-all"
                    :style="{ width: `${getCategoryBarWidth(category.total_views)}%` }"
                  ></div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <div
                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-4 flex items-center justify-center"
              >
                <span class="text-gray-400 text-lg">📁</span>
              </div>
              <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $t('crm.no_categories_yet') }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('crm.categories_will_appear_here') }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Views Chart -->
      <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <div class="w-5 h-5 bg-blue-600 dark:bg-blue-400 rounded mr-2"></div>
            {{ $t('crm.monthly_views_trend') }}
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $t('crm.last_12_months') }}
          </p>
        </div>
        <div class="p-6">
          <div v-if="monthlyViews.length > 0" class="space-y-4">
            <!-- Chart -->
            <div class="flex items-end justify-between h-64 space-x-2">
              <div v-for="month in monthlyViews" :key="month.month" class="flex-1 flex flex-col items-center">
                <div class="w-full flex items-end justify-center mb-2" style="height: 200px">
                  <div
                    class="w-full bg-blue-500 dark:bg-blue-400 rounded-t transition-all hover:bg-blue-600 dark:hover:bg-blue-300 group relative"
                    :style="{
                      height: `${getMonthlyBarHeight(month.views)}%`,
                      minHeight: month.views > 0 ? '4px' : '0',
                    }"
                  >
                    <!-- Tooltip -->
                    <div
                      class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap"
                    >
                      {{ formatNumber(month.views) }} {{ $t('crm.views') }}
                    </div>
                  </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                  {{ formatMonth(month.month) }}
                </div>
              </div>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  {{ formatNumber(monthlyViews.reduce((sum, month) => sum + month.views, 0)) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ $t('crm.total_views_last_12_months') }}
                </div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{
                    Math.round(
                      monthlyViews.reduce((sum, month) => sum + month.views, 0) / Math.max(monthlyViews.length, 1)
                    )
                  }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ $t('crm.avg_monthly_views') }}
                </div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  {{ formatNumber(Math.max(...monthlyViews.map((month) => month.views))) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ $t('crm.peak_monthly_views') }}
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12">
            <IconChartBar class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ $t('crm.no_view_data') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ $t('crm.view_data_will_appear_here') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Analytics Insights -->
      <div
        class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 border-l-4 border-blue-400 p-6 rounded-lg"
      >
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200">
              {{ $t('crm.analytics_insights') }}
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <h4 class="font-semibold mb-2">{{ $t('crm.content_insights') }}</h4>
                  <ul class="list-disc list-inside space-y-1">
                    <li v-if="stats.published_articles > 0">
                      {{ Math.round((stats.published_articles / stats.total_articles) * 100) }}%
                      {{ $t('crm.of_articles_published') }}
                    </li>
                    <li v-if="stats.featured_articles > 0">
                      {{ Math.round((stats.featured_articles / stats.total_articles) * 100) }}%
                      {{ $t('crm.of_articles_featured') }}
                    </li>
                    <li v-if="stats.total_views > 0 && stats.total_articles > 0">
                      {{ Math.round(stats.total_views / stats.total_articles) }} {{ $t('crm.avg_views_per_article') }}
                    </li>
                  </ul>
                </div>
                <div>
                  <h4 class="font-semibold mb-2">{{ $t('crm.recommendations') }}</h4>
                  <ul class="list-disc list-inside space-y-1">
                    <li v-if="stats.draft_articles > stats.published_articles">
                      {{ $t('crm.consider_publishing_drafts') }}
                    </li>
                    <li v-if="stats.featured_articles < 3">
                      {{ $t('crm.feature_popular_articles') }}
                    </li>
                    <li v-if="categoriesStats.length < 3">
                      {{ $t('crm.organize_with_categories') }}
                    </li>
                    <li>{{ $t('crm.update_content_regularly') }}</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
