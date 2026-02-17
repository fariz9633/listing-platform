<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\ListingSearchService;
use App\Services\ListingService;
use Illuminate\Http\Request;


class ListingController extends Controller
{
    protected $searchService;
    protected $listingService;

    public function __construct(
        ListingSearchService $searchService,
        ListingService $listingService
    ) {
        $this->searchService = $searchService;
        $this->listingService = $listingService;
    }

    public function index(Request $request)
    {
        
        $filters = [
            'keyword' => $request->input('q'),
            'category' => $request->input('category'),
            'city' => $request->input('city'),
            'suburb' => $request->input('suburb'),
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'sort' => $request->input('sort', 'newest'),
        ];

        
        $listings = $this->searchService->search($filters, 20);

        $filterOptions = $this->searchService->getFilterOptions();

        return view('listings.index', [
            'listings' => $listings,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    public function show(Listing $listing)
    {
        
        $this->authorize('view', $listing);

        $this->listingService->incrementViewCount($listing);

        $listing->load('user');

        return view('listings.show', [
            'listing' => $listing,
        ]);
    }
}

