<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { IconArrowLeft, IconClock, IconEye, IconStar } from '@tabler/icons-vue';

const props = defineProps({
  articles: Array,
});

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

const truncateContent = (content, length = 150) => {
  return content && content.length > length ? content.substring(0, length) + '...' : content;
};
</script>

<template>
  <Head title="Popular Knowledge Base Articles" />

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
              {{ $t('crm.popular_articles') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ $t('crm.most_viewed_articles') }}
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
            :href="route('knowledge-base.analytics')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.analytics') }}
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
      <!-- Popular Articles Overview -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          {{ $t('crm.popularity_overview') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
            <div class="flex items-center">
              <IconEye class="w-8 h-8 text-blue-600 dark:text-blue-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                  {{ $t('crm.total_views') }}
                </p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  {{ articles.reduce((total, article) => total + (article.view_count || 0), 0).toLocaleString() }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
            <div class="flex items-center">
              <IconStar class="w-8 h-8 text-green-600 dark:text-green-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">
                  {{ $t('crm.featured_articles') }}
                </p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{ articles.filter((article) => article.is_featured).length }}
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
                  {{ $t('crm.avg_views') }}
                </p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  {{
                    articles.length > 0
                      ? Math.round(
                          articles.reduce((total, article) => total + (article.view_count || 0), 0) / articles.length
                        )
                      : 0
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Performers -->
      <div v-if="articles.length > 0" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          {{ $t('crm.top_performers') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="(article, index) in articles.slice(0, 3)" :key="article.id" class="relative">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-4 rounded-lg text-white">
              <div class="flex items-center justify-between mb-2">
                <span class="text-2xl font-bold">#{{ index + 1 }}</span>
                <div class="flex items-center text-sm">
                  <IconEye class="w-4 h-4 mr-1" />
                  {{ article.view_count || 0 }}
                </div>
              </div>
              <h3 class="font-semibold text-sm mb-1 line-clamp-2">
                {{ article.title }}
              </h3>
              <p class="text-xs opacity-75">
                {{ article.category }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Articles List -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $t('crm.all_popular_articles') }}
          </h2>
        </div>

        <div v-if="articles.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="(article, index) in articles"
            :key="article.id"
            class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1 min-w-0">
                <div class="flex items-center mb-2">
                  <span
                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm font-medium mr-3"
                  >
                    {{ index + 1 }}
                  </span>
                  <div class="flex items-center space-x-2">
                    <span
                      :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(article.status)}`"
                    >
                      {{ article.status.toUpperCase() }}
                    </span>
                    <span
                      v-if="article.is_featured"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                    >
                      <IconStar class="w-3 h-3 mr-1" />
                      {{ $t('crm.featured') }}
                    </span>
                    <span
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      {{ article.category }}
                    </span>
                  </div>
                </div>

                <Link :href="route('knowledge-base.show', article.id)" class="block group">
                  <h3
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-2"
                  >
                    {{ article.title }}
                  </h3>
                </Link>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                  {{ truncateContent(article.excerpt || article.content) }}
                </p>

                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                  <div class="flex items-center">
                    <IconClock class="w-4 h-4 mr-1" />
                    {{ formatDate(article.created_at) }}
                  </div>
                  <div>{{ $t('words.by') }} {{ article.author?.name }}</div>
                </div>
              </div>

              <div class="ml-4 flex-shrink-0">
                <div class="text-right">
                  <div class="flex items-center text-lg font-bold text-blue-600 dark:text-blue-400">
                    <IconEye class="w-5 h-5 mr-1" />
                    {{ (article.view_count || 0).toLocaleString() }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $t('crm.views') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-12 text-center">
          <IconEye class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $t('crm.no_popular_articles') }}
          </h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $t('crm.no_popular_articles_description') }}
          </p>
          <div class="mt-6">
            <Link
              :href="route('knowledge-base.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              {{ $t('crm.create_article') }}
            </Link>
          </div>
        </div>
      </div>

      <!-- Popularity Tips -->
      <div class="mt-8 bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 p-4">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
              {{ $t('crm.boost_article_popularity') }}
            </h3>
            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
              <ul class="list-disc list-inside space-y-1">
                <li>{{ $t('crm.tip_clear_titles') }}</li>
                <li>{{ $t('crm.tip_comprehensive_content') }}</li>
                <li>{{ $t('crm.tip_regular_updates') }}</li>
                <li>{{ $t('crm.tip_proper_categorization') }}</li>
                <li>{{ $t('crm.tip_internal_linking') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
