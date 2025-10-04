<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import CreateTimeEntryModal from './CreateTimeEntryModal.vue';
import { ref } from 'vue';

const showModal = ref(false);

const props = defineProps({
  timeEntry: Object,
});

const formatCurrency = (amount) =>
  new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount || 0);
const formatHours = (hours) => parseFloat(hours || 0).toFixed(2);
const formatDateTime = (datetime) => new Date(datetime).toLocaleString('de-DE');

const statusInfo = computed(() => {
  if (props.timeEntry.is_approved) {
    return {
      text: 'Approved',
      class: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    };
  }
  return {
    text: 'Pending',
    class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
  };
});
</script>

<template>
  <Head :title="`Time Entry #${timeEntry.id}`" />
  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">Time Entry Details</h2>
        <div class="flex items-center gap-3">
          <button
            @click="showModal = true"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
          >
            Edit
          </button>
          <Link
            :href="route('time-entries.index')"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
          >
            Back to List
          </Link>
        </div>
      </div>
    </div>
  </header>
  <CreateTimeEntryModal :show="showModal" :timeEntry="timeEntry" @close="showModal = false" />

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ timeEntry.user.name }}
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Task:
                <Link
                  :href="route('tasks.show', { project: timeEntry.project.slug, task: timeEntry.task.id })"
                  class="text-blue-500 hover:underline"
                  >{{ timeEntry.task.name }}</Link
                >
              </p>
            </div>
            <span :class="statusInfo.class" class="px-3 py-1 rounded-full text-sm font-semibold">
              {{ statusInfo.text }}
            </span>
          </div>
        </div>
        <div class="p-6">
          <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-8">
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Time</dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                {{ formatDateTime(timeEntry.start_datetime) }}
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Time</dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                {{ formatDateTime(timeEntry.end_datetime) }}
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hours Worked</dt>
              <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100">
                {{ formatHours(timeEntry.hours_worked) }}h
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gross Amount</dt>
              <dd class="mt-1 text-sm font-bold text-green-600 dark:text-green-400">
                {{ formatCurrency(timeEntry.gross_amount) }}
              </dd>
            </div>
            <div class="sm:col-span-2" v-if="timeEntry.description">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                {{ timeEntry.description }}
              </dd>
            </div>
            <div class="sm:col-span-2" v-if="timeEntry.is_approved">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approval Details</dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                Approved by {{ timeEntry.approved_by.name }} on {{ formatDateTime(timeEntry.approved_at) }}
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>
