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
  <header class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Generate Bulk Payslips</h2>
  </header>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">
        <form @submit.prevent="submit" class="p-8 space-y-8">
          <section>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pay Period</h3>
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
          </section>

          <section>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Select Employees
              <span
                class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
              >
                {{ form.user_ids.length }} / {{ users.length }}
              </span>
            </h3>
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden max-h-96">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                  <tr>
                    <th class="px-4 py-3 text-left">
                      <Checkbox :checked="form.user_ids.length === users.length" @change="toggleAll" />
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Employee
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Hourly Rate
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr
                    v-for="user in users"
                    :key="user.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                  >
                    <td class="px-4 py-4">
                      <Checkbox :value="user.id.toString()" v-model:checked="form.user_ids" />
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                      {{ user.name }} <span class="text-gray-500 dark:text-gray-400">({{ user.email }})</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                      ${{ user.hourly_rate }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <InputError :message="form.errors.user_ids" class="mt-2" />
          </section>

          <div class="flex justify-end pt-4">
            <PrimaryButton :disabled="form.processing || form.user_ids.length === 0" class="px-8">
              Generate {{ form.user_ids.length }} Payslip{{ form.user_ids.length !== 1 ? 's' : '' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
