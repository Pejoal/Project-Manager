<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
  totalTasks: Number,
  completedTasks: Number,
  completedStatusColor: String,
});

const progress = computed(() => {
  if (props.totalTasks === 0) return 0;
  return (props.completedTasks / props.totalTasks) * 100;
});
</script>

<template>
  <div class="w-full">
    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden mb-2">
      <div
        class="h-full transition-all duration-300"
        :style="{
          width: progress + '%',
          backgroundColor: completedStatusColor,
        }"
      ></div>
    </div>
    <div class="text-sm flex items-center justify-between dark:text-gray-300">
      <p>{{ completedTasks }} / {{ totalTasks }} tasks completed</p>
      <p>Progress: {{ progress.toFixed(2) }}%</p>
    </div>
  </div>
</template>
