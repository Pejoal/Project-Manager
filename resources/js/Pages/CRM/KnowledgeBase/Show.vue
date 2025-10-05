<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { IconArrowLeft, IconEye, IconPencil, IconStar, IconTrash } from '@tabler/icons-vue';

const props = defineProps({
  article: Object,
  relatedArticles: Array,
});

const form = useForm({});

const deleteArticle = () => {
  if (confirm('Are you sure you want to delete this article?')) {
    form.delete(route('knowledge-base.destroy', props.article.id));
  }
};

const toggleFeature = () => {
  const action = props.article.is_featured ? 'unfeature' : 'feature';
  useForm().patch(route(`knowledge-base.${action}`, props.article.id), {
    preserveState: true,
  });
};

const updateStatus = (status) => {
  const action = status === 'published' ? 'publish' : 'unpublish';
  useForm().patch(route(`knowledge-base.${action}`, props.article.id), {
    preserveState: true,
  });
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
</script>

<template>
  <Head :title="article.title" />

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
              {{ article.title }}
            </h1>
            <div class="flex items-center space-x-4 mt-2">
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
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
              >
                {{ article.category }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('knowledge-base.edit', article.id)"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <IconPencil class="w-4 h-4 mr-2" />
            {{ $t('words.edit') }}
          </Link>
          <button
            @click="toggleFeature"
            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <IconStar class="w-4 h-4 mr-2" />
            {{ article.is_featured ? $t('crm.unfeature') : $t('crm.feature') }}
          </button>
          <button
            @click="updateStatus(article.status === 'published' ? 'draft' : 'published')"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ article.status === 'published' ? $t('crm.unpublish') : $t('crm.publish') }}
          </button>
          <button
            @click="deleteArticle"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <IconTrash class="w-4 h-4 mr-2" />
            {{ $t('words.delete') }}
          </button>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3">
          <!-- Article Meta -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
              <div class="flex items-center space-x-4">
                <span>{{ $t('words.by') }} {{ article.author?.name }}</span>
                <span>{{ formatDate(article.created_at) }}</span>
                <span class="flex items-center">
                  <IconEye class="w-4 h-4 mr-1" />
                  {{ article.view_count || 0 }} {{ $t('crm.views') }}
                </span>
              </div>
              <div v-if="article.updated_at !== article.created_at">
                {{ $t('words.updated') }} {{ formatDate(article.updated_at) }}
              </div>
            </div>
          </div>

          <!-- Article Excerpt -->
          <div v-if="article.excerpt" class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg mb-6">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
              {{ $t('crm.summary') }}
            </h2>
            <p class="text-blue-800 dark:text-blue-200">
              {{ article.excerpt }}
            </p>
          </div>

          <!-- Article Content -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="prose prose-lg max-w-none dark:prose-invert" v-html="article.content"></div>
          </div>

          <!-- Tags -->
          <div
            v-if="article.tags && article.tags.length > 0"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.tags') }}
            </h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tag in article.tags"
                :key="tag"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
              >
                #{{ tag }}
              </span>
            </div>
          </div>

          <!-- Attachments -->
          <div
            v-if="article.attachments && article.attachments.length > 0"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.attachments') }}
            </h3>
            <div class="space-y-2">
              <div
                v-for="attachment in article.attachments"
                :key="attachment.name"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md"
              >
                <div class="flex items-center">
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ attachment.name }}
                  </span>
                  <span class="ml-2 text-xs text-gray-500 dark:text-gray-400"> ({{ attachment.size }}) </span>
                </div>
                <a
                  :href="attachment.url"
                  target="_blank"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                >
                  {{ $t('words.download') }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Article Details -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.article_details') }}
            </h3>
            <div class="space-y-3 text-sm">
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.category') }}:</span>
                <div class="mt-1">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    {{ article.category }}
                  </span>
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.status') }}:</span>
                <div class="mt-1">
                  <span
                    :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(article.status)}`"
                  >
                    {{ article.status.toUpperCase() }}
                  </span>
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.author') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ article.author?.name }}
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.views') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ article.view_count || 0 }}
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.created') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ formatDate(article.created_at) }}
                </div>
              </div>
              <div v-if="article.updated_at !== article.created_at">
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.updated') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ formatDate(article.updated_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- SEO Details -->
          <div v-if="article.meta_description" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.seo_details') }}
            </h3>
            <div class="space-y-3 text-sm">
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.meta_description') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                  {{ article.meta_description }}
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.slug') }}:</span>
                <div class="mt-1 text-gray-900 dark:text-gray-100 font-mono text-xs">
                  {{ article.slug }}
                </div>
              </div>
            </div>
          </div>

          <!-- Related Articles -->
          <div
            v-if="relatedArticles && relatedArticles.length > 0"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.related_articles') }}
            </h3>
            <div class="space-y-3">
              <Link
                v-for="relatedArticle in relatedArticles"
                :key="relatedArticle.id"
                :href="route('knowledge-base.show', relatedArticle.id)"
                class="block group"
              >
                <div class="p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                  <h4
                    class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400"
                  >
                    {{ relatedArticle.title }}
                  </h4>
                  <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <IconEye class="w-3 h-3 mr-1" />
                    {{ relatedArticle.view_count || 0 }} {{ $t('crm.views') }}
                  </div>
                </div>
              </Link>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.quick_actions') }}
            </h3>
            <div class="space-y-2">
              <Link
                :href="route('knowledge-base.index', { category: article.category })"
                class="block text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ $t('crm.view_category_articles') }}
              </Link>
              <Link
                :href="route('knowledge-base.create')"
                class="block text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ $t('crm.create_new_article') }}
              </Link>
              <Link
                :href="route('knowledge-base.analytics')"
                class="block text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                {{ $t('crm.view_analytics') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
