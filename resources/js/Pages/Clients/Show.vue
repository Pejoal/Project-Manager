<template>
  <div>
    <h1>{{ client.name }}</h1>
    <p>Email: {{ client.email }}</p>
    <p>Phone: {{ client.phone }}</p>
    <router-link :to="{ name: 'clients.edit', params: { client: client.id } }">Edit</router-link>
    <button @click="destroy">Delete</button>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  client: Object,
});

const form = useForm({
  client: props.client,
});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('clients.destroy', props.client.id));
  }
};
</script>
