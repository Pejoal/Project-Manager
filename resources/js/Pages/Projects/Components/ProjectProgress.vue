<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
  totalTasks: Number,
  completedTasks: Number,
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
        class="h-full bg-green-500 transition-all duration-300"
        :style="{ width: progress + '%' }"
      ></div>
    </div>
    <div class="text-sm dark:text-gray-300">
      {{ completedTasks }} / {{ totalTasks }} tasks completed
    </div>
  </div>
</template>
