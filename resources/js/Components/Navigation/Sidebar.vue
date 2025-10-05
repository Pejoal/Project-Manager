<!-- Updated Sidebar.vue (Now Using All Smaller Components) -->
<script setup>
import { usePage } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';
import NavItem from './NavItem.vue';
import NavSection from './NavSection.vue';
import SidebarCollapseToggle from './SidebarCollapseToggle.vue';
import SidebarLogo from './SidebarLogo.vue';
import SidebarMobileClose from './SidebarMobileClose.vue';

const page = usePage();

const props = defineProps({
  isSidebarCollapsed: {
    type: Boolean,
    default: false,
  },
  showingMobileMenu: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggle-sidebar', 'close-mobile-menu']);

// Check if user has specific role
const hasRole = (role) => {
  return page.props.auth?.user?.roles?.some((userRole) => userRole.name === role) || false;
};

// Navigation items configuration
const navigationItems = computed(() => [
  {
    name: trans('words.dashboard'),
    href: route('dashboard'),
    icon: 'dashboard',
    current: route().current('dashboard'),
  },
  {
    name: trans('words.clients'),
    href: route('clients.index'),
    icon: 'clients',
    current: route().current('clients.*'),
  },
  {
    name: trans('words.projects'),
    href: route('projects.index'),
    icon: 'projects',
    current: route().current('projects.*'),
  },
  {
    name: trans('words.all_tasks'),
    href: route('tasks.all'),
    icon: 'tasks',
    current: route().current('tasks.all'),
  },
  {
    name: trans('words.time_entries'),
    href: route('time-entries.index'),
    icon: 'time',
    current: route().current('time-entries.*'),
  },
  // Payroll section (admin only)
  ...(hasRole('admin')
    ? [
        {
          name: trans('words.payroll'),
          icon: 'payroll',
          current:
            route().current('payroll.*') || route().current('employee-profiles.*') || route().current('payslips.*'),
          children: [
            {
              name: trans('words.dashboard'),
              href: route('payroll.dashboard'),
              current: route().current('payroll.dashboard'),
            },
            {
              name: trans('words.payrolls'),
              href: route('payroll.index'),
              current: route().current('payroll.index'),
            },
            {
              name: trans('words.employee_profiles'),
              href: route('employee-profiles.index'),
              current: route().current('employee-profiles.*'),
            },
            {
              name: trans('words.payslips'),
              href: route('payslips.index'),
              current: route().current('payslips.*'),
            },
            {
              name: trans('words.reports'),
              href: route('payroll.reports'),
              current: route().current('payroll.reports'),
            },
            {
              name: trans('words.settings'),
              href: route('payroll.settings'),
              current: route().current('payroll.settings'),
            },
          ],
        },
      ]
    : []),
  // CRM section
  {
    name: 'CRM',
    icon: 'crm',
    current:
      route().current('leads.*') ||
      route().current('contacts.*') ||
      route().current('opportunities.*') ||
      route().current('campaigns.*') ||
      route().current('support-tickets.*') ||
      route().current('interactions.*') ||
      route().current('knowledge-base.*'),
    children: [
      {
        name: trans('words.leads'),
        href: route('leads.index'),
        current: route().current('leads.*'),
      },
      {
        name: trans('words.contacts'),
        href: route('contacts.index'),
        current: route().current('contacts.*'),
      },
      {
        name: trans('words.opportunities'),
        href: route('opportunities.index'),
        current: route().current('opportunities.*'),
      },
      {
        name: trans('words.campaigns'),
        href: route('campaigns.index'),
        current: route().current('campaigns.*'),
      },
      {
        name: trans('words.support_tickets'),
        href: route('support-tickets.index'),
        current: route().current('support-tickets.*'),
      },
      {
        name: trans('words.interactions'),
        href: route('interactions.index'),
        current: route().current('interactions.*'),
      },
      {
        name: trans('words.knowledge_base'),
        href: route('knowledge-base.index'),
        current: route().current('knowledge-base.*'),
      },
    ],
  },
  // Settings section (admin only)
  ...(hasRole('admin')
    ? [
        {
          name: trans('words.settings'),
          icon: 'settings',
          current:
            route().current('project-statuses.*') ||
            route().current('project-priorities.*') ||
            route().current('task-statuses.*') ||
            route().current('task-priorities.*'),
          children: [
            {
              name: trans('words.project_statuses'),
              href: route('project-statuses.index'),
              current: route().current('project-statuses.*'),
            },
            {
              name: trans('words.project_priorities'),
              href: route('project-priorities.index'),
              current: route().current('project-priorities.*'),
            },
            {
              name: trans('words.task_statuses'),
              href: route('task-statuses.index'),
              current: route().current('task-statuses.*'),
            },
            {
              name: trans('words.task_priorities'),
              href: route('task-priorities.index'),
              current: route().current('task-priorities.*'),
            },
          ],
        },
      ]
    : []),
  {
    name: trans('words.activities'),
    href: route('activities.index'),
    icon: 'activities',
    current: route().current('activities.*'),
  },
  {
    name: trans('words.maps'),
    href: route('maps.index'),
    icon: 'map',
    current: route().current('maps.*'),
  },
]);

// Track expanded state for sections (keyed by item name)
const expandedSections = ref(new Set());

// Toggle section expansion
const toggleSection = (itemName) => {
  if (expandedSections.value.has(itemName)) {
    expandedSections.value.delete(itemName);
  } else {
    expandedSections.value.add(itemName);
  }
};

// Icon components (shared)
const icons = {
  dashboard: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 01-2 2H10a2 2 0 01-2-2v0z"></path></svg>`,
  },
  clients: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>`,
  },
  projects: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>`,
  },
  tasks: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>`,
  },
  time: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
  },
  payroll: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
  },
  crm: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>`,
  },
  settings: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>`,
  },
  activities: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`,
  },
  map: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>`,
  },
  chevronDown: {
    template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>`,
  },
  chevronLeft: {
    template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>`,
  },
};
</script>

<template>
  <!-- Desktop Sidebar -->
  <aside
    class="hidden md:flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300"
    :class="isSidebarCollapsed ? 'w-20' : 'w-64'"
  >
    <SidebarLogo :is-collapsed="isSidebarCollapsed" />

    <!-- Navigation -->
    <div class="flex-1 flex flex-col overflow-y-auto">
      <nav class="flex-1 px-2 py-4 space-y-1 hide-scrollbar">
        <NavItem
          v-for="item in navigationItems.filter((i) => !i.children)"
          :key="item.name"
          :item="item"
          :is-collapsed="isSidebarCollapsed"
          :icons="icons"
        />

        <NavSection
          v-for="item in navigationItems.filter((i) => i.children)"
          :key="item.name"
          :item="item"
          :is-collapsed="isSidebarCollapsed"
          :icons="icons"
          :expanded-sections="expandedSections"
          @toggle-section="toggleSection"
        />
      </nav>
    </div>

    <SidebarCollapseToggle :is-collapsed="isSidebarCollapsed" :icons="icons" @toggle-sidebar="emit('toggle-sidebar')" />
  </aside>

  <!-- Mobile Sidebar -->
  <div v-show="showingMobileMenu" class="md:hidden">
    <div class="fixed inset-0 flex z-40">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75" aria-hidden="true" @click="emit('close-mobile-menu')"></div>

      <!-- Sidebar -->
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800">
        <SidebarMobileClose @close-mobile-menu="emit('close-mobile-menu')" />

        <SidebarLogo :is-collapsed="false" @close-mobile-menu="emit('close-mobile-menu')" />

        <!-- Navigation -->
        <div class="flex-1 flex flex-col overflow-y-auto">
          <nav class="flex-1 px-2 py-4 space-y-1">
            <NavItem
              v-for="item in navigationItems.filter((i) => !i.children)"
              :key="item.name"
              :item="item"
              :is-collapsed="false"
              :icons="icons"
              @close-mobile-menu="emit('close-mobile-menu')"
            />

            <NavSection
              v-for="item in navigationItems.filter((i) => i.children)"
              :key="item.name"
              :item="item"
              :is-collapsed="false"
              :icons="icons"
              :expanded-sections="expandedSections"
              @toggle-section="toggleSection"
              @close-mobile-menu="emit('close-mobile-menu')"
            />
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>
