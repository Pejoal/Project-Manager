<script setup>
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, defineProps, ref } from 'vue';
import CreateMilestoneModal from './CreateMilestoneModal.vue';

const props = defineProps({
  milestones: Object,
  project: Object,
  phases: Array,
});

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const pagination = computed(() => {
  return {
    prev_page_url: props.milestones.prev_page_url,
    current_page: props.milestones.current_page,
    last_page: props.milestones.last_page,
    total: props.milestones.total,
    next_page_url: props.milestones.next_page_url,
  };
});
</script>

<template>
  <Head :title="`${trans('words.milestones')} for ${project.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        <section>
          {{ trans('words.milestones') }} for
          <Link
            :href="route('projects.show', { project: project.slug })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ project.name }}
          </Link>
        </section>
      </h1>
    </template>
    <div class="p-2 my-1 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <CreateMilestoneModal :show="showModal" :project="project" :phases="phases" @close="closeModal" />
      <button @click="openModal" class="text-blue-500 dark:text-blue-400 hover:underline">
        {{ trans('words.create_milestone') }}
      </button>

      <ul class="my-2 space-y-4">
        <li
          v-for="milestone in milestones.data"
          :key="milestone.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>
            <p>
              {{ trans('words.milestone') }}:
              <Link
                :href="
                  route('milestones.show', {
                    project: project ? project.slug : milestone.project.slug,
                    milestone: milestone.id,
                  })
                "
                class="text-blue-500 dark:text-blue-400 hover:underline"
              >
                <span v-html="milestone.name"></span>
              </Link>
            </p>
            <p>
              {{ trans('words.project') }}:
              <Link
                :href="
                  route('projects.show', {
                    project: project ? project.slug : milestone.project.slug,
                  })
                "
                class="text-blue-500 dark:text-blue-400 hover:underline"
              >
                {{ project ? project.name : milestone.project.name }}
              </Link>
            </p>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
              {{ trans('words.created_at') }}: {{ new Date(milestone.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            {{ trans('words.milestone_id') }}: {{ milestone.id }}
          </div>
        </li>
      </ul>

      <Pagination :pagination="pagination" />
    </div>
  </AppLayout>
</template>
