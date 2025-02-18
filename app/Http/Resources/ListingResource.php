<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'listingId' => $this->listingId,
            'userId' => $this->userId,
            'name' => $this->name,
            'listingStatusId' => $this->listingStatusId,
            'manufacturerId' => $this->manufacturerId,
            'manufacturerName' => $this->manufacturerName,
            'listingTitle' => $this->listingTitle,
            'listingDetail' => $this->listingDetail,
            'listingBudgetCurrencyId' => $this->listingBudgetCurrencyId,
            'currencyISO' => $this->currencyISO,
            'listingBudget' => $this->listingBudget,
            'listingAddressLine1' => $this->listingAddressLine1,
            'listingAddressLine2' => $this->listingAddressLine2,
            'listingCountryId' => $this->listingCountryId,
            'countryName' => $this->countryName,
            'listingPostCode' => $this->listingPostCode,
            'listingExpiryDate' => $this->listingExpiryDate,
        ];
    }
}
