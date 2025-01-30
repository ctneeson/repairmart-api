<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Resources\ListingResource;
use App\Models\Listings;

class ListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Listings::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $listing_userId = $request->input('userId');
        $listing_statusId = $request->input('statusId');
        $listing_manufacturerId = $request->input('manufacturerId');
        $listing_title = $request->input('listingTitle');
        $listing_detail = $request->input('listingDetail');
        $listing_budgetCurrencyId = $request->input('listingBudgetCurrencyId');
        $listing_budget = $request->input('listingBudget');
        $listing_useDefaultLocation = $request->input('useDefaultLocation');
        $listing_overrideAddressLine1 = $request->input('overrideAddressLine1');
        $listing_overrideAddressLine2 = $request->input('overrideAddressLine2');
        $listing_overrideAddressLine2 = $request->input('overrideAddressLine2');
        $listing_overrideCountryId = $request->input('overrideCountryId');
        $listing_overridePostCode = $request->input('overridePostCode');
        $listing_expiry = $request->input('listingExpiry');
        $listing_attachmentUrlList = $request->input('attachmentUrlList');
        $listing_attachmentHashList = $request->input('attachmentHashList');
        $listing_attachmentOrderList = $request->input('attachmentOrderList');
        $listing_productClassificationIdList = $request->input('productClassificationIdList');

        $listing = Listings::create([
            'userId' => $listing_userId,
            'statusId' => $listing_statusId,
            'manufacturerId' => $listing_manufacturerId,
            'listingTitle' => $listing_title,
            'listingDetail' => $listing_detail,
            'listingBudgetCurrencyId' => $listing_budgetCurrencyId,
            'listingBudget' => $listing_budget,
            'useDefaultLocation' => $listing_useDefaultLocation,
            'overrideAddressLine1' => $listing_overrideAddressLine1,
            'overrideAddressLine2' => $listing_overrideAddressLine2,
            'overrideAddressLine2' => $listing_overrideAddressLine2,
            'overrideCountryId' => $listing_overrideCountryId,
            'overridePostCode' => $listing_overridePostCode,
            'listingExpiry' => $listing_expiry,
            'attachmentUrlList' => $listing_attachmentUrlList,
            'attachmentHashList' => $listing_attachmentHashList,
            'attachmentOrderList' => $listing_attachmentOrderList,
            'productClassificationIdList' => $listing_productClassificationIdList,
        ]);
        return response()->json([
            'data' => new ListingResource($listing)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Listings $listing)
    {
        return new ListingResource($listing);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $listing_userId = $request->input('userId');
        $listing_statusId = $request->input('statusId');
        $listing_manufacturerId = $request->input('manufacturerId');
        $listing_title = $request->input('listingTitle');
        $listing_detail = $request->input('listingDetail');
        $listing_budgetCurrencyId = $request->input('listingBudgetCurrencyId');
        $listing_budget = $request->input('listingBudget');
        $listing_useDefaultLocation = $request->input('useDefaultLocation');
        $listing_overrideAddressLine1 = $request->input('overrideAddressLine1');
        $listing_overrideAddressLine2 = $request->input('overrideAddressLine2');
        $listing_overrideAddressLine2 = $request->input('overrideAddressLine2');
        $listing_overrideCountryId = $request->input('overrideCountryId');
        $listing_overridePostCode = $request->input('overridePostCode');
        $listing_expiry = $request->input('listingExpiry');
        $listing_attachmentUrlList = $request->input('attachmentUrlList');
        $listing_attachmentHashList = $request->input('attachmentHashList');
        $listing_attachmentOrderList = $request->input('attachmentOrderList');
        $listing_productClassificationIdList = $request->input('productClassificationIdList');

        $listing->update([
            'userId' => $listing_userId,
            'statusId' => $listing_statusId,
            'manufacturerId' => $listing_manufacturerId,
            'listingTitle' => $listing_title,
            'listingDetail' => $listing_detail,
            'listingBudgetCurrencyId' => $listing_budgetCurrencyId,
            'listingBudget' => $listing_budget,
            'useDefaultLocation' => $listing_useDefaultLocation,
            'overrideAddressLine1' => $listing_overrideAddressLine1,
            'overrideAddressLine2' => $listing_overrideAddressLine2,
            'overrideAddressLine2' => $listing_overrideAddressLine2,
            'overrideCountryId' => $listing_overrideCountryId,
            'overridePostCode' => $listing_overridePostCode,
            'listingExpiry' => $listing_expiry,
            'attachmentUrlList' => $listing_attachmentUrlList,
            'attachmentHashList' => $listing_attachmentHashList,
            'attachmentOrderList' => $listing_attachmentOrderList,
            'productClassificationIdList' => $listing_productClassificationIdList,
        ]);
        return response()->json([
            'data' => new ListingResource($listing)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $listing->delete();
        return response()->json(null,204);
    }
}
