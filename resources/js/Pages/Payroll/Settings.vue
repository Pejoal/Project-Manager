<script setup>

import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  settings: Object,
  taxConfigurations: Array,
});

const form = useForm({
  company_name: props.settings.company_name,
  company_address: props.settings.company_address,
  company_tax_id: props.settings.company_tax_id,
  pay_period: props.settings.pay_period,
  pay_day: props.settings.pay_day,
  default_hourly_rate: props.settings.default_hourly_rate,
  working_days: props.settings.working_days || [],
  standard_start_time: props.settings.standard_start_time?.substring(0, 5) || '09:00',
  standard_end_time: props.settings.standard_end_time?.substring(0, 5) || '17:00',
  break_duration_minutes: props.settings.break_duration_minutes,
  auto_calculate_overtime: props.settings.auto_calculate_overtime,
  require_approval_for_overtime: props.settings.require_approval_for_overtime,
  auto_generate_time_entries: props.settings.auto_generate_time_entries,
  currency: props.settings.currency,
  timezone: props.settings.timezone,
});

const daysOfWeek = [
  { value: 'monday', label: 'Monday' },
  { value: 'tuesday', label: 'Tuesday' },
  { value: 'wednesday', label: 'Wednesday' },
  { value: 'thursday', label: 'Thursday' },
  { value: 'friday', label: 'Friday' },
  { value: 'saturday', label: 'Saturday' },
  { value: 'sunday', label: 'Sunday' },
];

const currencies = [
  { value: 'USD', label: 'US Dollar (USD)' },
  { value: 'EUR', label: 'Euro (EUR)' },
  { value: 'GBP', label: 'British Pound (GBP)' },
  { value: 'CAD', label: 'Canadian Dollar (CAD)' },
  { value: 'AUD', label: 'Australian Dollar (AUD)' },
];

const timezones = [
  { value: 'UTC', label: 'UTC' },
  { value: 'America/New_York', label: 'Eastern Time (US & Canada)' },
  { value: 'America/Chicago', label: 'Central Time (US & Canada)' },
  { value: 'America/Denver', label: 'Mountain Time (US & Canada)' },
  { value: 'America/Los_Angeles', label: 'Pacific Time (US & Canada)' },
  { value: 'Europe/London', label: 'London' },
  { value: 'Europe/Berlin', label: 'Berlin' },
  { value: 'Europe/Paris', label: 'Paris' },
  { value: 'Asia/Tokyo', label: 'Tokyo' },
  { value: 'Asia/Shanghai', label: 'Shanghai' },
];

const toggleWorkingDay = (day) => {
  const index = form.working_days.indexOf(day);
  if (index > -1) {
    form.working_days.splice(index, 1);
  } else {
    form.working_days.push(day);
  }
};

const isWorkingDay = (day) => {
  return form.working_days.includes(day);
};

const submit = () => {
  form.put(route('payroll.settings.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // Settings updated successfully
    },
  });
};

const activeTaxes = computed(() => {
  return props.taxConfigurations.filter((tax) => tax.is_active);
});

const inactiveTaxes = computed(() => {
  return props.taxConfigurations.filter((tax) => !tax.is_active);
});
</script>

