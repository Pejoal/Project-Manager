<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  ticket: Object,
});

const form = useForm({});

const destroy = () => {
  if (confirm('Are you sure you want to delete this ticket?')) {
    form.delete(route('support-tickets.destroy', props.ticket.id));
  }
};

const updateStatus = (status) => {
  useForm({
    status: status,
    notes: '',
  }).patch(route('support-tickets.update-status', props.ticket.id), {
    preserveState: true,
  });
};

const assignTicket = (userId) => {
  useForm({
    assigned_to: userId,
  }).patch(route('support-tickets.assign', props.ticket.id), {
    preserveState: true,
  });
};

const resolveTicket = () => {
  if (confirm('Are you sure you want to resolve this ticket?')) {
    useForm({
      notes: 'Ticket resolved',
    }).patch(route('support-tickets.resolve', props.ticket.id), {
      preserveState: true,
    });
  }
};

const formatDateTime = (datetime) => {
  if (!datetime) return 'N/A';
  return new Date(datetime).toLocaleString();
};

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-red-100 text-red-800',
    urgent: 'bg-red-200 text-red-900',
  };
  return colors[priority] || 'bg-gray-100 text-gray-800';
};

const getStatusColor = (status) => {
  const colors = {
    open: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-yellow-100 text-yellow-800',
    pending_customer: 'bg-purple-100 text-purple-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-gray-100 text-gray-800',
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
  <Head :title="`Ticket #${ticket.ticket_number} - ${ticket.subject}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Ticket #{{ ticket.ticket_number }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ ticket.subject }}</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('support-tickets.edit', ticket.id)"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('words.edit') }}
          </Link>
          <button
            v-if="ticket.status !== 'resolved' && ticket.status !== 'closed'"
            @click="resolveTicket"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('crm.resolve') }}
          </button>
          <button @click="destroy" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            {{ $t('words.delete') }}
          </button>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Ticket Details -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.ticket_details') }}
            </h2>

            <div class="space-y-4">
              <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                  {{ $t('words.description') }}
                </h3>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                  {{ ticket.description }}
                </div>
              </div>

              <div v-if="ticket.category" class="grid grid-cols-2 gap-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $t('words.category') }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ ticket.category.replace(/_/g, ' ').toUpperCase() }}
                  </p>
                </div>
                <div v-if="ticket.source">
                  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $t('words.source') }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ ticket.source.replace(/_/g, ' ').toUpperCase() }}
                  </p>
                </div>
              </div>

              <div v-if="ticket.satisfaction_rating" class="grid grid-cols-2 gap-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $t('crm.satisfaction_rating') }}
                  </h3>
                  <div class="mt-1">
                    <span class="text-yellow-500">
                      {{ '★'.repeat(ticket.satisfaction_rating) }}{{ '☆'.repeat(5 - ticket.satisfaction_rating) }}
                    </span>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                      ({{ ticket.satisfaction_rating }}/5)
                    </span>
                  </div>
                </div>
                <div v-if="ticket.satisfaction_comment">
                  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $t('crm.satisfaction_comment') }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ ticket.satisfaction_comment }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Interactions -->
          <div
            v-if="ticket.interactions && ticket.interactions.length > 0"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
          >
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.interactions') }}
            </h2>
            <div class="space-y-4">
              <div
                v-for="interaction in ticket.interactions"
                :key="interaction.id"
                class="border-l-4 border-blue-400 pl-4 py-2"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                      {{ interaction.subject }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                      {{ interaction.type.replace(/_/g, ' ').toUpperCase() }} -
                      {{ interaction.direction.toUpperCase() }}
                    </p>
                  </div>
                  <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDateTime(interaction.interaction_date) }}
                  </span>
                </div>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                  {{ interaction.description }}
                </p>
                <div v-if="interaction.user" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  by {{ interaction.user.name }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Status & Priority -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.ticket_status') }}
            </h3>
            <div class="space-y-3">
              <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                <span
                  :class="`ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(ticket.status)}`"
                >
                  {{ ticket.status.replace(/_/g, ' ').toUpperCase() }}
                </span>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Priority:</span>
                <span
                  :class="`ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getPriorityColor(ticket.priority)}`"
                >
                  {{ ticket.priority.toUpperCase() }}
                </span>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div v-if="ticket.contact" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.contact_information') }}
            </h3>
            <div class="space-y-2">
              <div>
                <Link
                  :href="route('contacts.show', ticket.contact.id)"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                >
                  {{ ticket.contact.first_name }} {{ ticket.contact.last_name }}
                </Link>
              </div>
              <div v-if="ticket.contact.email" class="text-sm text-gray-600 dark:text-gray-400">
                <a :href="`mailto:${ticket.contact.email}`" class="hover:text-blue-600">
                  {{ ticket.contact.email }}
                </a>
              </div>
              <div v-if="ticket.contact.phone" class="text-sm text-gray-600 dark:text-gray-400">
                <a :href="`tel:${ticket.contact.phone}`" class="hover:text-blue-600">
                  {{ ticket.contact.phone }}
                </a>
              </div>
              <div v-if="ticket.contact.company" class="text-sm text-gray-600 dark:text-gray-400">
                {{ ticket.contact.company }}
              </div>
            </div>
          </div>

          <!-- Assignment -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.assignment') }}
            </h3>
            <div class="space-y-3">
              <div v-if="ticket.assigned_to">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned to:</span>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ ticket.assigned_to.name }}
                </div>
              </div>
              <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                {{ $t('crm.unassigned') }}
              </div>

              <div v-if="ticket.created_by">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created by:</span>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ ticket.created_by.name }}
                </div>
              </div>
            </div>
          </div>

          <!-- Timestamps -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('crm.timestamps') }}
            </h3>
            <div class="space-y-2 text-sm">
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">Created:</span>
                <div class="text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(ticket.created_at) }}
                </div>
              </div>
              <div>
                <span class="font-medium text-gray-500 dark:text-gray-400">Updated:</span>
                <div class="text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(ticket.updated_at) }}
                </div>
              </div>
              <div v-if="ticket.resolved_at">
                <span class="font-medium text-gray-500 dark:text-gray-400">Resolved:</span>
                <div class="text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(ticket.resolved_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
