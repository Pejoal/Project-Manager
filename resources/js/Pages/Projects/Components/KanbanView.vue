<script setup>
import { defineProps, ref, watch } from 'vue';
import Draggable from 'vuedraggable';
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import KanbanTask from './KanbanTask.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  project: Object,
  filtersForm: Object,
});

const phases = ref(JSON.parse(JSON.stringify(props.project.phases)));

watch(
  () => phases.value,
  (newPhases) => {
    axios.put(route('phases.sync', props.project.slug), {
      phases: newPhases,
    });
  },
  { deep: true }
);

const drag = ref(false);

const phasesDragOptions = {
  animation: 200,
  group: 'phases',
  disabled: false,
  ghostClass: 'ghost',
};

const tasksDragOptions = {
  animation: 200,
  group: 'tasks',
  disabled: false,
  ghostClass: 'ghost',
};
</script>

<template>
  <section class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
    <h2 class="text-xl font-bold dark:text-gray-100">Kanban View</h2>
    <h3 class="text-lg font-bold dark:text-gray-100">Phases - Tasks</h3>
    <form class="my-2 flex flex-wrap gap-2">
      <section class="flex items-center">
        <InputLabel for="show_description" value="Show Description" />
        <Checkbox
          class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
          id="show_description"
          v-model:checked="filtersForm.show_description"
          name="show_description"
        />
      </section>
      <section class="flex items-center">
        <InputLabel for="show_status" value="Show Status" />
        <Checkbox
          class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
          id="show_status"
          v-model:checked="filtersForm.show_status"
          name="show_status"
        />
      </section>
      <section class="flex items-center">
        <InputLabel for="show_priority" value="Show Priority" />
        <Checkbox
          class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
          id="show_priority"
          v-model:checked="filtersForm.show_priority"
          name="show_priority"
        />
      </section>
      <section class="flex items-center">
        <InputLabel for="show_assigned_to" value="Show Assigned to" />
        <Checkbox
          class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
          id="show_assigned_to"
          v-model:checked="filtersForm.show_assigned_to"
          name="show_assigned_to"
        />
      </section>
    </form>
    <Draggable
      v-model="phases"
      @start="drag = true"
      @end="drag = false"
      group="phases"
      item-key="id"
      class="flex flex-wrap gap-4"
      v-bind="phasesDragOptions"
    >
      <template #item="{ element }">
        <div
          class="cursor-move bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md w-full sm:w-80"
        >
          <Link
            :href="
              route('phases.show', { project: project.slug, phase: element.id })
            "
            class="text-lg text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ element.name }}
          </Link>
          <Draggable
            v-model="element.tasks"
            group="tasks"
            item-key="id"
            v-bind="tasksDragOptions"
          >
            <template #item="{ element: task }">
              <KanbanTask
                :task="task"
                :project="project"
                :filtersForm="filtersForm"
              />
            </template>
          </Draggable>
        </div>
      </template>
    </Draggable>
  </section>
</template>

<style>
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
</style>
