<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import SidebarDropdown from '@/Components/SidebarDropdown.vue';
import SidebarLink from '@/Components/SidebarLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// SVG Icons
const IconDashboard = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>`,
};
const IconClients = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.224-1.25-.62-1.722M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.224-1.25.62-1.722M12 12a3 3 0 100-6 3 3 0 000 6z"></path></svg>`,
};
const IconProjects = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>`,
};
const IconTasks = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>`,
};
const IconPayroll = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>`,
};
const IconCRM = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>`,
};
const IconChevronLeft = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>`,
};

const props = defineProps({
  isSidebarCollapsed: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggle-collapse', 'toggle-sidebar-collapse']);

const page = usePage();

// Role-based Access
const hasRole = (role) => page.props.user.roles.includes(role);
const hasAnyRole = (roles) => roles.some((r) => hasRole(r));

// Active State Logic
const isPayrollActive = computed(
  () =>
    route().current('payroll.*') ||
    route().current('employee-profiles.*') ||
    route().current('payslips.*') ||
    route().current('time-entries.*')
);

const isCRMActive = computed(
  () =>
    route().current('leads.*') ||
    route().current('contacts.*') ||
    route().current('opportunities.*') ||
    route().current('campaigns.*') ||
    route().current('support-tickets.*') ||
    route().current('interactions.*') ||
    route().current('knowledge-base.*')
);

const handleToggleSidebarCollapse = () => {
  if (props.isSidebarCollapsed) {
    emit('toggle-collapse');
  }
};
</script>

<template>
  <aside
    class="hidden md:flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300"
    :class="isSidebarCollapsed ? 'w-20' : 'w-64'"
  >
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
      <Link :href="route('dashboard')">
        <ApplicationMark class="block h-8 w-auto" />
      </Link>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
      <ul class="space-y-2">
        <!-- Dashboard -->
        <li>
          <SidebarLink
            :href="route('dashboard')"
            :active="route().current('dashboard')"
            :is-collapsed="isSidebarCollapsed"
          >
            <template #icon><IconDashboard /></template>
            {{ $t('words.dashboard') }}
          </SidebarLink>
        </li>

        <!-- Clients -->
        <li>
          <SidebarLink
            :href="route('clients.index')"
            :active="route().current('clients.*')"
            :is-collapsed="isSidebarCollapsed"
          >
            <template #icon><IconClients /></template>
            {{ $t('words.clients') }}
          </SidebarLink>
        </li>

        <!-- Projects -->
        <li>
          <SidebarLink
            :href="route('projects.index')"
            :active="route().current('projects.*')"
            :is-collapsed="isSidebarCollapsed"
          >
            <template #icon><IconProjects /></template>
            {{ $t('words.projects') }}
          </SidebarLink>
        </li>

        <!-- All Tasks -->
        <li>
          <SidebarLink
            :href="route('tasks.all')"
            :active="route().current('tasks.all')"
            :is-collapsed="isSidebarCollapsed"
          >
            <template #icon><IconTasks /></template>
            {{ $t('words.all_tasks') }}
          </SidebarLink>
        </li>

        <!-- Payroll Dropdown -->
        <li v-if="hasAnyRole(['admin', 'manager', 'employee'])">
          <SidebarDropdown
            @toggled="handleToggleSidebarCollapse"
            :title="$t('words.payroll')"
            :active="isPayrollActive"
            :is-collapsed="isSidebarCollapsed"
          >
            <template #icon><IconPayroll /></template>
            <li v-if="hasRole('admin')">
              <SidebarLink :href="route('payroll.dashboard')" :active="route().current('payroll.dashboard')">
                {{ $t('words.dashboard') }}
              </SidebarLink>
            </li>
            <li v-if="hasRole('admin')">
              <SidebarLink :href="route('payroll.index')" :active="route().current('payroll.index')">
                {{ $t('words.payrolls') }}
              </SidebarLink>
            </li>
            <li v-if="hasAnyRole(['admin', 'manager', 'employee'])">
              <SidebarLink :href="route('time-entries.index')" :active="route().current('time-entries.*')">
                {{ $t('words.time_entries') }}
              </SidebarLink>
            </li>
            <li v-if="hasRole('admin')">
              <SidebarLink :href="route('employee-profiles.index')" :active="route().current('employee-profiles.*')">
                {{ $t('words.employees') }}
              </SidebarLink>
            </li>
            <li v-if="hasRole('admin')">
              <SidebarLink :href="route('payslips.index')" :active="route().current('payslips.*')">
                {{ $t('words.payslips') }}
              </SidebarLink>
            </li>
            <li v-if="hasRole('admin')">
              <SidebarLink :href="route('payroll.settings')" :active="route().current('payroll.settings')">
                {{ $t('words.settings') }}
              </SidebarLink>
            </li>
          </SidebarDropdown>
        </li>

        <!-- CRM Dropdown -->
        <li v-if="hasAnyRole(['admin', 'manager', 'sales'])">
          <SidebarDropdown
            @toggled="handleToggleSidebarCollapse"
            :title="$t('words.crm')"
            :active="isCRMActive"
            :is-collapsed="isSidebarCollapsed"
          >
            <template #icon><IconCRM /></template>
            <li>
              <SidebarLink :href="route('leads.index')" :active="route().current('leads.*')">
                {{ $t('words.leads') }}
              </SidebarLink>
            </li>
            <li>
              <SidebarLink :href="route('contacts.index')" :active="route().current('contacts.*')">
                {{ $t('words.contacts') }}
              </SidebarLink>
            </li>
            <li>
              <SidebarLink :href="route('opportunities.index')" :active="route().current('opportunities.*')">
                {{ $t('words.opportunities') }}
              </SidebarLink>
            </li>
            <li>
              <SidebarLink :href="route('campaigns.index')" :active="route().current('campaigns.*')">
                {{ $t('words.campaigns') }}
              </SidebarLink>
            </li>
            <li>
              <SidebarLink :href="route('support-tickets.index')" :active="route().current('support-tickets.*')">
                {{ $t('words.support_tickets') }}
              </SidebarLink>
            </li>
            <li>
              <SidebarLink :href="route('interactions.index')" :active="route().current('interactions.*')">
                {{ $t('words.interactions') }}
              </SidebarLink>
            </li>
            <li>
              <SidebarLink :href="route('knowledge-base.index')" :active="route().current('knowledge-base.*')">
                {{ $t('words.knowledge_base') }}
              </SidebarLink>
            </li>
          </SidebarDropdown>
        </li>
      </ul>
    </nav>

    <!-- Collapse Toggle Button -->
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
      <button
        @click="emit('toggle-collapse')"
        class="w-full flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
      >
        <IconChevronLeft
          class="w-6 h-6 transition-transform duration-300"
          :class="{ 'rotate-180': isSidebarCollapsed }"
        />
      </button>
    </div>
  </aside>
</template>
