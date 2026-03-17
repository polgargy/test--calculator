<script setup lang="ts">
import { computed } from 'vue';

import { useCalculatorStore } from '@/stores/calculator';

const props = defineProps<{ index: number }>();

const store = useCalculatorStore();
const entry = computed(() => store.resultEntries[props.index]!);

const result = computed({
  get: () => entry.value.result,
  set: (v: string) => store.updateResultEntry(props.index, 'result', v),
});

const advancedLevel = computed({
  get: () => entry.value.advanced_level,
  set: (v: boolean) => store.updateResultEntry(props.index, 'advanced_level', v),
});
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-lg p-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <span class="text-sm font-medium text-gray-800">
        {{ entry.subject_name }}
        <span v-if="entry.is_required" class="ml-1 text-xs text-red-500 font-normal">
          *Kötelező <span v-if="entry.requires_advanced_level"> (emelt szinten) </span>
        </span>
      </span>

      <div class="flex items-center gap-4">
        <!-- Result percentage -->
        <div class="flex items-center gap-1">
          <input
            v-model="result"
            type="number"
            min="0"
            max="100"
            placeholder="0–100"
            class="w-20 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <span class="text-sm text-gray-500">%</span>
        </div>

        <!-- Advanced level -->
        <label
          class="flex items-center gap-1 text-sm cursor-pointer"
          :class="entry.requires_advanced_level ? 'text-orange-600 font-medium' : 'text-gray-700'"
        >
          <input
            v-model="advancedLevel"
            type="checkbox"
            :disabled="entry.requires_advanced_level"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
          Emelt szint
        </label>
      </div>
    </div>

    <p v-if="entry.error" class="mt-1 text-xs text-red-600">{{ entry.error }}</p>
  </div>
</template>
