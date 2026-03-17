<script setup lang="ts">
import { useCalculatorStore } from '@/stores/calculator';

import ExamResultRow from './ExamResultRow.vue';

const store = useCalculatorStore();
</script>

<template>
  <div v-if="store.resultEntries.length > 0">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Érettségi eredmények</h2>
    <div class="space-y-3">
      <ExamResultRow
        v-for="(entry, index) in store.resultEntries"
        :key="entry.subject_id"
        :index="index"
      />
    </div>
  </div>
</template>
