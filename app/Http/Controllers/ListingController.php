<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Listing;
use Validator;
use App\Http\Resources\ListingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $errMessage = '';
        $errInd = 0;

        $listings = DB::select('EXEC sp_getListings @ERR_MESSAGE = :ERR_MESSAGE OUTPUT, @ERR_IND = :ERR_IND OUTPUT', [
            'ERR_MESSAGE' => &$errMessage,
            'ERR_IND' => &$errInd
        ]);

        if ($errInd == 1) {
            return $this->sendError('Error executing stored procedure.', $errMessage);
        }

        return $this->sendResponse($listings, 'Listings retrieved successfully.');
    }

    /**
     * Show the form for creating a new listing.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(): JsonResponse
    {
        $errMessage = '';
        $errInd = 0;

        $productClassifications = DB::select('EXEC sp_getProductClassification @ERR_MESSAGE = :ERR_MESSAGE OUTPUT, @ERR_IND = :ERR_IND OUTPUT', [
            'ERR_MESSAGE' => &$errMessage,
            'ERR_IND' => &$errInd
        ]);

        if ($errInd == 1) {
            return $this->sendError('Error executing stored procedure.', $errMessage);
        }

        return $this->sendResponse($productClassifications, 'Product classifications retrieved successfully.');
    }
        
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            // Add other validation rules as needed
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Call the stored procedure
        $result = $this->postNewListing($input);

        if ($result['ERR_IND'] == 1) {
            return $this->sendError('Error executing stored procedure.', $result['ERR_MESSAGE']);
        }

        return $this->sendResponse($result, 'Listing created successfully.');
    } 
   
    /**
     * Execute the stored procedure to post a new listing.
     *
     * @param  array  $input
     * @return array
     */
    private function postNewListing(array $input): array
    {
        $result = DB::select('EXEC sp_postNewListing 
            @inp_userId = :inp_userId,
            @inp_listingStatusId = :inp_listingStatusId,
            @inp_manufacturerId = :inp_manufacturerId,
            @inp_listingTitle = :inp_listingTitle,
            @inp_listingDetail = :inp_listingDetail,
            @inp_listingBudgetCurrencyId = :inp_listingBudgetCurrencyId,
            @inp_listingBudget = :inp_listingBudget,
            @inp_useDefaultLocation = :inp_useDefaultLocation,
            @inp_overrideAddressLine1 = :inp_overrideAddressLine1,
            @inp_overrideAddressLine2 = :inp_overrideAddressLine2,
            @inp_overrideCountryId = :inp_overrideCountryId,
            @inp_overridePostCode = :inp_overridePostCode,
            @inp_listingExpiry = :inp_listingExpiry,
            @inp_attachmentUrlList = :inp_attachmentUrlList,
            @inp_attachmentHashList = :inp_attachmentHashList,
            @inp_attachmentOrderList = :inp_attachmentOrderList,
            @inp_productClassificationIdList = :inp_productClassificationIdList,
            @ins_rows = :ins_rows OUTPUT,
            @ins_rows_attachments = :ins_rows_attachments OUTPUT,
            @ins_rows_classifications = :ins_rows_classifications OUTPUT,
            @ERR_MESSAGE = :ERR_MESSAGE OUTPUT,
            @ERR_IND = :ERR_IND OUTPUT,
            @out_runId = :out_runId OUTPUT,
            @out_listingId = :out_listingId OUTPUT', $input);

        return $result[0];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        Log::info('GET /listings/{$id} request received', ['id' => $id]);

        $errMessage = '';
        $errInd = 0;

        $listing = DB::select('EXEC sp_getListingById @inp_listingId = :inp_listingId, @ERR_MESSAGE = :ERR_MESSAGE OUTPUT, @ERR_IND = :ERR_IND OUTPUT', [
            'inp_listingId' => $id,
            'ERR_MESSAGE' => &$errMessage,
            'ERR_IND' => &$errInd
        ]);

        if ($errInd == 1) {
            return $this->sendError('Error executing stored procedure.', $errMessage);
        }

        if (empty($listing)) {
            return $this->sendError('Listing not found.');
        }

        // Convert the result to an array
        $listingArray = (array) $listing[0];

        Log::info('Request processed successfully', ['listing' => $listingArray]);
        return $this->sendResponse(new ListingResource((object) $listingArray), 'Listing retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Listing $listing): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $listing->name = $input['name'];
        $listing->detail = $input['detail'];
        $listing->save();
   
        return $this->sendResponse(new ListingResource($listing), 'Listing updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing): JsonResponse
    {
        $listing->delete();
   
        return $this->sendResponse([], 'Listing deleted successfully.');
    }
}
