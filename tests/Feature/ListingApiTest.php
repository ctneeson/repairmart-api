<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListingApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_get_all_listings()
    {
        $response = $this->get('/api/listings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['listingId', 'userId', 'listingStatusId', 'manufacturerId', 'listingTitle', 'listingDetail', 'listingBudgetCurrencyId', 'listingBudget',
            'useDefaultLocation', 'overrideAddressLine1', 'overrideAddressLine2', 'overrideCountryId', 'overridePostCode', 'listingExpiry', 'runId', 'ACTIVE',
            'created_at', 'updated_at']
        ]);
    }
}
