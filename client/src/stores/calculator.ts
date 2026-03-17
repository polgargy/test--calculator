import { computed, readonly, ref } from 'vue';

import { defineStore } from 'pinia';

import {
  calculatorApi,
  type ApiError,
  type EnumOption,
  type Faculty,
  type Institution,
  type Subject,
} from '@/api/calculator';

interface ResultEntry {
  subject_id: number;
  subject_name: string;
  is_required: boolean;
  requires_advanced_level: boolean;
  advanced_level: boolean;
  result: string;
  error: string;
}

interface LanguageExamEntry {
  uid: string;
  language: string;
  level: string;
}

export const useCalculatorStore = defineStore('calculator', () => {
  // Options (enums from API)
  const languages = ref<EnumOption[]>([]);
  const levels = ref<EnumOption[]>([]);

  // Institutions
  const institutions = ref<Institution[]>([]);
  const requiredSubjects = ref<Subject[]>([]);
  const loadingInstitutions = ref(false);
  const institutionsError = ref('');

  // Form state
  const studentName = ref('');
  const selectedInstitutionId = ref<number | null>(null);
  const selectedFacultyId = ref<number | null>(null);
  const resultEntries = ref<ResultEntry[]>([]);
  const languageExams = ref<LanguageExamEntry[]>([]);

  // Submission state
  const submitting = ref(false);
  const totalPoints = ref<number | null>(null);
  const generalError = ref('');
  const fieldErrors = ref<Record<string, string[]>>({});

  // Computed
  const availableFaculties = computed<Faculty[]>(() => {
    if (!selectedInstitutionId.value) return [];
    return institutions.value.find((i) => i.id === selectedInstitutionId.value)?.faculties ?? [];
  });

  const selectedFaculty = computed<Faculty | null>(() => {
    if (!selectedFacultyId.value) return null;
    return availableFaculties.value.find((f) => f.id === selectedFacultyId.value) ?? null;
  });

  // Actions
  async function loadInstitutions() {
    loadingInstitutions.value = true;
    institutionsError.value = '';
    try {
      const [opts, insts, subjects] = await Promise.all([
        calculatorApi.fetchLanguageOptions(),
        calculatorApi.fetchInstitutions(),
        calculatorApi.fetchRequiredSubjects(),
      ]);
      languages.value = opts.languages;
      levels.value = opts.levels;
      institutions.value = insts;
      requiredSubjects.value = subjects;
    } catch {
      institutionsError.value = 'Az intézmények betöltése sikertelen.';
    } finally {
      loadingInstitutions.value = false;
    }
  }

  function selectInstitution(id: number | null) {
    selectedInstitutionId.value = id;
    selectedFacultyId.value = null;
    resultEntries.value = [];
    totalPoints.value = null;
    generalError.value = '';
    fieldErrors.value = {};
  }

  function selectFaculty(id: number | null) {
    selectedFacultyId.value = id;
    buildResultEntries();
    totalPoints.value = null;
    generalError.value = '';
    fieldErrors.value = {};
  }

  function addLanguageExam() {
    languageExams.value.push({ uid: crypto.randomUUID(), language: '', level: '' });
  }

  function removeLanguageExam(index: number) {
    languageExams.value.splice(index, 1);
  }

  function updateStudentName(name: string) {
    studentName.value = name;
  }

  function updateResultEntry(index: number, field: 'result', value: string): void;
  function updateResultEntry(index: number, field: 'advanced_level', value: boolean): void;
  function updateResultEntry(
    index: number,
    field: 'result' | 'advanced_level',
    value: string | boolean,
  ): void {
    const entry = resultEntries.value[index];
    if (!entry) return;
    if (field === 'result') entry.result = value as string;
    else entry.advanced_level = value as boolean;
  }

  function updateLanguageExam(index: number, field: 'language' | 'level', value: string) {
    const exam = languageExams.value[index];
    if (!exam) return;
    exam[field] = value;
  }

  async function submit() {
    if (!validate()) return;

    submitting.value = true;
    totalPoints.value = null;
    generalError.value = '';
    fieldErrors.value = {};

    const results = resultEntries.value
      .filter((e) => e.result !== '')
      .map((e) => ({
        subject_id: e.subject_id,
        advanced_level: e.advanced_level,
        result: Number(e.result),
      }));

    const exams = languageExams.value
      .filter((e) => e.language && e.level)
      .map(({ language, level }) => ({ language, level }));

    try {
      const response = await calculatorApi.calculatePoints({
        name: studentName.value.trim(),
        institution_id: selectedInstitutionId.value!,
        faculty_id: selectedFacultyId.value!,
        results,
        language_exams: exams,
      });
      totalPoints.value = response.total_points;
    } catch (err) {
      const apiError = err as ApiError;
      if (apiError?.errors) {
        fieldErrors.value = apiError.errors;
        if (apiError.errors.calculation?.[0]) {
          generalError.value = apiError.errors.calculation[0];
        }
      } else {
        generalError.value = apiError?.message ?? 'Ismeretlen hiba történt.';
      }
    } finally {
      submitting.value = false;
    }
  }

  // Internals
  function buildResultEntries() {
    const faculty = selectedFaculty.value;
    if (!faculty) {
      resultEntries.value = [];
      return;
    }

    const entries: ResultEntry[] = [];
    const addedIds = new Set<number>();

    // 1. Globally required subjects (from API)
    for (const subj of requiredSubjects.value) {
      if (!addedIds.has(subj.id)) {
        addedIds.add(subj.id);
        entries.push(makeEntry(subj, true, false));
      }
    }

    // 2. Faculty's required subject (may overlap with globally required)
    if (!addedIds.has(faculty.required_subject.id)) {
      addedIds.add(faculty.required_subject.id);
      entries.push(makeEntry(faculty.required_subject, true, faculty.requires_advanced_level));
    } else if (faculty.requires_advanced_level) {
      // Subject already added globally — promote it to require advanced level
      const existing = entries.find((e) => e.subject_id === faculty.required_subject.id);
      if (existing) {
        existing.requires_advanced_level = true;
        existing.advanced_level = true;
      }
    }

    // 3. Elective subjects
    for (const subj of faculty.elective_subjects) {
      if (!addedIds.has(subj.id)) {
        addedIds.add(subj.id);
        entries.push(makeEntry(subj, false, false));
      }
    }

    resultEntries.value = entries;
  }

  function makeEntry(
    subject: Subject,
    isRequired: boolean,
    requiresAdvancedLevel: boolean,
  ): ResultEntry {
    return {
      subject_id: subject.id,
      subject_name: subject.name,
      is_required: isRequired,
      requires_advanced_level: requiresAdvancedLevel,
      advanced_level: requiresAdvancedLevel,
      result: '',
      error: '',
    };
  }

  function validate(): boolean {
    let valid = true;

    for (const entry of resultEntries.value) {
      entry.error = '';
    }
    generalError.value = '';

    if (!studentName.value.trim()) {
      generalError.value = 'A név megadása kötelező.';
      return false;
    }

    if (!selectedInstitutionId.value || !selectedFacultyId.value) {
      generalError.value = 'Az intézmény és a szak megadása kötelező.';
      return false;
    }

    const faculty = selectedFaculty.value!;
    const requiredIds = requiredSubjects.value.map((s) => s.id);
    const facultyRequiredId = faculty.required_subject_id;
    const electiveIds = new Set(faculty.elective_subjects.map((s) => s.id));

    for (const entry of resultEntries.value) {
      const mustProvide =
        requiredIds.includes(entry.subject_id) || entry.subject_id === facultyRequiredId;

      if (mustProvide && (entry.result === '' || entry.result === null)) {
        entry.error = 'A mező kitöltése kötelező.';
        valid = false;
        continue;
      }

      if (entry.result !== '') {
        const num = Number(entry.result);
        if (isNaN(num) || num < 0 || num > 100) {
          entry.error = 'Az eredmény 0–100 közé kell essen.';
          valid = false;
        } else if (num < 20) {
          entry.error = 'Pontszámítás nem lehetséges';
          valid = false;
        }
      }
    }

    const hasElective = resultEntries.value.some(
      (e) => electiveIds.has(e.subject_id) && e.result !== '',
    );
    if (!hasElective) {
      generalError.value = 'Legalább egy választható tantárgy megadása szükséges.';
      valid = false;
    }

    return valid;
  }

  function reset() {
    studentName.value = '';
    selectedInstitutionId.value = null;
    selectedFacultyId.value = null;
    resultEntries.value = [];
    languageExams.value = [];
    totalPoints.value = null;
    generalError.value = '';
    fieldErrors.value = {};
  }

  return {
    // State (readonly — mutate only via actions)
    languages: readonly(languages),
    levels: readonly(levels),
    institutions: readonly(institutions),
    loadingInstitutions: readonly(loadingInstitutions),
    institutionsError: readonly(institutionsError),
    studentName: readonly(studentName),
    selectedInstitutionId: readonly(selectedInstitutionId),
    selectedFacultyId: readonly(selectedFacultyId),
    resultEntries: readonly(resultEntries),
    languageExams: readonly(languageExams),
    submitting: readonly(submitting),
    totalPoints: readonly(totalPoints),
    generalError: readonly(generalError),
    fieldErrors: readonly(fieldErrors),
    // Computed (already readonly)
    availableFaculties,
    selectedFaculty,
    // Actions
    loadInstitutions,
    selectInstitution,
    selectFaculty,
    addLanguageExam,
    removeLanguageExam,
    submit,
    updateStudentName,
    updateResultEntry,
    updateLanguageExam,
    reset,
  };
});
