<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  profile: Object,
});

const form = useForm({
  _method: 'PUT',
  employee_id: props.profile.employee_id,
  base_hourly_rate: props.profile.base_hourly_rate,
  overtime_rate_multiplier: props.profile.overtime_rate_multiplier,
  standard_hours_per_day: props.profile.standard_hours_per_day,
  standard_hours_per_week: props.profile.standard_hours_per_week,
  payment_method: props.profile.payment_method,
  bank_account_number: props.profile.bank_account_number,
  bank_name: props.profile.bank_name,
  tax_id: props.profile.tax_id,
  is_active: props.profile.is_active,
  hire_date: props.profile.hire_date,
  termination_date: props.profile.termination_date,
});

const submit = () => {
  form.post(route('employee-profiles.update', props.profile.id));
};
</script>

<template>
  <Head :title="`Edit Profile: ${profile.user.name}`" />

  <header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Edit Profile: {{ profile.user.name }}
    </h2>
  </header>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <InputLabel for="base_hourly_rate" value="Base Hourly Rate (€)" />
              <TextInput
                id="base_hourly_rate"
                v-model="form.base_hourly_rate"
                type="number"
                step="0.01"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.base_hourly_rate" class="mt-2" />
            </div>
            <div>
              <InputLabel for="overtime_rate_multiplier" value="Overtime Multiplier" />
              <TextInput
                id="overtime_rate_multiplier"
                v-model="form.overtime_rate_multiplier"
                type="number"
                step="0.1"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.overtime_rate_multiplier" class="mt-2" />
            </div>
            <div class="md:col-span-2">
              <InputLabel for="payment_method" value="Payment Method" />
              <select
                v-model="form.payment_method"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
              >
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cash">Cash</option>
                <option value="check">Check</option>
              </select>
              <InputError :message="form.errors.payment_method" class="mt-2" />
            </div>
            <div>
              <InputLabel for="hire_date" value="Hire Date" />
              <TextInput id="hire_date" v-model="form.hire_date" type="date" class="mt-1 block w-full" required />
              <InputError :message="form.errors.hire_date" class="mt-2" />
            </div>
            <div>
              <InputLabel for="termination_date" value="Termination Date" />
              <TextInput id="termination_date" v-model="form.termination_date" type="date" class="mt-1 block w-full" />
              <InputError :message="form.errors.termination_date" class="mt-2" />
            </div>
            <div class="flex items-center space-x-2">
              <Checkbox id="is_active" v-model:checked="form.is_active" />
              <InputLabel for="is_active" value="Is Active" />
              <InputError :message="form.errors.is_active" class="mt-2" />
            </div>
          </div>
          <div class="flex items-center justify-end mt-6">
            <SecondaryButton :href="route('employee-profiles.index')">Cancel</SecondaryButton>
            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              Update Profile
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
