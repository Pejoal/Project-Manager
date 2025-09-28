<script setup>
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, defineProps, ref } from 'vue';
import CreatePhaseModal from './CreatePhaseModal.vue';

const props = defineProps({
  phases: Object,
  project: Object,
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
    prev_page_url: props.phases.prev_page_url,
    prev_page_url: props.phases.prev_page_url,
    current_page: props.phases.current_page,
    last_page: props.phases.last_page,
    total: props.phases.total,
    next_page_url: props.phases.next_page_url,
    next_page_url: props.phases.next_page_url,
  };
});
</script>

<template>
  <Head :title="`Phases for ${project.name}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        <section>
          Phases for
          <Link
            :href="route('projects.show', { project: project.slug })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ project.name }}
          </Link>
        </section>
      </h1>
    </div>
  </header>

  <div class="p-2 my-1 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <CreatePhaseModal :show="showModal" :project="project" @close="closeModal" />
    <button @click="openModal" class="text-blue-500 dark:text-blue-400 hover:underline">Create Phase</button>

    <ul class="my-2 space-y-4">
      <li
        v-for="phase in phases.data"
        :key="phase.id"
        class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
      >
        <div>
          <p>
            Phase:
            <Link
              :href="
                route('phases.show', {
                  project: project ? project.slug : phase.project.slug,
                  phase: phase.id,
                })
              "
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              <span v-html="phase.name"></span>
            </Link>
          </p>
          <p>
            Project:
            <Link
              :href="
                route('projects.show', {
                  project: project ? project.slug : phase.project.slug,
                })
              "
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ project ? project.name : phase.project.name }}
            </Link>
          </p>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Created at: {{ new Date(phase.created_at).toLocaleString() }}
          </div>
        </div>
        <div class="text-gray-500 dark:text-gray-400 text-sm">Phase ID: {{ phase.id }}</div>
      </li>
    </ul>

    <Pagination :pagination="pagination" />
  </div>
</template>
