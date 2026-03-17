export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
export type ApiRoute = { url: string; method: HttpMethod };

/**
 * Spreads a route definition into apiRequest arguments.
 * Usage: apiRequest<T>(...buildRequest(routes.x, { body: JSON.stringify(data) }))
 */
export function buildRequest(
  route: ApiRoute,
  extra: Omit<RequestInit, 'method'> = {},
): [string, RequestInit] {
  return [route.url, { method: route.method, ...extra }];
}

export const CALCULATOR = {
  languageOptions: { url: '/language-options', method: 'GET' } as ApiRoute,
  institutions: { url: '/institutions', method: 'GET' } as ApiRoute,
  requiredSubjects: { url: '/required-subjects', method: 'GET' } as ApiRoute,
  calculate: { url: '/calculator', method: 'POST' } as ApiRoute,
} as const;
