<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  users: Array,
  suggestedPeriod: Object,
});

const form = useForm({
  user_ids: props.users.map((u) => u.id), // Pre-select all
  pay_period_start: props.suggestedPeriod.start_date,
  pay_period_end: props.suggestedPeriod.end_date,
  pay_date: props.suggestedPeriod.pay_date,
});

const toggleAll = () => {
  if (form.user_ids.length === props.users.length) {
    form.user_ids = [];
  } else {
    form.user_ids = props.users.map((u) => u.id);
  }
};

const submit = () => {
  form.post(route('payslips.generate'));
};
</script>

<template>
  <Head title="Generate Bulk Payslips" />
  <header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Generate Bulk Payslips</h2>
  </header>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <h3 class="text-lg font-medium">Pay Period</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <InputLabel for="pay_period_start" value="Period Start" />
              <TextInput
                id="pay_period_start"
                v-model="form.pay_period_start"
                type="date"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.pay_period_start" class="mt-2" />
            </div>
            <div>
              <InputLabel for="pay_period_end" value="Period End" />
              <TextInput
                id="pay_period_end"
                v-model="form.pay_period_end"
                type="date"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.pay_period_end" class="mt-2" />
            </div>
            <div>
              <InputLabel for="pay_date" value="Pay Date" />
              <TextInput id="pay_date" v-model="form.pay_date" type="date" class="mt-1 block w-full" required />
              <InputError :message="form.errors.pay_date" class="mt-2" />
            </div>
          </div>

          <h3 class="text-lg font-medium">Select Employees ({{ form.user_ids.length }}/{{ users.length }})</h3>
          <div class="border rounded-md max-h-96 overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                <tr>
                  <th class="p-4">
                    <Checkbox :checked="form.user_ids.length === users.length" @change="toggleAll" />
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Employee</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Hourly Rate</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="user in users" :key="user.id">
                  <td class="p-4">
                    <Checkbox :value="user.id" v-model:checked="form.user_ids" />
                  </td>
                  <td class="px-6 py-4">{{ user.name }} ({{ user.email }})</td>
                  <td class="px-6 py-4">{{ user.hourly_rate }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <InputError :message="form.errors.user_ids" class="mt-2" />

          <div class="flex justify-end">
            <PrimaryButton :disabled="form.processing || form.user_ids.length === 0">
              Generate {{ form.user_ids.length }} Payslip(s)
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
