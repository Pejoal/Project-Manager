<script setup>
import AttachmentSystem from '@/Components/AttachmentsSystem.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

const emit = defineEmits(['close', 'refresh']);
const props = defineProps({
  show: Boolean,
  task: {
    type: Object,
    default: null,
  },
});

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  return new Date(datetime).toLocaleString();
};

const routeParams = computed(() => {
  if (!props.task) return {};
  return {
    project: props.task.project.slug,
    task: props.task.id,
  };
});
</script>

<template>
  <DialogModal :show="show" @close="emit('close')" max-width="4xl">
    <template #title>
      <div class="flex items-center justify-between">
        <span>{{ task?.name || 'Task Details' }}</span>
        <div class="flex items-center space-x-2">
          <span
            v-if="task?.status"
            class="px-2 py-1 text-xs font-semibold rounded-full text-white"
            :style="{ backgroundColor: task.status.color }"
          >
            {{ task.status.name }}
          </span>
          <span
            v-if="task?.priority"
            class="px-2 py-1 text-xs font-semibold rounded-full text-white"
            :style="{ backgroundColor: task.priority.color }"
          >
            {{ task.priority.name }}
          </span>
        </div>
      </div>
    </template>

    <template #content>
      <div v-if="task" class="space-y-6">
        <!-- Basic Information Section -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ trans('words.basic_data') }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.task_id') }}</label>
              <p class="mt-1 text-gray-900 dark:text-gray-100">#{{ task.id }}</p>
            </div>
            <div>
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.project') }}</label>
              <Link
                :href="route('projects.show', task.project.slug)"
                class="mt-1 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                >{{ task.project.name }}</Link
              >
            </div>
            <div v-if="task.phase">
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.phase') }}</label>
              <Link
                :href="route('phases.show', { project: task.project.slug, phase: task.phase.id })"
                class="mt-1 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                >{{ task.phase.name }}</Link
              >
            </div>
            <div v-if="task.milestone">
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.milestone') }}</label>
              <Link
                :href="route('milestones.show', { project: task.project.slug, milestone: task.milestone.id })"
                class="mt-1 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                >{{ task.milestone.name }}</Link
              >
            </div>
            <div class="md:col-span-2">
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.description') }}</label>
              <p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                {{ task.description || 'No description provided' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Assignment & Timing Section -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Assignment & Timing</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.assigned_to') }}</label>
              <div class="mt-1">
                <div v-if="task.assigned_to?.length > 0" class="flex flex-wrap gap-2">
                  <span
                    v-for="user in task.assigned_to"
                    :key="user.id"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100"
                    >{{ user.name }}</span
                  >
                </div>
                <p v-else class="text-gray-500 dark:text-gray-400">Not assigned</p>
              </div>
            </div>
            <div>
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{ trans('words.created_at') }}</label>
              <p class="mt-1 text-gray-900 dark:text-gray-100">{{ formatDateTime(task.created_at) }}</p>
            </div>
            <div>
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{
                trans('words.start_datetime')
              }}</label>
              <p class="mt-1 text-gray-900 dark:text-gray-100">{{ formatDateTime(task.start_datetime) }}</p>
            </div>
            <div>
              <label class="block font-medium text-gray-700 dark:text-gray-300">{{
                trans('words.end_datetime')
              }}</label>
              <p class="mt-1 text-gray-900 dark:text-gray-100">{{ formatDateTime(task.end_datetime) }}</p>
            </div>
          </div>
        </div>

        <!-- Reusable Attachment System -->
        <AttachmentSystem
          :attachments="task.attachments"
          :route-params="routeParams"
          upload-route-name="task-attachments.store"
          update-route-name="task-attachments.update"
          delete-route-name="task-attachments.destroy"
          download-route-name="task-attachments.download"
          @refresh="emit('refresh')"
        />
      </div>
    </template>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <div class="flex space-x-2">
          <Link
            v-if="task"
            :href="route('tasks.edit', { project: task.project.slug, task: task.id })"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
          >
            {{ trans('words.edit') }}
          </Link>
          <Link
            v-if="task"
            :href="route('time-entries.index', { task_id: task.id })"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
          >
            {{ trans('words.log_time') }}
          </Link>
        </div>
        <SecondaryButton @click="emit('close')">{{ trans('words.close') }}</SecondaryButton>
      </div>
    </template>
  </DialogModal>
</template>
