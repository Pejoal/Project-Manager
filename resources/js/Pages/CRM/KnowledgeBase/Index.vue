<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { IconChevronDown, IconSearch, IconPlus, IconEye, IconPencil, IconTrash } from '@tabler/icons-vue';

const props = defineProps({
  articles: Object,
  categories: Array,
  filters: Object,
  sort: Object,
});

const form = useForm({
  search: props.filters.search || '',
  category: props.filters.category || '',
  status: props.filters.status || '',
  featured: props.filters.featured || false,
  tag: props.filters.tag || '',
});

const selectedArticles = ref([]);
const bulkActionForm = useForm({
  action: '',
  article_ids: [],
});

// Search and filter
const search = () => {
  router.get(route('knowledge-base.index'), form.data(), {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  form.reset();
  router.get(route('knowledge-base.index'));
};

// Sorting
const sortBy = (field) => {
  const direction = props.sort.sort === field && props.sort.direction === 'asc' ? 'desc' : 'asc';
  router.get(
    route('knowledge-base.index'),
    {
      ...form.data(),
      sort: field,
      direction,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

// Article actions
const deleteArticle = (article) => {
  if (confirm('Are you sure you want to delete this article?')) {
    useForm().delete(route('knowledge-base.destroy', article.id), {
      preserveState: true,
    });
  }
};

const toggleFeature = (article) => {
  const action = article.is_featured ? 'unfeature' : 'feature';
  useForm().patch(route(`knowledge-base.${action}`, article.id), {
    preserveState: true,
  });
};

const updateStatus = (article, status) => {
  const action = status === 'published' ? 'publish' : 'unpublish';
  useForm().patch(route(`knowledge-base.${action}`, article.id), {
    preserveState: true,
  });
};

// Bulk actions
const toggleSelectAll = () => {
  if (selectedArticles.value.length === props.articles.data.length) {
    selectedArticles.value = [];
  } else {
    selectedArticles.value = props.articles.data.map((article) => article.id);
  }
};

const executeBulkAction = () => {
  if (!bulkActionForm.action || selectedArticles.value.length === 0) return;

  bulkActionForm.article_ids = selectedArticles.value;

  if (bulkActionForm.action === 'delete') {
    if (!confirm(`Are you sure you want to delete ${selectedArticles.value.length} articles?`)) return;
  }

  bulkActionForm.post(route('knowledge-base.bulk-action'), {
    preserveState: true,
    onSuccess: () => {
      selectedArticles.value = [];
      bulkActionForm.reset();
    },
  });
};

// Utility functions
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
  return content.length > length ? content.substring(0, length) + '...' : content;
};
</script>

<template>
  <Head title="Knowledge Base" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('crm.knowledge_base') }}
        </h1>
        <div class="flex space-x-3">
          <Link
            :href="route('knowledge-base.categories')"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.categories') }}
          </Link>
          <Link
            :href="route('knowledge-base.analytics')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.analytics') }}
          </Link>
          <Link
            :href="route('knowledge-base.create')"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <IconPlus class="w-4 h-4 mr-2" />
            {{ $t('crm.create_article') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Search and Filters -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
          <!-- Search -->
          <div class="relative">
            <IconSearch class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
            <input
              v-model="form.search"
              @keyup.enter="search"
              type="text"
              :placeholder="$t('words.search')"
              class="pl-10 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
            />
          </div>

          <!-- Category Filter -->
          <select
            v-model="form.category"
            @change="search"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          >
            <option value="">{{ $t('crm.all_categories') }}</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>

          <!-- Status Filter -->
          <select
            v-model="form.status"
            @change="search"
            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
          >
            <option value="">{{ $t('crm.all_statuses') }}</option>
            <option value="published">{{ $t('crm.published') }}</option>
            <option value="draft">{{ $t('crm.draft') }}</option>
            <option value="archived">{{ $t('crm.archived') }}</option>
          </select>

          <!-- Featured Filter -->
          <div class="flex items-center">
            <input
              v-model="form.featured"
              @change="search"
              type="checkbox"
              id="featured"
              class="rounded border-gray-300 dark:border-gray-700"
            />
            <label for="featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
              {{ $t('crm.featured_only') }}
            </label>
          </div>

          <!-- Actions -->
          <div class="flex space-x-2">
            <button @click="search" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.search') }}
            </button>
            <button @click="clearFilters" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              {{ $t('words.clear') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedArticles.length > 0" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg mb-6">
        <div class="flex items-center justify-between">
          <span class="text-sm text-blue-700 dark:text-blue-300">
            {{ selectedArticles.length }} {{ $t('crm.articles_selected') }}
          </span>
          <div class="flex items-center space-x-4">
            <select
              v-model="bulkActionForm.action"
              class="text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
            >
              <option value="">{{ $t('crm.bulk_actions') }}</option>
              <option value="publish">{{ $t('crm.publish') }}</option>
              <option value="unpublish">{{ $t('crm.unpublish') }}</option>
              <option value="archive">{{ $t('crm.archive') }}</option>
              <option value="feature">{{ $t('crm.feature') }}</option>
              <option value="unfeature">{{ $t('crm.unfeature') }}</option>
              <option value="delete">{{ $t('words.delete') }}</option>
            </select>
            <button
              @click="executeBulkAction"
              :disabled="!bulkActionForm.action"
              class="bg-blue-500 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded text-sm"
            >
              {{ $t('words.execute') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Articles Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left">
                <input
                  type="checkbox"
                  @change="toggleSelectAll"
                  :checked="selectedArticles.length === articles.data.length && articles.data.length > 0"
                  class="rounded border-gray-300 dark:border-gray-700"
                />
              </th>
              <th
                @click="sortBy('title')"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
              >
                {{ $t('words.title') }}
                <IconChevronDown v-if="sort.sort === 'title'" class="w-4 h-4 inline ml-1" />
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.category') }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.status') }}
              </th>
              <th
                @click="sortBy('view_count')"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
              >
                {{ $t('crm.views') }}
                <IconChevronDown v-if="sort.sort === 'view_count'" class="w-4 h-4 inline ml-1" />
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.author') }}
              </th>
              <th
                @click="sortBy('created_at')"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
              >
                {{ $t('words.created_at') }}
                <IconChevronDown v-if="sort.sort === 'created_at'" class="w-4 h-4 inline ml-1" />
              </th>
              <th
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              >
                {{ $t('words.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="article in articles.data" :key="article.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  v-model="selectedArticles"
                  :value="article.id"
                  type="checkbox"
                  class="rounded border-gray-300 dark:border-gray-700"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ article.title }}
                      <span
                        v-if="article.is_featured"
                        class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                      >
                        {{ $t('crm.featured') }}
                      </span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ truncateContent(article.excerpt || article.content) }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                >
                  {{ article.category }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(article.status)}`"
                >
                  {{ article.status.toUpperCase() }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ article.view_count || 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ article.author?.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ formatDate(article.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <Link
                  :href="route('knowledge-base.show', article.id)"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                  title="View"
                >
                  <IconEye class="w-4 h-4 inline" />
                </Link>
                <Link
                  :href="route('knowledge-base.edit', article.id)"
                  class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                  title="Edit"
                >
                  <IconPencil class="w-4 h-4 inline" />
                </Link>
                <button
                  @click="toggleFeature(article)"
                  class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                  :title="article.is_featured ? 'Unfeature' : 'Feature'"
                >
                  ⭐
                </button>
                <button
                  @click="updateStatus(article, article.status === 'published' ? 'draft' : 'published')"
                  class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                  :title="article.status === 'published' ? 'Unpublish' : 'Publish'"
                >
                  {{ article.status === 'published' ? '📤' : '📥' }}
                </button>
                <button
                  @click="deleteArticle(article)"
                  class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                  title="Delete"
                >
                  <IconTrash class="w-4 h-4 inline" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div
          v-if="articles.links"
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
    </div>
  </div>
</template>
