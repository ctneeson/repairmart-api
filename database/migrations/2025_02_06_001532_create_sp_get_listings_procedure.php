<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            IF OBJECT_ID(\'sp_getListings\', \'P\') IS NOT NULL
                DROP PROCEDURE sp_getListings;
        ');

        DB::unprepared('
        CREATE PROCEDURE sp_getListings
        @ERR_MESSAGE nvarchar(500) OUTPUT,
        @ERR_IND BIT OUTPUT
        AS
        BEGIN

            SET NOCOUNT ON;
            SET @ERR_IND = 0;

            BEGIN TRY;

            SELECT
            l.listingId,
            l.userId,
            u.[name],
            l.listingStatusId,
            l.manufacturerId,
            m.manufacturerName,
            l.listingTitle,
            l.listingDetail,
            l.listingBudgetCurrencyId,
            cu.currencyISO,
            l.listingBudget,
            CASE WHEN l.useDefaultLocation = 0 THEN l.overrideAddressLine1 ELSE u.addressLine1 END AS listingAddressLine1,
            CASE WHEN l.useDefaultLocation = 0 THEN l.overrideAddressLine2 ELSE u.addressLine2 END AS listingAddressLine2,
            CASE WHEN l.useDefaultLocation = 0 THEN l.overrideCountryId ELSE u.countryId END AS listingCountryId,
            co.countryName,
            CASE WHEN l.useDefaultLocation = 0 THEN l.overridePostCode ELSE u.postCode END AS listingPostCode,
            DATEADD(day,l.listingExpiry,l.created_at) AS listingExpiryDate
            FROM listings l
            JOIN users u ON l.userId = u.id
            JOIN listingStatus ls ON l.listingStatusId = ls.listingStatusId AND ls.ACTIVE = 1
            JOIN manufacturers m ON l.manufacturerId = m.manufacturerId AND m.ACTIVE = 1
            JOIN currency cu ON l.listingBudgetCurrencyId = cu.currencyId AND cu.ACTIVE = 1
            JOIN country co ON COALESCE(l.overrideCountryId, 1) = co.countryId
            WHERE l.ACTIVE = 1;

            END TRY

            BEGIN CATCH
                ROLLBACK TRANSACTION;
                SET @ERR_MESSAGE = ERROR_MESSAGE();
                SET @ERR_IND = 1;
                RAISERROR (@ERR_MESSAGE, 16, 1);
            END CATCH;
        END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_getListings');
    }
};
