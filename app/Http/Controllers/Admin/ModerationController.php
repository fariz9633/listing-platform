<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Http\Request;


class ModerationController extends Controller
{
    protected $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->middleware(['auth']);
        $this->listingService = $listingService;
    }

    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $listings = Listing::pendingModeration()
            ->with('user')
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);

        return view('admin.moderation.index', [
            'listings' => $listings,
        ]);
    }

    public function show(Listing $listing)
    {
        $this->authorize('moderate', $listing);

        $listing->load(['user', 'moderationLogs.user']);

        return view('admin.moderation.show', [
            'listing' => $listing,
        ]);
    }

    public function approve(Request $request, Listing $listing)
    {
        $this->authorize('approve', $listing);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->listingService->approveListing(
                $listing,
                $request->user(),
                $request->input('notes')
            );

            return redirect()
                ->route('admin.moderation.index')
                ->with('success', 'Listing approved successfully!');

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Listing $listing)
    {
        $this->authorize('reject', $listing);

        $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->listingService->rejectListing(
                $listing,
                $request->user(),
                $request->input('reason'),
                $request->input('notes')
            );

            return redirect()
                ->route('admin.moderation.index')
                ->with('success', 'Listing rejected.');

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function suspend(Request $request, Listing $listing)
    {
        $this->authorize('suspend', $listing);

        $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->listingService->suspendListing(
                $listing,
                $request->user(),
                $request->input('reason'),
                $request->input('notes')
            );

            return redirect()
                ->route('admin.moderation.index')
                ->with('success', 'Listing suspended.');

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}

