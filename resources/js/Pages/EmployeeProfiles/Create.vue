<script setup></script>
<template>
  <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="mb-5">
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ $t('payroll.employee_profiles.create_employee_profile') }}
      </h1>
      <p class="mt-1 text-sm text-gray-600">
        {{ $t('payroll.employee_profiles.create_employee_profile_description') }}
      </p>
    </div>
    <EmployeeProfileForm :action="route('employee-profiles.store')" method="post" />
  </div>
</template>
