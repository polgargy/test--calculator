<script setup lang="ts">
import { computed, onMounted } from 'vue';

import ExamResultsSection from '@/components/calculator/ExamResultsSection.vue';
import InstitutionFacultySelect from '@/components/calculator/InstitutionFacultySelect.vue';
import LanguageExamsSection from '@/components/calculator/LanguageExamsSection.vue';
import { useCalculatorStore } from '@/stores/calculator';

const store = useCalculatorStore();

const studentName = computed({
  get: () => store.studentName,
  set: (v: string) => store.updateStudentName(v),
});

function handleSubmit() {
  void store.submit();
}

function handleReset() {
  store.reset();
}

onMounted(store.loadInstitutions);
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold text-gray-800 mb-8">Felvételi Pontszámító Kalkulátor</h1>

      <!-- Success screen -->
      <template v-if="store.totalPoints !== null">
        <div
          class="rounded-lg bg-green-100 border border-green-300 p-6 text-green-800 text-lg font-semibold mb-6"
        >
          Összesített pontszám: {{ store.totalPoints }} pont
        </div>
        <button
          type="button"
          class="w-full bg-gray-600 text-white rounded-md py-2.5 font-semibold text-sm hover:bg-gray-700 transition-colors"
          @click="handleReset"
        >
          Vissza
        </button>
      </template>

      <!-- Form -->
      <template v-else>
        <!-- General error -->
        <div
          v-if="store.generalError"
          class="mb-6 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700"
        >
          {{ store.generalError }}
        </div>

        <form novalidate class="space-y-6" @submit.prevent="handleSubmit">
          <!-- Student name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Név</label>
            <input
              v-model="studentName"
              type="text"
              placeholder="Pl. Kovács János"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p v-if="store.fieldErrors.name" class="mt-1 text-xs text-red-600">
              {{ store.fieldErrors.name[0] }}
            </p>
          </div>

          <InstitutionFacultySelect />

          <ExamResultsSection />

          <LanguageExamsSection />

          <!-- Submit -->
          <div v-if="store.selectedFacultyId">
            <button
              type="submit"
              :disabled="store.submitting"
              class="w-full bg-blue-600 text-white rounded-md py-2.5 font-semibold text-sm hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ store.submitting ? 'Számítás folyamatban…' : 'Pontszám kiszámítása' }}
            </button>
          </div>
        </form>

        <!-- Helper info -->
        <div class="mt-8 text-xs text-gray-400 space-y-1">
          <p>* B2 nyelvvizsga: +28 pont | C1 nyelvvizsga: +40 pont</p>
          <p>* Emelt szintű érettségi: +50 pont</p>
          <p>* Többletpontok maximum: 100 pont</p>
        </div>
      </template>
    </div>
  </div>
</template>
