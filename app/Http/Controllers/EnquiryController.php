<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Listing;
use App\Services\EnquiryService;
use Illuminate\Http\Request;


class EnquiryController extends Controller
{
    protected $enquiryService;

    public function __construct(EnquiryService $enquiryService)
    {
        $this->enquiryService = $enquiryService;
    }

    public function create(Listing $listing)
    {
        
        if (!$listing->isApproved()) {
            abort(404);
        }

        return view('enquiries.create', [
            'listing' => $listing,
        ]);
    }

    public function store(StoreEnquiryRequest $request, Listing $listing)
    {
        
        $token = $request->input('_token');
        $submittedKey = "enquiry_submitted:{$token}";
        
        if (session()->has($submittedKey)) {
            return redirect()
                ->route('listings.show', $listing)
                ->with('error', 'This enquiry has already been submitted.');
        }

        try {
            
            $this->enquiryService->createEnquiry(
                $listing,
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            session()->put($submittedKey, true);

            return redirect()
                ->route('listings.show', $listing)
                ->with('success', 'Your enquiry has been sent successfully!');

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}

