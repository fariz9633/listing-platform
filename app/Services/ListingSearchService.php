<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ListingSearchService
{
    /**
     * Search listings with filters
     * 
     * @param array $filters [
     *   'keyword' => string|null,
     *   'category' => string|null,
     *   'city' => string|null,
     *   'suburb' => string|null,
     *   'price_min' => float|null,
     *   'price_max' => float|null,
     *   'sort' => string (relevance|newest|price_low|price_high)
     * ]
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Listing::query()
            ->approved() 
            ->with('user');

        $this->applyFilters($query, $filters);

        $this->applySorting($query, $filters['sort'] ?? 'newest');

        return $query->paginate($perPage)->withQueryString();
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['keyword'])) {
            $query->search($filters['keyword']);
        }
        
        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }
        
        if (!empty($filters['city']) || !empty($filters['suburb'])) {
            $query->byLocation(
                $filters['city'] ?? null,
                $filters['suburb'] ?? null
            );
        }
        
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $query->byPriceRange(
                $filters['price_min'] ?? null,
                $filters['price_max'] ?? null
            );
        }
    }

    protected function applySorting(Builder $query, string $sort): void
    {
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            
            case 'relevance':
                $query->orderBy('created_at', 'desc');
                break;
            
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    }

    public function getPopularCategories(int $limit = 10): array
    {
        return Listing::approved()
            ->select('category', \DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getPopularCities(int $limit = 10): array
    {
        return Listing::approved()
            ->select('city', \DB::raw('COUNT(*) as count'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getFilterOptions(): array
    {
        return [
            'categories' => $this->getPopularCategories(),
            'cities' => $this->getPopularCities(),
            'price_ranges' => [
                ['min' => 0, 'max' => 100, 'label' => 'Under $100'],
                ['min' => 100, 'max' => 200, 'label' => '$100 - $200'],
                ['min' => 200, 'max' => 300, 'label' => '$200 - $300'],
                ['min' => 300, 'max' => 500, 'label' => '$300 - $500'],
                ['min' => 500, 'max' => null, 'label' => '$500+'],
            ],
        ];
    }
}