<template>
  <Head :title="trans('payroll.settings.title')" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Header -->
      <div>
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ trans('payroll.settings.title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          {{ trans('payroll.settings.description') }}
        </p>
      </div>

      <!-- Settings Form -->
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <!-- Company Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              {{ trans('payroll.settings.company_information') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel for="company_name" :value="trans('payroll.settings.company_name')" />
                <TextInput
                  id="company_name"
                  v-model="form.company_name"
                  type="text"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.company_name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="company_tax_id" :value="trans('payroll.settings.company_tax_id')" />
                <TextInput id="company_tax_id" v-model="form.company_tax_id" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.company_tax_id" class="mt-2" />
              </div>

              <div class="md:col-span-2">
                <InputLabel for="company_address" :value="trans('payroll.settings.company_address')" />
                <textarea
                  id="company_address"
                  v-model="form.company_address"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <InputError :message="form.errors.company_address" class="mt-2" />
              </div>
            </div>
          </div>

          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              {{ trans('payroll.settings.pay_period_settings') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <InputLabel for="pay_period" :value="trans('payroll.settings.pay_period')" />
                <select
                  id="pay_period"
                  v-model="form.pay_period"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="weekly">{{ trans('payroll.settings.weekly') }}</option>
                  <option value="bi_weekly">{{ trans('payroll.settings.bi_weekly') }}</option>
                  <option value="monthly">{{ trans('payroll.settings.monthly') }}</option>
                </select>
                <InputError :message="form.errors.pay_period" class="mt-2" />
              </div>

              <div>
                <InputLabel for="pay_day" :value="trans('payroll.settings.pay_day')" />
                <TextInput
                  id="pay_day"
                  v-model.number="form.pay_day"
                  type="number"
                  min="0"
                  max="31"
                  class="mt-1 block w-full"
                  required
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ trans('payroll.settings.pay_day_hint') }}
                </p>
                <InputError :message="form.errors.pay_day" class="mt-2" />
              </div>

              <div>
                <InputLabel for="currency" :value="trans('payroll.settings.currency')" />
                <select
                  id="currency"
                  v-model="form.currency"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option v-for="curr in currencies" :key="curr.value" :value="curr.value">
                    {{ curr.label }}
                  </option>
                </select>
                <InputError :message="form.errors.currency" class="mt-2" />
              </div>

              <div>
                <InputLabel for="timezone" :value="trans('payroll.settings.timezone')" />
                <select
                  id="timezone"
                  v-model="form.timezone"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option v-for="tz in timezones" :key="tz.value" :value="tz.value">
                    {{ tz.label }}
                  </option>
                </select>
                <InputError :message="form.errors.timezone" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Working Hours -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              {{ trans('payroll.settings.working_hours') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <InputLabel for="standard_start_time" :value="trans('payroll.settings.start_time')" />
                <TextInput
                  id="standard_start_time"
                  v-model="form.standard_start_time"
                  type="time"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.standard_start_time" class="mt-2" />
              </div>

              <div>
                <InputLabel for="standard_end_time" :value="trans('payroll.settings.end_time')" />
                <TextInput
                  id="standard_end_time"
                  v-model="form.standard_end_time"
                  type="time"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.standard_end_time" class="mt-2" />
              </div>

              <div>
                <InputLabel for="break_duration_minutes" :value="trans('payroll.settings.break_duration')" />
                <TextInput
                  id="break_duration_minutes"
                  v-model.number="form.break_duration_minutes"
                  type="number"
                  min="0"
                  max="480"
                  class="mt-1 block w-full"
                  required
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ trans('payroll.settings.minutes') }}
                </p>
                <InputError :message="form.errors.break_duration_minutes" class="mt-2" />
              </div>

              <div>
                <InputLabel for="default_hourly_rate" :value="trans('payroll.settings.default_hourly_rate')" />
                <TextInput
                  id="default_hourly_rate"
                  v-model.number="form.default_hourly_rate"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.default_hourly_rate" class="mt-2" />
              </div>
            </div>

            <!-- Working Days -->
            <div class="mt-6">
              <InputLabel :value="trans('payroll.settings.working_days')" />
              <div class="mt-3 flex flex-wrap gap-3">
                <button
                  v-for="day in daysOfWeek"
                  :key="day.value"
                  type="button"
                  @click="toggleWorkingDay(day.value)"
                  :class="[
                    'px-4 py-2 rounded-md text-sm font-medium transition',
                    isWorkingDay(day.value)
                      ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                      : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600',
                  ]"
                >
                  {{ day.label }}
                </button>
              </div>
              <InputError :message="form.errors.working_days" class="mt-2" />
            </div>
          </div>

          <!-- Automation Settings -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              {{ trans('payroll.settings.automation') }}
            </h3>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="auto_calculate_overtime"
                    v-model="form.auto_calculate_overtime"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                  />
                </div>
                <div class="ml-3">
                  <label for="auto_calculate_overtime" class="font-medium text-gray-700 dark:text-gray-300">
                    {{ trans('payroll.settings.auto_calculate_overtime') }}
                  </label>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ trans('payroll.settings.auto_calculate_overtime_hint') }}
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="require_approval_for_overtime"
                    v-model="form.require_approval_for_overtime"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                  />
                </div>
                <div class="ml-3">
                  <label for="require_approval_for_overtime" class="font-medium text-gray-700 dark:text-gray-300">
                    {{ trans('payroll.settings.require_approval_for_overtime') }}
                  </label>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ trans('payroll.settings.require_approval_for_overtime_hint') }}
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="auto_generate_time_entries"
                    v-model="form.auto_generate_time_entries"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                  />
                </div>
                <div class="ml-3">
                  <label for="auto_generate_time_entries" class="font-medium text-gray-700 dark:text-gray-300">
                    {{ trans('payroll.settings.auto_generate_time_entries') }}
                  </label>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ trans('payroll.settings.auto_generate_time_entries_hint') }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-6">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              {{ trans('payroll.settings.save_settings') }}
            </PrimaryButton>
          </div>
        </form>
      </div>

      <!-- Tax Configurations -->
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ trans('payroll.tax_configurations.title') }}
          </h3>

          <!-- Active Taxes -->
          <div class="mb-6">
            <h4 class="text-md font-medium text-green-600 dark:text-green-400 mb-3">
              {{ trans('payroll.tax_configurations.active_taxes') }}
            </h4>
            <div class="space-y-2">
              <div
                v-for="tax in activeTaxes"
                :key="tax.id"
                class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg"
              >
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ tax.name }}</p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ tax.type === 'progressive' ? 'Progressive Tax' : tax.type === 'percentage' ? `${tax.rate}%` : `Flat ${tax.rate}` }}
                    - Priority: {{ tax.priority }}
                  </p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-semibold">
                  {{ trans('payroll.tax_configurations.active') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Inactive Taxes -->
          <div v-if="inactiveTaxes.length > 0">
            <h4 class="text-md font-medium text-gray-600 dark:text-gray-400 mb-3">
              {{ trans('payroll.tax_configurations.inactive_taxes') }}
            </h4>
            <div class="space-y-2">
              <div
                v-for="tax in inactiveTaxes"
                :key="tax.id"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg"
              >
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ tax.name }}</p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ tax.type === 'progressive' ? 'Progressive Tax' : tax.type === 'percentage' ? `${tax.rate}%` : `Flat ${tax.rate}` }}
                    - Priority: {{ tax.priority }}
                  </p>
                </div>
                <span class="px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 rounded-full text-xs font-semibold">
                  {{ trans('payroll.tax_configurations.inactive') }}
                </span>
              </div>
            </div>
          </div>

          <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-blue-800 dark:text-blue-200">
              <strong>{{ trans('payroll.tax_configurations.note') }}:</strong>
              {{ trans('payroll.tax_configurations.activation_note') }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
