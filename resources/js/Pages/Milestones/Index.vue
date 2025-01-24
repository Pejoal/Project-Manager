<script setup>
import { defineProps, ref, computed } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateMilestoneModal from './CreateMilestoneModal.vue';

const props = defineProps({
  milestones: Array,
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

const title = computed(() =>
  props.project ? `Milestones for ${props.project.name}` : 'All Milestones'
);

const form = useForm({});

const fetchPage = (url) => {
  form.get(url, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="title" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ title }}
        <template v-if="project">
          <Link
            :href="route('projects.show', { project: project.slug })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ project.name }}
          </Link>
        </template>
      </h1>
    </template>
    <div
      class="p-2 my-1 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-lg shadow-md"
    >
      <CreateMilestoneModal
        :show="showModal"
        :project="project"
        :phases="phases"
        @close="closeModal"
      />
      <button
        @click="openModal"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Milestone
      </button>

      <ul class="my-2 space-y-4">
        <li
          v-for="milestone in milestones"
          :key="milestone.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>
            <p>
              Milestone:
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
              Project:
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
              Created at: {{ new Date(milestone.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Milestone ID: {{ milestone.id }}
          </div>
        </li>
      </ul>
      <!-- Pagination Controls -->
      <section class="flex items-center justify-between my-2">
        <button
          v-if="props.milestones.prev_page_url"
          @click="fetchPage(props.milestones.prev_page_url)"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg"
        >
          Previous
        </button>
        <span class="mx-2"
          >{{ props.milestones.current_page }} /
          {{ props.milestones.last_page }}</span
        >
        <span class="mx-2">Total: {{ props.milestones.total }}</span>
        <button
          v-if="props.milestones.next_page_url"
          @click="fetchPage(props.milestones.next_page_url)"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg"
        >
          Next
        </button>
      </section>
    </div>
  </AppLayout>
</template>
