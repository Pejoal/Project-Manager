<script setup>
import { ArrowLeftIcon, ClockIcon, EyeIcon, MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  articles: Object,
  query: String,
});

const searchForm = useForm({
  q: props.query || '',
});

const searchInput = ref(null);

const search = () => {
  if (searchForm.q.trim().length < 3) {
    alert('Please enter at least 3 characters to search.');
    return;
  }

  router.get(
    route('knowledge-base.search'),
    { q: searchForm.q },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

const clearSearch = () => {
  searchForm.q = '';
  searchInput.value?.focus();
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

const truncateContent = (content, length = 200) => {
  return content && content.length > length ? content.substring(0, length) + '...' : content;
};

const highlightSearchTerm = (text, query) => {
  if (!query || !text) return text;
  const regex = new RegExp(`(${query})`, 'gi');
  return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-800 px-1 rounded">$1</mark>');
};

const searchStats = computed(() => {
  if (!props.articles.data) return null;

  return {
    total: props.articles.total,
    categories: [...new Set(props.articles.data.map((article) => article.category))].length,
    avgViews:
      props.articles.data.length > 0
        ? Math.round(
            props.articles.data.reduce((sum, article) => sum + (article.view_count || 0), 0) /
              props.articles.data.length
          )
        : 0,
  };
});

onMounted(() => {
  searchInput.value?.focus();
});
</script>

<template>
  <Head :title="`Search: ${query || 'Knowledge Base'}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('knowledge-base.index')"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ $t('crm.search_knowledge_base') }}
            </h1>
            <p v-if="query" class="text-sm text-gray-600 dark:text-gray-400">
              {{ $t('crm.search_results_for') }}: "{{ query }}"
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
      <!-- Search Form -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <form @submit.prevent="search" class="space-y-4">
          <div class="relative">
            <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
            <input
              ref="searchInput"
              v-model="searchForm.q"
              type="text"
              :placeholder="$t('crm.search_placeholder')"
              class="w-full pl-10 pr-10 py-3 text-lg rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
              minlength="3"
            />
            <button
              v-if="searchForm.q"
              @click="clearSearch"
              type="button"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ $t('crm.search_tip') }}
            </div>
            <button
              type="submit"
              :disabled="searchForm.processing || searchForm.q.length < 3"
              class="bg-blue-500 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded inline-flex items-center"
            >
              <MagnifyingGlassIcon class="w-4 h-4 mr-2" />
              {{ searchForm.processing ? $t('words.searching') : $t('words.search') }}
            </button>
          </div>
        </form>
      </div>

      <!-- Search Statistics -->
      <div v-if="query && searchStats" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          {{ $t('crm.search_statistics') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-600 dark:bg-blue-400 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">#</span>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                  {{ $t('crm.results_found') }}
                </p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  {{ searchStats.total }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-green-600 dark:bg-green-400 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">📁</span>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">
                  {{ $t('crm.categories_found') }}
                </p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{ searchStats.categories }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
            <div class="flex items-center">
              <EyeIcon class="w-8 h-8 text-purple-600 dark:text-purple-400" />
              <div class="ml-3">
                <p class="text-sm font-medium text-purple-900 dark:text-purple-100">
                  {{ $t('crm.avg_views') }}
                </p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  {{ searchStats.avgViews }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Results -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div v-if="query" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $t('crm.search_results') }}
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ articles.total }} {{ articles.total === 1 ? $t('crm.result') : $t('crm.results') }}
            {{ $t('words.for') }} "{{ query }}"
          </p>
        </div>

        <!-- Results List -->
        <div v-if="articles.data && articles.data.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="article in articles.data"
            :key="article.id"
            class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1 min-w-0">
                <div class="flex items-center mb-2 space-x-2">
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(article.status)}`"
                  >
                    {{ article.status.toUpperCase() }}
                  </span>
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    {{ article.category }}
                  </span>
                  <span
                    v-if="article.is_featured"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                  >
                    {{ $t('crm.featured') }}
                  </span>
                </div>

                <Link :href="route('knowledge-base.show', article.id)" class="block group">
                  <h3
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 mb-2"
                    v-html="highlightSearchTerm(article.title, query)"
                  ></h3>
                </Link>

                <div
                  class="text-sm text-gray-600 dark:text-gray-400 mb-3"
                  v-html="highlightSearchTerm(truncateContent(article.excerpt || article.content), query)"
                ></div>

                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                  <div class="flex items-center">
                    <ClockIcon class="w-4 h-4 mr-1" />
                    {{ formatDate(article.created_at) }}
                  </div>
                  <div>{{ $t('words.by') }} {{ article.author?.name }}</div>
                  <div class="flex items-center">
                    <EyeIcon class="w-4 h-4 mr-1" />
                    {{ article.view_count || 0 }} {{ $t('crm.views') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Results -->
        <div v-else-if="query" class="p-12 text-center">
          <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $t('crm.no_results_found') }}
          </h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $t('crm.no_results_description') }} "{{ query }}"
          </p>
          <div class="mt-6">
            <button
              @click="clearSearch"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 mr-3"
            >
              {{ $t('crm.try_different_search') }}
            </button>
            <Link
              :href="route('knowledge-base.create')"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              {{ $t('crm.create_article') }}
            </Link>
          </div>
        </div>

        <!-- Initial State -->
        <div v-else class="p-12 text-center">
          <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $t('crm.search_knowledge_base') }}
          </h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $t('crm.search_description') }}
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="articles.links && articles.data && articles.data.length > 0"
          class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6"
        >
          <div class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
              <Link
                v-if="articles.prev_page_url"
                :href="articles.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                {{ $t('words.previous') }}
              </Link>
              <Link
                v-if="articles.next_page_url"
                :href="articles.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                {{ $t('words.next') }}
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  {{ $t('words.showing') }}
                  <span class="font-medium">{{ articles.from }}</span>
                  {{ $t('words.to') }}
                  <span class="font-medium">{{ articles.to }}</span>
                  {{ $t('words.of') }}
                  <span class="font-medium">{{ articles.total }}</span>
                  {{ $t('words.results') }}
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <Link
                    v-for="link in articles.links"
                    :key="link.label"
                    :href="link.url"
                    v-html="link.label"
                    :class="{
                      'relative inline-flex items-center px-2 py-2 border text-sm font-medium': true,
                      'bg-blue-50 border-blue-500 text-blue-600': link.active,
                      'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                      'cursor-not-allowed opacity-50': !link.url,
                    }"
                  />
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Tips -->
      <div class="mt-8 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
              {{ $t('crm.search_tips') }}
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
              <ul class="list-disc list-inside space-y-1">
                <li>{{ $t('crm.tip_use_keywords') }}</li>
                <li>{{ $t('crm.tip_check_spelling') }}</li>
                <li>{{ $t('crm.tip_try_synonyms') }}</li>
                <li>{{ $t('crm.tip_use_quotes') }}</li>
                <li>{{ $t('crm.tip_browse_categories') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
