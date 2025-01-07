<template>
  <div>
    <h1>Edit Client</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="name">Name</label>
        <input type="text" v-model="form.name" id="name" />
      </div>
      <div>
        <label for="email">Email</label>
        <input type="email" v-model="form.email" id="email" />
      </div>
      <div>
        <label for="phone">Phone</label>
        <input type="text" v-model="form.phone" id="phone" />
      </div>
      <button type="submit">Update</button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
  client: Object,
});

const form = useForm({
  name: props.client.name,
  email: props.client.email,
  phone: props.client.phone,
});

const submit = () => {
  form.put(route('clients.update', props.client.id));
};
</script>
