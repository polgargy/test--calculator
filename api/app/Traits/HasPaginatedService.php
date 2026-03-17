<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait HasPaginatedService
 *
 * Provides pagination functionality for services.
 * Services using this trait must have a repository property with a getAll() method.
 */
trait HasPaginatedService
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
        return $this->getRepository()->getAll($orderBy, $order, $perPage, $filters);
    }

    /**
     * Get the repository instance.
     * This method should be implemented or the repository should be accessible as a property.
     */
    abstract protected function getRepository(): mixed;
}
