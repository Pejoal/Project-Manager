<script setup>
import { ref } from 'vue';

const newItem = ref('');
const items = ref([]);

function addItem() {
  if (newItem.value.trim() !== '') {
    items.value.push(newItem.value.trim());
    newItem.value = '';
  }
}

function deleteItem(index) {
  items.value.splice(index, 1);
}
</script>

<template>
  <div class="max-w-lg mx-auto font-sans pt-5">
    <h1 class="text-center text-2xl font-bold mb-4">Todo List</h1>
    <div class="flex justify-center mb-4">
      <input
        v-model="newItem"
        @keyup.enter="addItem"
        placeholder="Add a new todo..."
        class="flex-1 p-2 text-lg border border-gray-300 rounded"
      />
      <button
        @click="addItem"
        class="ml-2 p-2 text-lg bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        Add
      </button>
    </div>
    <ul class="list-none p-0">
      <li
        v-for="(item, index) in items"
        :key="index"
        class="flex justify-between p-2 border-b border-gray-300"
      >
        {{ item }}
        <button
          @click="deleteItem(index)"
          class="text-red-500 hover:text-red-700"
        >
          Delete
        </button>
      </li>
    </ul>
  </div>
</template>
