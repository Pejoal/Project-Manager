<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  payslip: Object,
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount || 0);
};

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString('de-DE') : 'N/A';
};
</script>

<template>
  <Head :title="`Payslip #${payslip.id}`" />
  <header>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Payslip for {{ payslip.user.name }}
    </h2>
  </header>
  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Payslip</h1>
          <p class="text-gray-500 dark:text-gray-400">
            Pay Period: {{ formatDate(payslip.pay_period_start) }} - {{ formatDate(payslip.pay_period_end) }}
          </p>
        </div>
        <div class="grid grid-cols-2 gap-8 mb-8">
          <div>
            <h3 class="font-semibold text-gray-700 dark:text-gray-300">Employee Details</h3>
            <p class="text-gray-900 dark:text-gray-100">{{ payslip.user.name }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ payslip.user.employee_profile.employee_id }}</p>
          </div>
          <div class="text-right">
            <h3 class="font-semibold text-gray-700 dark:text-gray-300">Pay Date</h3>
            <p class="text-gray-900 dark:text-gray-100">{{ formatDate(payslip.pay_date) }}</p>
          </div>
        </div>

        <h3 class="font-semibold text-lg mb-2 text-gray-800 dark:text-gray-200 border-b pb-2">Earnings</h3>
        <div class="space-y-2 mb-6">
          <div class="flex justify-between">
            <span>Regular Pay ({{ payslip.regular_hours }}h @ {{ formatCurrency(payslip.regular_rate) }})</span>
            <span>{{ formatCurrency(payslip.gross_regular_pay) }}</span>
          </div>
          <div class="flex justify-between">
            <span>Overtime Pay ({{ payslip.overtime_hours }}h @ {{ formatCurrency(payslip.overtime_rate) }})</span>
            <span>{{ formatCurrency(payslip.gross_overtime_pay) }}</span>
          </div>
          <div class="flex justify-between font-bold border-t pt-2">
            <span>Total Gross Pay</span>
            <span>{{ formatCurrency(payslip.gross_total_pay) }}</span>
          </div>
        </div>

        <h3 class="font-semibold text-lg mb-2 text-gray-800 dark:text-gray-200 border-b pb-2">Deductions</h3>
        <div class="space-y-2 mb-6">
          <div v-for="deduction in payslip.tax_deductions" :key="deduction.name" class="flex justify-between">
            <span>{{ deduction.name }}</span>
            <span>- {{ formatCurrency(deduction.amount) }}</span>
          </div>
          <div class="flex justify-between font-bold border-t pt-2">
            <span>Total Deductions</span>
            <span>- {{ formatCurrency(payslip.total_tax_deductions) }}</span>
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg text-right">
          <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            <span>Net Pay: </span>
            <span>{{ formatCurrency(payslip.net_pay) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
