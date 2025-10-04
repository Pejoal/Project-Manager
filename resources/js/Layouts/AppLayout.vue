<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// --- Assets: SVG Icons ---
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
const IconReports = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-4m3 4v-2m3 2v-6m-9 0h12a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2"></path></svg>`,
};
const IconSettings = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>`,
};
const IconChevronLeft = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>`,
};
const IconChevronRight = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>`,
};

// --- Sidebar Link Component ---
const SidebarLink = {
  props: ['href', 'active', 'isCollapsed'],
  components: { Link },
  template: `
    <Link :href="href" class="flex items-center p-2 text-base font-normal rounded-lg transition-colors duration-200"
          :class="[
            active ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
            isCollapsed ? 'justify-center' : ''
          ]">
      <span class="w-6 h-6"><slot name="icon"></slot></span>
      <span v-show="!isCollapsed" class="ml-3 flex-1 whitespace-nowrap">{{ $slots.default()[0].children }}</span>
    </Link>
  `,
};

// --- Dropdown for Nested Links ---
const SidebarDropdown = {
  props: ['title', 'active', 'isCollapsed'],
  setup(props, { slots }) {
    const isOpen = ref(props.active);
    const toggle = () => {
      if (!props.isCollapsed) {
        isOpen.value = !isOpen.value;
      }
    };
    return { isOpen, toggle, slots };
  },
  template: `
        <div>
            <button @click="toggle" type="button" class="flex items-center w-full p-2 text-base font-normal text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                    :class="[active ? 'bg-gray-100 dark:bg-gray-700' : '', isCollapsed ? 'justify-center' : '']">
                <span class="w-6 h-6 flex-shrink-0"><slot name="icon"></slot></span>
                <span v-show="!isCollapsed" class="flex-1 ml-3 text-left whitespace-nowrap">{{ title }}</span>
                <span v-show="!isCollapsed" class="w-6 h-6 transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </span>
            </button>
            <ul v-show="isOpen && !isCollapsed" class="py-2 space-y-2 pl-4 border-l border-gray-200 dark:border-gray-700 ml-3">
                <slot></slot>
            </ul>
        </div>
    `,
};

const page = usePage();
const isCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true');

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  localStorage.setItem('sidebarCollapsed', isCollapsed.value);
};

const hasPermission = (permission) => {
  return page.props?.user?.can.includes(permission);
};

const hasAnyPermission = (permissions) => {
  return permissions.some((p) => hasPermission(p));
};

const isPayrollActive = computed(
  () =>
    route().current('payroll.*') ||
    route().current('employee-profiles.*') ||
    route().current('payslips.*') ||
    route().current('time-entries.*')
);

const payrollPermissions = [
  'view payroll dashboard',
  'view employee profiles',
  'view payslips',
  'view time entries',
  'manage payroll settings',
];
</script>

<template>
  <aside
    class="flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300"
    :class="isCollapsed ? 'w-20' : 'w-64'"
  >
    <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
      <Link :href="route('dashboard')">
        <ApplicationMark class="block h-8 w-auto" />
      </Link>
    </div>

    <nav class="flex-1 px-3 py-4 overflow-y-auto">
      <ul class="space-y-2">
        <li>
          <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" :is-collapsed="isCollapsed">
            <template #icon><IconDashboard /></template>
            Dashboard
          </SidebarLink>
        </li>
        <li>
          <SidebarLink
            :href="route('clients.index')"
            :active="route().current('clients.*')"
            :is-collapsed="isCollapsed"
          >
            <template #icon><IconClients /></template>
            Clients
          </SidebarLink>
        </li>
        <li>
          <SidebarLink
            :href="route('projects.index')"
            :active="route().current('projects.*')"
            :is-collapsed="isCollapsed"
          >
            <template #icon><IconProjects /></template>
            Projects
          </SidebarLink>
        </li>
        <li>
          <SidebarLink :href="route('tasks.all')" :active="route().current('tasks.all')" :is-collapsed="isCollapsed">
            <template #icon><IconTasks /></template>
            All Tasks
          </SidebarLink>
        </li>

        <!-- Payroll Dropdown -->
        <li v-if="hasAnyPermission(payrollPermissions)">
          <SidebarDropdown title="Payroll" :active="isPayrollActive" :is-collapsed="isCollapsed">
            <template #icon><IconPayroll /></template>
            <li v-if="hasPermission('view payroll dashboard')">
              <SidebarLink :href="route('payroll.dashboard')" :active="route().current('payroll.dashboard')"
                >Dashboard</SidebarLink
              >
            </li>
            <li v-if="hasPermission('view time entries')">
              <SidebarLink :href="route('time-entries.index')" :active="route().current('time-entries.*')"
                >Time Entries</SidebarLink
              >
            </li>
            <li v-if="hasPermission('view employee profiles')">
              <SidebarLink :href="route('employee-profiles.index')" :active="route().current('employee-profiles.*')"
                >Employees</SidebarLink
              >
            </li>
            <li v-if="hasPermission('view payslips')">
              <SidebarLink :href="route('payslips.index')" :active="route().current('payslips.*')"
                >Payslips</SidebarLink
              >
            </li>
            <li v-if="hasPermission('manage payroll settings')">
              <SidebarLink :href="route('payroll.settings')" :active="route().current('payroll.settings')"
                >Settings</SidebarLink
              >
            </li>
          </SidebarDropdown>
        </li>
      </ul>
    </nav>

    <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
      <button
        @click="toggleSidebar"
        class="w-full flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
      >
        <IconChevronLeft class="w-6 h-6 transition-transform duration-300" :class="{ 'rotate-180': isCollapsed }" />
      </button>
    </div>
  </aside>
</template>
