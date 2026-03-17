<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait Paginatable
 *
 * Provides pagination functionality for repositories.
 * Repositories using this trait must implement the buildQuery() method.
 */
trait Paginatable
{
    /**
     * Get all records with optional pagination.
     */
    public function getAll(
        string $orderBy = 'id',
        string $order = 'desc',
        ?int $perPage = null,
        ?array $filters = [],
    ): LengthAwarePaginator|Collection {
        $query = $this->buildQuery($filters);

        // Apply ordering
        $query->orderBy($orderBy, $order);

        // If perPage is null, return all results without pagination
        if ($perPage === null) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * Build the base query for the repository.
     * This method should be implemented by the repository using this trait.
     */
    abstract protected function buildQuery(array $filters = []): Builder;
}
