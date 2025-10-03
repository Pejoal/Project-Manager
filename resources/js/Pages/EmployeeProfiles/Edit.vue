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

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ $t('payroll.employee_profiles.edit_title') }}: {{ profile.user.name }}
      </h2>
    </div>
  </header>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <!-- User Information (Read-only) -->
          <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('payroll.employee_profiles.user_information') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel for="user_name" :value="$t('words.name')" />
                <TextInput id="user_name" :value="profile.user.name" type="text" class="mt-1 block w-full" readonly />
              </div>
              <div>
                <InputLabel for="user_email" :value="$t('words.email')" />
                <TextInput
                  id="user_email"
                  :value="profile.user.email"
                  type="email"
                  class="mt-1 block w-full"
                  readonly
                />
              </div>
            </div>
          </div>

          <!-- Employment Details -->
          <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('payroll.employee_profiles.employment_details') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel for="employee_id" :value="$t('payroll.employee_profiles.employee_id')" />
                <TextInput
                  id="employee_id"
                  v-model="form.employee_id"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="Auto-generated if left empty"
                />
                <InputError :message="form.errors.employee_id" class="mt-2" />
              </div>

              <div>
                <InputLabel for="hire_date" :value="$t('payroll.employee_profiles.hire_date')" />
                <TextInput id="hire_date" v-model="form.hire_date" type="date" class="mt-1 block w-full" required />
                <InputError :message="form.errors.hire_date" class="mt-2" />
              </div>

              <div>
                <InputLabel for="termination_date" :value="$t('payroll.employee_profiles.termination_date')" />
                <TextInput
                  id="termination_date"
                  v-model="form.termination_date"
                  type="date"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.termination_date" class="mt-2" />
              </div>

              <div class="flex items-center space-x-2 mt-6">
                <Checkbox id="is_active" v-model:checked="form.is_active" />
                <InputLabel for="is_active" :value="$t('payroll.employee_profiles.is_active')" />
                <InputError :message="form.errors.is_active" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Compensation -->
          <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('payroll.employee_profiles.compensation') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel for="base_hourly_rate" :value="$t('payroll.employee_profiles.base_hourly_rate')" />
                <TextInput
                  id="base_hourly_rate"
                  v-model="form.base_hourly_rate"
                  type="number"
                  step="0.01"
                  min="0"
                  max="999.99"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.base_hourly_rate" class="mt-2" />
              </div>

              <div>
                <InputLabel
                  for="overtime_rate_multiplier"
                  :value="$t('payroll.employee_profiles.overtime_multiplier')"
                />
                <TextInput
                  id="overtime_rate_multiplier"
                  v-model="form.overtime_rate_multiplier"
                  type="number"
                  step="0.1"
                  min="1"
                  max="5"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.overtime_rate_multiplier" class="mt-2" />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ $t('payroll.employee_profiles.overtime_multiplier_hint') }}
                </p>
              </div>

              <div>
                <InputLabel
                  for="standard_hours_per_day"
                  :value="$t('payroll.employee_profiles.standard_hours_per_day')"
                />
                <TextInput
                  id="standard_hours_per_day"
                  v-model="form.standard_hours_per_day"
                  type="number"
                  min="1"
                  max="24"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.standard_hours_per_day" class="mt-2" />
              </div>

              <div>
                <InputLabel
                  for="standard_hours_per_week"
                  :value="$t('payroll.employee_profiles.standard_hours_per_week')"
                />
                <TextInput
                  id="standard_hours_per_week"
                  v-model="form.standard_hours_per_week"
                  type="number"
                  min="1"
                  max="168"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.standard_hours_per_week" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Payment Information -->
          <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('payroll.employee_profiles.payment_information') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <InputLabel for="payment_method" :value="$t('payroll.employee_profiles.payment_method')" />
                <select
                  id="payment_method"
                  v-model="form.payment_method"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="bank_transfer">{{ $t('payroll.payment_methods.bank_transfer') }}</option>
                  <option value="cash">{{ $t('payroll.payment_methods.cash') }}</option>
                  <option value="check">{{ $t('payroll.payment_methods.check') }}</option>
                </select>
                <InputError :message="form.errors.payment_method" class="mt-2" />
              </div>

              <div>
                <InputLabel for="bank_name" :value="$t('payroll.employee_profiles.bank_name')" />
                <TextInput
                  id="bank_name"
                  v-model="form.bank_name"
                  type="text"
                  maxlength="100"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.bank_name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="bank_account_number" :value="$t('payroll.employee_profiles.bank_account')" />
                <TextInput
                  id="bank_account_number"
                  v-model="form.bank_account_number"
                  type="text"
                  maxlength="50"
                  class="mt-1 block w-full"
                />
                <InputError :message="form.errors.bank_account_number" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Tax Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              {{ $t('payroll.employee_profiles.tax_information') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel for="tax_id" :value="$t('payroll.employee_profiles.tax_id')" />
                <TextInput id="tax_id" v-model="form.tax_id" type="text" maxlength="50" class="mt-1 block w-full" />
                <InputError :message="form.errors.tax_id" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <SecondaryButton :href="route('employee-profiles.index')" class="mr-3">
              {{ $t('words.cancel') }}
            </SecondaryButton>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              {{ $t('words.update') }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
