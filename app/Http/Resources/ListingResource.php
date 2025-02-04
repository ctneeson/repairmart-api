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
            'listingStatusId' => $this->listingStatusId,
            'manufacturerId' => $this->manufacturerId,
            'listingTitle' => $this->listingTitle,
            'listingDetail' => $this->listingDetail,
            'listingBudgetCurrencyId' => $this->listingBudgetCurrencyId,
            'listingBudget' => $this->listingBudget,
            'useDefaultLocation' => $this->useDefaultLocation,
            'overrideAddressLine1' => $this->overrideAddressLine1,
            'overrideAddressLine2' => $this->overrideAddressLine2,
            'overrideCountryId' => $this->overrideCountryId,
            'overridePostCode' => $this->overridePostCode,
            'listingExpiry' => $this->listingExpiry,
            'created_at' => $this->created_at->format('YYYY-mm-dd hh:mm:ss'),
            'updated_at' => $this->updated_at->format('YYYY-mm-dd hh:mm:ss'),
        ];
    }
}
