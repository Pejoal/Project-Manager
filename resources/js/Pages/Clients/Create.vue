<template>
  <div>
    <h1>Create Client</h1>
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
      <button type="submit">Create</button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  phone: '',
});

const submit = () => {
  form.post(route('clients.store'));
};
</script>
