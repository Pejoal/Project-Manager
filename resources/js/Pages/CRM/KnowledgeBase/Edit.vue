<script setup>
import { ArrowLeftIcon, PencilIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  article: Object,
});

const form = useForm({
  title: props.article.title,
  content: props.article.content,
  excerpt: props.article.excerpt || '',
  category: props.article.category,
  tags: props.article.tags || [],
  status: props.article.status,
  is_featured: props.article.is_featured,
  meta_description: props.article.meta_description || '',
  attachments: props.article.attachments || [],
});

const newTag = ref('');
const newCategory = ref('');

// Tag management
const addTag = () => {
  if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
    form.tags.push(newTag.value.trim());
    newTag.value = '';
  }
};

const removeTag = (index) => {
  form.tags.splice(index, 1);
};

// Category management
const addNewCategory = () => {
  if (newCategory.value.trim()) {
    form.category = newCategory.value.trim();
    newCategory.value = '';
  }
};

// Form submission
const submit = () => {
  form.patch(route('knowledge-base.update', props.article.id), {
    onSuccess: () => {
      // Redirect will be handled by the controller
    },
  });
};

// Predefined categories (you could fetch these from the backend)
const predefinedCategories = [
  'Getting Started',
  'Troubleshooting',
  'Features',
  'API Documentation',
  'Best Practices',
  'FAQ',
  'Tutorials',
  'Integration',
];
</script>

<template>
  <Head :title="`Edit: ${article.title}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('knowledge-base.show', article.id)"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
          >
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ $t('crm.edit_article') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ article.title }}
            </p>
          </div>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('knowledge-base.show', article.id)"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('words.view') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                {{ $t('crm.basic_information') }}
              </h2>

              <!-- Title -->
              <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('words.title') }} *
                </label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.title }"
                />
                <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                  {{ form.errors.title }}
                </div>
              </div>

              <!-- Excerpt -->
              <div class="mb-6">
                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('crm.excerpt') }}
                  <span class="text-gray-500 text-xs">({{ $t('crm.excerpt_description') }})</span>
                </label>
                <textarea
                  id="excerpt"
                  v-model="form.excerpt"
                  rows="3"
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.excerpt }"
                  maxlength="500"
                ></textarea>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ form.excerpt.length }}/500 {{ $t('words.characters') }}
                </div>
                <div v-if="form.errors.excerpt" class="mt-1 text-sm text-red-600">
                  {{ form.errors.excerpt }}
                </div>
              </div>

              <!-- Content -->
              <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('words.content') }} *
                </label>
                <textarea
                  id="content"
                  v-model="form.content"
                  rows="20"
                  required
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.content }"
                  placeholder="Write your article content here. You can use HTML formatting."
                ></textarea>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ $t('crm.html_supported') }}
                </div>
                <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                  {{ form.errors.content }}
                </div>
              </div>
            </div>

            <!-- Article History -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.article_history') }}
              </h2>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.created') }}:</span>
                  <div class="text-gray-900 dark:text-gray-100">
                    {{ new Date(article.created_at).toLocaleDateString() }} by {{ article.author?.name }}
                  </div>
                </div>
                <div>
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('words.updated') }}:</span>
                  <div class="text-gray-900 dark:text-gray-100">
                    {{ new Date(article.updated_at).toLocaleDateString() }}
                  </div>
                </div>
                <div>
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.views') }}:</span>
                  <div class="text-gray-900 dark:text-gray-100">
                    {{ article.view_count || 0 }}
                  </div>
                </div>
                <div>
                  <span class="font-medium text-gray-500 dark:text-gray-400">{{ $t('crm.slug') }}:</span>
                  <div class="text-gray-900 dark:text-gray-100 font-mono text-xs">
                    {{ article.slug }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Publishing Options -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.publishing_options') }}
              </h3>

              <!-- Status -->
              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('words.status') }}
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                >
                  <option value="draft">{{ $t('crm.draft') }}</option>
                  <option value="published">{{ $t('crm.published') }}</option>
                  <option value="archived">{{ $t('crm.archived') }}</option>
                </select>
              </div>

              <!-- Featured -->
              <div class="mb-4">
                <div class="flex items-center">
                  <input
                    id="is_featured"
                    v-model="form.is_featured"
                    type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-700"
                  />
                  <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $t('crm.featured_article') }}
                  </label>
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ $t('crm.featured_description') }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-3">
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex-1 bg-blue-500 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded inline-flex items-center justify-center"
                  >
                    <PencilIcon class="w-4 h-4 mr-2" />
                    {{ form.processing ? $t('words.updating') : $t('words.update') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Category -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('words.category') }}
              </h3>

              <!-- Select existing category -->
              <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('crm.select_category') }}
                </label>
                <select
                  id="category"
                  v-model="form.category"
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.category }"
                >
                  <option value="">{{ $t('crm.select_category') }}</option>
                  <option v-for="category in predefinedCategories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
              </div>

              <!-- Or create new category -->
              <div class="mb-4">
                <label for="new_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('crm.or_create_new') }}
                </label>
                <div class="flex space-x-2">
                  <input
                    id="new_category"
                    v-model="newCategory"
                    type="text"
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :placeholder="$t('crm.new_category_name')"
                  />
                  <button
                    @click="addNewCategory"
                    type="button"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-3 rounded"
                  >
                    {{ $t('words.add') }}
                  </button>
                </div>
              </div>

              <div v-if="form.errors.category" class="text-sm text-red-600">
                {{ form.errors.category }}
              </div>
            </div>

            <!-- Tags -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.tags') }}
              </h3>

              <!-- Add new tag -->
              <div class="mb-4">
                <div class="flex space-x-2">
                  <input
                    v-model="newTag"
                    @keyup.enter="addTag"
                    type="text"
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :placeholder="$t('crm.add_tag')"
                  />
                  <button
                    @click="addTag"
                    type="button"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-3 rounded"
                  >
                    {{ $t('words.add') }}
                  </button>
                </div>
              </div>

              <!-- Current tags -->
              <div v-if="form.tags.length > 0" class="space-y-2">
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="(tag, index) in form.tags"
                    :key="index"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                  >
                    #{{ tag }}
                    <button
                      @click="removeTag(index)"
                      type="button"
                      class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200"
                    >
                      <XMarkIcon class="w-3 h-3" />
                    </button>
                  </span>
                </div>
              </div>
            </div>

            <!-- SEO Settings -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('crm.seo_settings') }}
              </h3>

              <!-- Meta Description -->
              <div class="mb-4">
                <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ $t('crm.meta_description') }}
                </label>
                <textarea
                  id="meta_description"
                  v-model="form.meta_description"
                  rows="3"
                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                  :class="{ 'border-red-500': form.errors.meta_description }"
                  maxlength="160"
                  :placeholder="$t('crm.meta_description_placeholder')"
                ></textarea>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ form.meta_description.length }}/160 {{ $t('words.characters') }}
                </div>
                <div v-if="form.errors.meta_description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.meta_description }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
