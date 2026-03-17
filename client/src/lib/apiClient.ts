const API_BASE = (import.meta.env.VITE_API_URL as string) || 'http://localhost:8080/api';

export async function apiRequest<T>(url: string, options?: RequestInit): Promise<T> {
  const res = await fetch(`${API_BASE}${url}`, {
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    ...options,
  });

  const json = (await res.json()) as unknown;

  if (!res.ok) {
    throw json;
  }

  return json as T;
}
