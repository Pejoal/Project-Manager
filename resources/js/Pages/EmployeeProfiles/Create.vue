<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

// Define props passed from the controller
const props = defineProps({
  users: Array, // Array of users without a profile
});

// Initialize the form object with fields matching the validation rules
const form = useForm({
  user_id: null,
  employee_id: '',
  base_hourly_rate: '',
  overtime_rate_multiplier: 1.5,
  standard_hours_per_day: 8,
  standard_hours_per_week: 40,
  payment_method: 'bank_transfer',
  bank_account_number: '',
  bank_name: '',
  tax_id: '',
  hire_date: new Date().toISOString().slice(0, 10), // Default to today
  termination_date: null,
});

// Format users for vue-select: { label: 'Name (email)', value: id }
const userOptions = props.users.map((user) => ({
  label: `${user.name} (${user.email})`,
  value: user.id,
}));

// Handle form submission
const submit = () => {
  // Map the selected user object back to just the ID for the request
  const formData = { ...form.data() };
  if (form.user_id && typeof form.user_id === 'object') {
    formData.user_id = form.user_id.value;
  }

  form
    .transform(() => formData)
    .post(route('employee-profiles.store'), {
      onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <header>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create New Employee Profile</h2>
    </div>
  </header>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <InputLabel for="user_id" value="Select User" />
              <v-select
                id="user_id"
                v-model="form.user_id"
                :options="userOptions"
                placeholder="Search for a user..."
                class="mt-1 block w-full"
                :class="{ 'border-red-500': form.errors.user_id }"
              />
              <InputError :message="form.errors.user_id" class="mt-2" />
            </div>

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
            <div>
              <InputLabel for="standard_hours_per_day" value="Standard Hours / Day" />
              <TextInput
                id="standard_hours_per_day"
                v-model="form.standard_hours_per_day"
                type="number"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.standard_hours_per_day" class="mt-2" />
            </div>
            <div>
              <InputLabel for="standard_hours_per_week" value="Standard Hours / Week" />
              <TextInput
                id="standard_hours_per_week"
                v-model="form.standard_hours_per_week"
                type="number"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.standard_hours_per_week" class="mt-2" />
            </div>

            <div class="md:col-span-2">
              <InputLabel for="payment_method" value="Payment Method" />
              <select
                v-model="form.payment_method"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
              >
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cash">Cash</option>
                <option value="check">Check</option>
              </select>
              <InputError :message="form.errors.payment_method" class="mt-2" />
            </div>
            <template v-if="form.payment_method === 'bank_transfer'">
              <div>
                <InputLabel for="bank_name" value="Bank Name" />
                <TextInput id="bank_name" v-model="form.bank_name" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.bank_name" class="mt-2" />
              </div>
              <div>
                <InputLabel for="bank_account_number" value="Bank Account Number (IBAN)" />
                <TextInput
                  id="bank_account_number"
                  v-model="form.bank_account_number"
                  type="text"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.bank_account_number" class="mt-2" />
              </div>
            </template>

            <div>
              <InputLabel for="employee_id" value="Employee ID (Optional)" />
              <TextInput id="employee_id" v-model="form.employee_id" type="text" class="mt-1 block w-full" />
              <InputError :message="form.errors.employee_id" class="mt-2" />
            </div>
            <div>
              <InputLabel for="tax_id" value="Tax ID (Optional)" />
              <TextInput id="tax_id" v-model="form.tax_id" type="text" class="mt-1 block w-full" />
              <InputError :message="form.errors.tax_id" class="mt-2" />
            </div>
            <div>
              <InputLabel for="hire_date" value="Hire Date" />
              <TextInput id="hire_date" v-model="form.hire_date" type="date" class="mt-1 block w-full" required />
              <InputError :message="form.errors.hire_date" class="mt-2" />
            </div>
            <div>
              <InputLabel for="termination_date" value="Termination Date (Optional)" />
              <TextInput id="termination_date" v-model="form.termination_date" type="date" class="mt-1 block w-full" />
              <InputError :message="form.errors.termination_date" class="mt-2" />
            </div>
          </div>

          <div class="flex items-center justify-end mt-6">
            <SecondaryButton :href="route('employee-profiles.index')">Cancel</SecondaryButton>
            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              Create Profile
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
