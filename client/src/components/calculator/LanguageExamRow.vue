<script setup lang="ts">
import { computed } from 'vue';

import { useCalculatorStore } from '@/stores/calculator';

const props = defineProps<{ index: number }>();

const store = useCalculatorStore();
const exam = computed(() => store.languageExams[props.index]!);

const language = computed({
  get: () => exam.value.language,
  set: (v: string) => store.updateLanguageExam(props.index, 'language', v),
});

const level = computed({
  get: () => exam.value.level,
  set: (v: string) => store.updateLanguageExam(props.index, 'level', v),
});

function removeLanguageExam() {
  store.removeLanguageExam(props.index);
}
</script>

<template>
  <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg p-3">
    <select
      v-model="language"
      class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <option value="" disabled>– Nyelv –</option>
      <option v-for="lang in store.languages" :key="lang.value" :value="lang.value">
        {{ lang.label }}
      </option>
    </select>

    <select
      v-model="level"
      class="w-24 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <option value="" disabled>– Szint –</option>
      <option v-for="lvl in store.levels" :key="lvl.value" :value="lvl.value">
        {{ lvl.label }}
      </option>
    </select>

    <button
      type="button"
      class="text-red-400 hover:text-red-600 text-sm font-medium"
      @click="removeLanguageExam"
    >
      Törlés
    </button>
  </div>
</template>
