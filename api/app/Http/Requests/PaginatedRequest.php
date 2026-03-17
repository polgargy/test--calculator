<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaginatedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'order_by' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (!in_array($value, $this->allowedOrderByColumns())) {
                    $fail("The {$attribute} field must be one of: ".implode(', ', $this->allowedOrderByColumns()));
                }
            }],
            'order' => 'nullable|string|in:asc,desc',
        ], $this->additionalRules());
    }

    /**
     * Additional rules to be merged with pagination rules.
     * Override this method in child classes to add custom validation rules.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function additionalRules(): array
    {
        return [];
    }

    /**
     * Define allowed columns for ordering.
     * Override this method in child classes to specify allowed columns.
     *
     * @return array<string>
     */
    protected function allowedOrderByColumns(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    /**
     * Check if pagination is requested.
     */
    public function shouldPaginate(): bool
    {
        return $this->has('page');
    }

    /**
     * Get the page number.
     */
    public function getPage(): int
    {
        return (int) $this->query('page', 1);
    }

    /**
     * Get the per_page value.
     * Returns the default per_page if page is provided but per_page is not.
     */
    public function getPerPage(int $default = 15): int
    {
        return (int) $this->query('per_page', $default);
    }

    /**
     * Get the order_by column.
     */
    public function getOrderBy(string $default = 'id'): string
    {
        return $this->query('order_by', $default);
    }

    /**
     * Get the order direction.
     */
    public function getOrder(string $default = 'desc'): string
    {
        return strtolower($this->query('order', $default));
    }
}
