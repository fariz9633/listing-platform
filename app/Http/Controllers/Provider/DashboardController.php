<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Http\Request;

/**
 * Provider DashboardController
 * 
 * Handles provider's listing management dashboard.
 * THIN CONTROLLER - business logic in services.
 */
class DashboardController extends Controller
{
    protected $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->middleware(['auth']);
        $this->listingService = $listingService;
    }

    /**
     * Display provider dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get provider's listings with pagination
        $listings = Listing::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('provider.dashboard', [
            'listings' => $listings,
        ]);
    }

    /**
     * Show create listing form
     */
    public function create()
    {
        $this->authorize('create', Listing::class);

        return view('provider.listings.create');
    }

    /**
     * Store new listing
     */
    public function store(StoreListingRequest $request)
    {
        $this->authorize('create', Listing::class);

        $listing = $this->listingService->createListing(
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('provider.listings.edit', $listing)
            ->with('success', 'Listing created successfully!');
    }

    /**
     * Show edit listing form
     */
    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);

        return view('provider.listings.edit', [
            'listing' => $listing,
        ]);
    }

    /**
     * Update listing
     */
    public function update(StoreListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        try {
            $this->listingService->updateListing($listing, $request->validated());

            return redirect()
                ->route('provider.listings.edit', $listing)
                ->with('success', 'Listing updated successfully!');

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Submit listing for moderation
     */
    public function submit(Listing $listing)
    {
        $this->authorize('submit', $listing);

        try {
            $this->listingService->submitForModeration($listing);

            return redirect()
                ->route('provider.dashboard')
                ->with('success', 'Listing submitted for moderation!');

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete listing
     */
    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        $listing->delete();

        return redirect()
            ->route('provider.dashboard')
            ->with('success', 'Listing deleted successfully!');
    }
}

