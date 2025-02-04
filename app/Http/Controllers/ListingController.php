<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Listing;
use Validator;
use App\Http\Resources\ListingResource;
use Illuminate\Http\JsonResponse;

class ListingController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $listings = Listing::all();
    
        return $this->sendResponse(ListingResource::collection($listings), 'Listings retrieved successfully.');
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
            'detail' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $listing = Listing::create($input);
   
        return $this->sendResponse(new ListingResource($listing), 'Listing created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $listing = Listing::find($id);
  
        if (is_null($listing)) {
            return $this->sendError('Listing not found.');
        }
   
        return $this->sendResponse(new ListingResource($listing), 'Listing retrieved successfully.');
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
