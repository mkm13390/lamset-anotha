<?php

namespace App\Services;

use App\Models\AdminSearchIndex;
use Illuminate\Support\Collection;

class AdminSearchService
{
    public function search(string $term, int $limit = 30): Collection
    {
        $term = trim($term);

        if ($term === '') {
            return collect();
        }

        return AdminSearchIndex::query()
            ->where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%')
                    ->orWhere('subtitle', 'like', '%' . $term . '%')
                    ->orWhere('searchable_text', 'like', '%' . $term . '%');
            })
            ->latest('updated_at')
            ->limit($limit)
            ->get();
    }
}
