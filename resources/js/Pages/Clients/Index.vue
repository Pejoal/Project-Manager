<template>
  <div>
    <h1>Clients</h1>
    <ul>
      <li v-for="client in clients" :key="client.id">
        <router-link :to="{ name: 'clients.show', params: { client: client.id } }">
          {{ client.name }}
        </router-link>
      </li>
    </ul>
    <router-link :to="{ name: 'clients.create' }">Add New Client</router-link>
  </div>
</template>

<script setup>
defineProps({
  clients: Array,
});
</script>
