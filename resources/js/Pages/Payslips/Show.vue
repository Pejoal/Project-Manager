<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
  payslip: Object,
  settings: Object, // Accept the new settings prop
});

const isDownloading = ref(false);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount || 0);
};

const formatDate = (date) => {
  return date
    ? new Date(date).toLocaleDateString('de-DE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
      })
    : 'N/A';
};

const downloadPayslip = async () => {
  isDownloading.value = true;
  try {
    const response = await axios.get(route('payslips.download-pdf', props.payslip.id), {
      responseType: 'blob', // This is crucial to receive the file data correctly
    });

    // Create a URL for the blob data
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;

    // Extract the filename from the 'Content-Disposition' header if available
    const contentDisposition = response.headers['content-disposition'];
    let filename = `Lohnabrechnung-${props.payslip.payslip_number}.pdf`; // Fallback filename
    if (contentDisposition) {
      const filenameMatch = contentDisposition.match(/filename="(.+)"/);
      if (filenameMatch && filenameMatch.length === 2) {
        filename = filenameMatch[1];
      }
    }

    link.setAttribute('download', filename);
    document.body.appendChild(link);

    // Trigger the download and clean up
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error('PDF download error:', error);
    alert('Could not download the PDF. Please try again or check the console for errors.');
  } finally {
    isDownloading.value = false;
  }
};
</script>

<template>
  <Head :title="`Payslip ${payslip.payslip_number}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            Lohnabrechnung für {{ payslip.user.name }}
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Abrechnungsnummer: {{ payslip.payslip_number }}</p>
        </div>
        <div class="flex items-center space-x-4">
          <Link :href="route('payslips.index')" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
            ← Zurück zur Übersicht
          </Link>
          <PrimaryButton @click="downloadPayslip" :disabled="isDownloading">
            <svg
              v-if="isDownloading"
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <span>{{ isDownloading ? 'Wird erstellt...' : 'PDF Herunterladen' }}</span>
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
        <div class="p-8">
          <!-- Header -->
          <div class="flex justify-between items-start mb-8">
            <div class="text-sm">
              <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ payslip.user.name }}</h3>
              <p class="text-gray-600 dark:text-gray-400">{{ payslip.user.employee_profile?.street_address }}</p>
              <p class="text-gray-600 dark:text-gray-400">
                {{ payslip.user.employee_profile?.postal_code }} {{ payslip.user.employee_profile?.city }}
              </p>
            </div>
            <div class="text-sm text-right">
              <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">
                {{ settings?.company_name ?? 'Your Company' }}
              </h3>
              <p class="text-gray-600 dark:text-gray-400">{{ settings?.company_address ?? 'Your Address' }}</p>
              <p class="text-gray-600 dark:text-gray-400">Steuernummer: {{ settings?.company_tax_id ?? 'N/A' }}</p>
            </div>
          </div>

          <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Lohn- und Gehaltsabrechnung</h1>
          </div>

          <!-- Summary Table -->
          <div
            class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm border-t border-b border-gray-200 dark:border-gray-700 py-4 mb-8"
          >
            <div>
              <p class="font-semibold text-gray-500 dark:text-gray-400">Abrechnungszeitraum</p>
              <p class="text-gray-800 dark:text-gray-200">
                {{ formatDate(payslip.pay_period_start) }} - {{ formatDate(payslip.pay_period_end) }}
              </p>
            </div>
            <div>
              <p class="font-semibold text-gray-500 dark:text-gray-400">Personalnummer</p>
              <p class="text-gray-800 dark:text-gray-200">{{ payslip.user.employee_profile?.employee_id ?? 'N/A' }}</p>
            </div>
            <div>
              <p class="font-semibold text-gray-500 dark:text-gray-400">Zahltag</p>
              <p class="text-gray-800 dark:text-gray-200">{{ formatDate(payslip.pay_date) }}</p>
            </div>
            <div>
              <p class="font-semibold text-gray-500 dark:text-gray-400">Steuer-ID</p>
              <p class="text-gray-800 dark:text-gray-200">{{ payslip.user.employee_profile?.tax_id ?? 'N/A' }}</p>
            </div>
          </div>

          <!-- Earnings -->
          <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4 text-gray-800 dark:text-gray-200 border-b pb-2">Bezüge</h3>
            <div class="space-y-3 text-sm">
              <div class="flex justify-between items-center">
                <div>
                  <p class="font-medium text-gray-800 dark:text-gray-200">Grundgehalt (Regulär)</p>
                  <p class="text-gray-500 dark:text-gray-400">
                    {{ payslip.regular_hours }}h @ {{ formatCurrency(payslip.regular_rate) }}/Std.
                  </p>
                </div>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{
                  formatCurrency(payslip.gross_regular_pay)
                }}</span>
              </div>
              <div v-if="payslip.overtime_hours > 0" class="flex justify-between items-center">
                <div>
                  <p class="font-medium text-gray-800 dark:text-gray-200">Überstunden</p>
                  <p class="text-gray-500 dark:text-gray-400">
                    {{ payslip.overtime_hours }}h @ {{ formatCurrency(payslip.overtime_rate) }}/Std.
                  </p>
                </div>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{
                  formatCurrency(payslip.gross_overtime_pay)
                }}</span>
              </div>
              <div
                class="flex justify-between items-center font-bold border-t border-gray-200 dark:border-gray-700 pt-3 mt-3"
              >
                <span class="text-gray-900 dark:text-gray-100">Gesamtbrutto</span>
                <span class="text-gray-900 dark:text-gray-100">{{ formatCurrency(payslip.gross_total_pay) }}</span>
              </div>
            </div>
          </div>

          <!-- Deductions -->
          <div class="mb-8">
            <h3 class="font-semibold text-lg mb-4 text-gray-800 dark:text-gray-200 border-b pb-2">
              Gesetzliche Abzüge
            </h3>
            <div class="space-y-3 text-sm">
              <div v-for="deduction in payslip.tax_deductions" :key="deduction.name" class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">{{ deduction.name }}</span>
                <span class="font-medium text-red-600 dark:text-red-400">- {{ formatCurrency(deduction.amount) }}</span>
              </div>
              <div class="flex justify-between font-bold border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                <span class="text-gray-900 dark:text-gray-100">Gesamtabzüge</span>
                <span class="font-bold text-red-600 dark:text-red-400"
                  >- {{ formatCurrency(payslip.total_tax_deductions) }}</span
                >
              </div>
            </div>
          </div>

          <!-- Net Pay -->
          <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg mt-8">
            <div class="flex justify-between items-center">
              <span class="text-xl font-bold text-gray-900 dark:text-gray-100">NETTOVERDIENST</span>
              <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{
                formatCurrency(payslip.net_pay)
              }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
