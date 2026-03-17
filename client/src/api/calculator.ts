import { CALCULATOR as routes, buildRequest } from '@/api/_api-routes';
import { apiRequest } from '@/lib/apiClient';

export interface EnumOption {
  value: string;
  label: string;
}

export interface Subject {
  id: number;
  name: string;
  required: boolean;
}

export interface Faculty {
  id: number;
  institution_id: number;
  name: string;
  required_subject_id: number;
  requires_advanced_level: boolean;
  required_subject: Subject;
  elective_subjects: Subject[];
}

export interface Institution {
  id: number;
  name: string;
  faculties: Faculty[];
}

export interface Options {
  languages: EnumOption[];
  levels: EnumOption[];
}

export interface CalculatePayload {
  name: string;
  institution_id: number;
  faculty_id: number;
  results: { subject_id: number; advanced_level: boolean; result: number }[];
  language_exams: { language: string; level: string }[];
}

export interface CalculateResult {
  total_points: number;
}

export interface ApiError {
  errors?: Record<string, string[]>;
  message?: string;
}

export const calculatorApi = {
  fetchLanguageOptions: () => apiRequest<Options>(...buildRequest(routes.languageOptions)),

  fetchInstitutions: () =>
    apiRequest<{ data: Institution[] }>(...buildRequest(routes.institutions)).then((r) => r.data),

  fetchRequiredSubjects: () =>
    apiRequest<{ data: Subject[] }>(...buildRequest(routes.requiredSubjects)).then((r) => r.data),

  calculatePoints: (payload: CalculatePayload) =>
    apiRequest<CalculateResult>(
      ...buildRequest(routes.calculate, { body: JSON.stringify(payload) }),
    ),
};
