<script setup>
import { defineProps, ref, computed, watch } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreatePhaseModal from './CreatePhaseModal.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
  phases: Array,
  project: Object,
});

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const title = computed(() =>
  props.project ? `Phases for ${props.project.name}` : 'All Phases'
);

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
      <CreatePhaseModal
        :show="showModal"
        :project="project"
        @close="closeModal"
      />
      <button
        @click="openModal"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Phase
      </button>

      <ul class="my-2 space-y-4">
        <li
          v-for="phase in phases"
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
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Phase ID: {{ phase.id }}
          </div>
        </li>
      </ul>
      <!-- Pagination Controls -->
      <section class="flex items-center justify-between my-2">
        <button
          v-if="props.phases.prev_page_url"
          @click="fetchPage(props.phases.prev_page_url)"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg"
        >
          Previous
        </button>
        <span class="mx-2"
          >{{ props.phases.current_page }} / {{ props.phases.last_page }}</span
        >
        <span class="mx-2">Total: {{ props.phases.total }}</span>
        <button
          v-if="props.phases.next_page_url"
          @click="fetchPage(props.phases.next_page_url)"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg"
        >
          Next
        </button>
      </section>
    </div>
  </AppLayout>
</template>
