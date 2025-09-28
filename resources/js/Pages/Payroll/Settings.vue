<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
// ... other component imports

const props = defineProps({
  settings: Object,
  taxConfigurations: Array,
});

const form = useForm({
  _method: 'PUT',
  ...props.settings,
});

const submit = () => {
  form.post(route('payroll.settings.update'));
};
</script>

<template>
  <Head title="Payroll Settings" />
  <header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Payroll Settings</h2>
  </header>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Company Name</label
              >
              <input
                v-model="form.company_name"
                type="text"
                id="company_name"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
              />
            </div>
            <div>
              <label for="pay_period" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                >Pay Period</label
              >
              <select
                v-model="form.pay_period"
                id="pay_period"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
              >
                <option value="weekly">Weekly</option>
                <option value="bi_weekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end">
            <PrimaryButton :disabled="form.processing">Save Settings</PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
