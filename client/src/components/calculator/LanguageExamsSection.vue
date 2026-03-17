<script setup lang="ts">
import { useCalculatorStore } from '@/stores/calculator';

import LanguageExamRow from './LanguageExamRow.vue';

const store = useCalculatorStore();

function addLanguageExam() {
  store.addLanguageExam();
}
</script>

<template>
  <div v-if="store.selectedFacultyId">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-lg font-semibold text-gray-700">Nyelvvizsgák</h2>
      <button
        type="button"
        class="text-sm text-blue-600 hover:text-blue-800 font-medium cursor-pointer"
        @click="addLanguageExam"
      >
        + Hozzáadás
      </button>
    </div>

    <div v-if="store.languageExams.length === 0" class="text-sm text-gray-400 italic">
      Nincs megadott nyelvvizsga.
    </div>

    <div class="space-y-2">
      <LanguageExamRow
        v-for="(exam, index) in store.languageExams"
        :key="exam.uid"
        :index="index"
      />
    </div>
  </div>
</template>
