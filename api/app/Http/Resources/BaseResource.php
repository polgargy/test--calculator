<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Format date to standard Y-m-d H:i:s format
     */
    protected function formatDate($date): ?string
    {
        return $date?->format('Y-m-d H:i:s');
    }

    /**
     * Format date to human readable format
     */
    protected function formatDateHuman($date): ?string
    {
        return $date?->diffForHumans();
    }

    /**
     * Get common timestamp fields formatted
     *
     * @return array<string, string|null>
     */
    protected function timestamps(): array
    {
        return [
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
