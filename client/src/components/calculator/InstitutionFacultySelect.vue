<script setup lang="ts">
import { useCalculatorStore } from '@/stores/calculator';

const store = useCalculatorStore();

function onInstitutionChange(event: Event) {
  const val = (event.target as HTMLSelectElement).value;
  store.selectInstitution(val ? Number(val) : null);
}

function onFacultyChange(event: Event) {
  const val = (event.target as HTMLSelectElement).value;
  store.selectFaculty(val ? Number(val) : null);
}
</script>

<template>
  <div class="space-y-6">
    <!-- Institution -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Intézmény</label>
      <select
        :value="store.selectedInstitutionId"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        @change="onInstitutionChange"
      >
        <option :value="null" disabled>– Válasszon intézményt –</option>
        <option v-for="inst in store.institutions" :key="inst.id" :value="inst.id">
          {{ inst.name }}
        </option>
      </select>
      <p v-if="store.fieldErrors.institution_id" class="mt-1 text-xs text-red-600">
        {{ store.fieldErrors.institution_id[0] }}
      </p>
    </div>

    <!-- Faculty -->
    <div v-if="store.selectedInstitutionId">
      <label class="block text-sm font-medium text-gray-700 mb-1">Szak</label>
      <select
        :value="store.selectedFacultyId"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        @change="onFacultyChange"
      >
        <option :value="null" disabled>– Válasszon szakot –</option>
        <option v-for="fac in store.availableFaculties" :key="fac.id" :value="fac.id">
          {{ fac.name }}
        </option>
      </select>
      <p v-if="store.fieldErrors.faculty_id" class="mt-1 text-xs text-red-600">
        {{ store.fieldErrors.faculty_id[0] }}
      </p>
    </div>
  </div>
</template>
