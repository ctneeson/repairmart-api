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
            IF OBJECT_ID(\'sp_postNewListing\', \'P\') IS NOT NULL
                DROP PROCEDURE sp_postNewListing;
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_postNewListing
               @inp_userId bigint,
               @inp_listingStatusId bigint,
               @inp_manufacturerId bigint,
               @inp_listingTitle nvarchar(500),
               @inp_listingDetail nvarchar(4000),
               @inp_listingBudgetCurrencyId bigint,
               @inp_listingBudget decimal(10,2),
               @inp_useDefaultLocation bit,
               @inp_overrideAddressLine1 nvarchar(500),
               @inp_overrideAddressLine2 nvarchar(500),
               @inp_overrideCountryId bigint,
               @inp_overridePostCode nvarchar(50),
               @inp_listingExpiry int,
               @inp_attachmentUrlList nvarchar(4000),
               @inp_attachmentHashList nvarchar(4000),
               @inp_attachmentOrderList nvarchar(100),
               @inp_productClassificationIdList nvarchar(20),
               @ins_rows INT OUTPUT,
               @ins_rows_attachments INT OUTPUT,
               @ins_rows_classifications INT OUTPUT,
               @ERR_MESSAGE nvarchar(500) OUTPUT,
               @ERR_IND BIT OUTPUT,
               @out_runId bigint OUTPUT,
               @out_listingId bigint OUTPUT
            AS
            BEGIN
                SET NOCOUNT ON;
                SET @ERR_IND = 0;

                IF (@inp_userId IS NULL
                    OR @inp_listingStatusId IS NULL
                    OR @inp_manufacturerId IS NULL
                    OR @inp_listingTitle IS NULL
                    OR @inp_useDefaultLocation IS NULL
                    OR @inp_listingExpiry IS NULL
                    OR @inp_productClassificationIdList IS NULL
                    )
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input(s). userId, listingStatusId, listingTitle, useDefaultLocation, listingExpiry and productClassificationIdList must not be null.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF ((@inp_useDefaultLocation = 0 AND COALESCE(@inp_overrideAddressLine1, @inp_overrideAddressLine2) IS NULL)
                         OR (@inp_useDefaultLocation = 0 AND @inp_overrideCountryId IS NULL)
                    )
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input(s). When useDefaultLocation is false, override address fields must be populated.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF (@inp_userId IS NOT NULL
                         AND NOT EXISTS (SELECT 1 FROM users WHERE id = @inp_userId AND ACTIVE = 1))
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid userId. No active user could be found for the userId provided.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF (@inp_listingStatusId IS NOT NULL
                         AND NOT EXISTS (SELECT 1 FROM listingStatus WHERE listingStatusId = @inp_listingStatusId AND ACTIVE = 1))
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid listingStatusId. No active status could be found for the listingStatusId provided.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF (@inp_manufacturerId IS NOT NULL
                         AND NOT EXISTS (SELECT 1 FROM manufacturers WHERE manufacturerId = @inp_manufacturerId AND ACTIVE = 1))
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid manufacturerId. No active manufacturer could be found for the manufacturerId provided.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_listingTitle) > 500
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid listingTitle length. listingTitle cannot be longer than 500 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_listingDetail) > 4000
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid listingDetail length. listingDetail cannot be longer than 4000 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF (@inp_listingBudgetCurrencyId IS NOT NULL
                         AND NOT EXISTS (SELECT 1 FROM currency WHERE currencyId = @inp_listingBudgetCurrencyId AND ACTIVE = 1))
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid listingBudgetCurrencyId. No active currency could be found for the listingBudgetCurrencyId provided.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF (@inp_overrideCountryId IS NOT NULL
                         AND NOT EXISTS (SELECT 1 FROM country WHERE countryId = @inp_overrideCountryId AND ACTIVE = 1))
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid overrideCountryId. No active country could be found for the overrideCountryId provided.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF @inp_listingBudget < 0
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid listingBudget provided. Must not be negative.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF @inp_listingExpiry < 0
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid listingExpiry provided. Must not be negative.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF (LEN(@inp_overrideAddressLine1) > 500 OR LEN(@inp_overrideAddressLine2) > 500)
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid address length. overrideAddressLine1 and inp_overrideAddressLine2 cannot be longer than 500 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_overridePostCode) > 50
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid postcode length. overridePostCode cannot be longer than 50 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_productClassificationIdList) > 20
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input: productClassificationIdList. Length must not be greater than 20 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_attachmentUrlList) > 5000
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input: attachmentUrlList. Length must not be greater than 5000 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_attachmentHashList) > 5000
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input: attachmentHashList. Length must not be greater than 5000 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_attachmentOrderList) > 100
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input: attachmentOrderList. Length must not be greater than 100 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_attachmentUrlList) <= 5000 AND LEN(@inp_attachmentHashList) <= 5000 AND LEN(@inp_attachmentOrderList) <= 100
                BEGIN
                    -- Validate email attachments in ;-delimited list
                    DECLARE @attachmentIterator INT;
                    SET @attachmentIterator = 1;
                    IF OBJECT_ID(\'tempdb..#temp_listingAttachments\') IS NOT NULL
                    DROP TABLE #temp_listingAttachments;
                    
                    CREATE TABLE #temp_listingAttachments (
                        attachmentUrl nvarchar(1000),
                        hashValue nvarchar(1000),
                        attachmentOrder nvarchar(10),
                        rowNum INT
                    );
                    INSERT INTO #temp_listingAttachments (attachmentUrl, hashValue, attachmentOrder, rowNum)
                    SELECT u.value, h.value, o.value, u.ordinal
                    FROM STRING_SPLIT(@inp_attachmentUrlList,\' ;\',1) u
                    JOIN STRING_SPLIT(@inp_attachmentHashList,\' ;\',1) h
                    ON u.ordinal = h.ordinal
                    JOIN STRING_SPLIT(@inp_attachmentOrderList,\' ;\',1) o
                    ON u.ordinal = o.ordinal;

                    WHILE (@attachmentIterator <= (SELECT MAX(rowNum) FROM #temp_listingAttachments))
                    BEGIN
                        IF NOT EXISTS (SELECT 1 FROM #temp_listingAttachments
                                       WHERE rowNum = @attachmentIterator
                                       AND LEN(REPLACE(REPLACE(attachmentUrl,\' \',\'\'),\' \',\'\'))>0)
                        BEGIN
                            SET @ERR_MESSAGE = \'Invalid input: attachmentUrlList. Attachment URLs must not be empty.\';
                            SET @ERR_IND = 1;
                            BREAK;
                        END
                        ELSE IF NOT EXISTS (SELECT 1 FROM #temp_listingAttachments
                                            WHERE rowNum = @attachmentIterator
                                            AND LEN(REPLACE(REPLACE(hashValue,\' \',\'\'),\' \',\'\'))>0)
                        BEGIN
                            SET @ERR_MESSAGE = \'Invalid input: attachmentHashList. Attachment hash values must not be empty.\';
                            SET @ERR_IND = 1;
                            BREAK;
                        END
                        ELSE IF (SELECT TRY_CAST(attachmentOrder AS int) FROM #temp_listingAttachments
                                 WHERE rowNum = @attachmentIterator) NOT IN (SELECT rowNum FROM #temp_listingAttachments)
                        BEGIN
                            SET @ERR_MESSAGE = \'Invalid input: attachmentOrderList. List must contain sequential positive integers.\';
                            SET @ERR_IND = 1;
                            BREAK;
                        END
                    SET @attachmentIterator = @attachmentIterator + 1;
                    END
                END
                ELSE IF LEN(@inp_productClassificationIdList) > 20
                BEGIN
                    SET @ERR_MESSAGE = \'Invalid input: productClassificationIdList. Length must not be greater than 20 characters.\';
                    SET @ERR_IND = 1;
                END
                ELSE IF LEN(@inp_productClassificationIdList) <= 20
                BEGIN
                    -- Validate product classification IDs in ;-delimited list
                    DECLARE @classificationIterator INT;
                    SET @classificationIterator = 1;
                    IF OBJECT_ID(\'tempdb..#temp_listingClassifications\') IS NOT NULL
                    DROP TABLE #temp_listingClassifications;
                    
                    CREATE TABLE #temp_listingClassifications (
                        productClassificationId nvarchar(5),
                        rowNum INT
                    );
                    INSERT INTO #temp_listingClassifications (productClassificationId, rowNum)
                    SELECT value, ordinal FROM STRING_SPLIT(@inp_productClassificationIdList,\' ;\',1);

                    WHILE (@classificationIterator <= (SELECT MAX(rowNum) FROM #temp_listingClassifications))
                    BEGIN
                        IF NOT EXISTS (SELECT 1 FROM #temp_listingClassifications
                                       WHERE rowNum = @classificationIterator
                                       AND LEN(REPLACE(REPLACE(productClassificationId,\' \',\'\'),\' \',\'\'))>0)
                        BEGIN
                            SET @ERR_MESSAGE = \'Invalid input: listingProductClassifications. Product Classifications must not be empty.\';
                            SET @ERR_IND = 1;
                            BREAK;
                        END
                        ELSE IF NOT EXISTS (SELECT 1 FROM productClassification
                                            WHERE ACTIVE = 1
                                            AND productClassificationId = (SELECT TRY_CAST(productClassificationId AS bigint)
                                                                       FROM #temp_listingClassifications
                                                                       WHERE rowNum = @classificationIterator)
                                           )
                        BEGIN
                            DECLARE @productClassificationId nvarchar(5) = (SELECT productClassificationId FROM #temp_listingClassifications WHERE rowNum = @classificationIterator);
                            SET @ERR_MESSAGE = \'Invalid input: listingProductClassifications. An active Product Classification ID was not found for: \'+@productClassificationId+\'.\';
                            SET @ERR_IND = 1;
                            BREAK;
                        END
                    SET @classificationIterator = @classificationIterator + 1;
                    END
                END

                IF @ERR_IND = 1
                BEGIN
                    RAISERROR (@ERR_MESSAGE, 16, 1);
                    RETURN;
                END

                BEGIN TRANSACTION;

                BEGIN TRY;
                    -- Get runID
                    INSERT INTO runIds(processName, UPDATED_BY)
                    VALUES (\'sp_postNewListing\', @inp_userId);
                    SET @out_runId = (SELECT MAX(runId) from runIds WHERE processName = \'sp_postNewListing\' AND UPDATED_BY = @inp_userId);

                    -- insert listing
                    INSERT INTO listings(userId, listingStatusId, manufacturerId, listingTitle, listingDetail, listingBudgetCurrencyId,
                                         listingBudget, useDefaultLocation, overrideAddressLine1, overrideAddressLine2, overrideCountryId,
                                         overridePostCode, listingExpiry, runId)
                    VALUES (@inp_userId,
                            @inp_listingStatusId,
                            @inp_manufacturerId,
                            @inp_listingTitle,
                            @inp_listingDetail,
                            @inp_listingBudgetCurrencyId,
                            @inp_listingBudget,
                            @inp_useDefaultLocation,
                            @inp_overrideAddressLine1,
                            @inp_overrideAddressLine2,
                            @inp_overrideCountryId,
                            @inp_overridePostCode,
                            @inp_listingExpiry,
                            @out_runId);
                
                    SET @ins_rows = @@ROWCOUNT;
                    SET @out_listingId = (SELECT MAX(listingId) from listings WHERE runId = @out_runId);

                    -- insert attachments
                    SET @attachmentIterator = 1;
                    WHILE (@attachmentIterator <= (SELECT MAX(rowNum) FROM #temp_listingAttachments))
                    BEGIN
                        INSERT INTO attachments(attachmentUrl, hashValue, runId)
                        SELECT attachmentUrl, hashValue, @out_runId
                        FROM #temp_listingAttachments WHERE rowNum = @attachmentIterator;

                        INSERT INTO listings_attachments(listingId, attachmentId, attachmentOrder, runId)
                        SELECT @out_listingId, a.attachmentId, t.attachmentOrder, @out_runId
                        FROM attachments a
                        JOIN #temp_listingAttachments t
                         ON a.hashValue = t.hashValue
                        WHERE a.runId = @out_runId;

                        SET @attachmentIterator = @attachmentIterator + 1;
                    END
                    SET @ins_rows_attachments = (SELECT COUNT(*) FROM attachments
                                                 WHERE runId = @out_runId);

                    -- insert product classifications
                    SET @classificationIterator = 1;

                    IF OBJECT_ID(\'tempdb..#temp_listingClassifications_new\') IS NOT NULL
                    DROP TABLE #temp_listingClassifications_new;
                    
                    CREATE TABLE #temp_listingClassifications_new (
                        productClassificationId nvarchar(5),
                        rowNum INT
                    );
                    INSERT INTO #temp_listingClassifications_new (productClassificationId, rowNum)
                    SELECT value, ordinal FROM STRING_SPLIT(@inp_productClassificationIdList,\' ;\',1);

                    WHILE (@classificationIterator <= (SELECT MAX(rowNum) FROM #temp_listingClassifications_new))
                    BEGIN
                        DECLARE @out_productClassificationId bigint = (SELECT CAST(productClassificationId AS bigint)
                                                                    FROM #temp_listingClassifications_new
                                                                    WHERE rowNum = @classificationIterator);

                        INSERT INTO listings_productClassification(listingId, productClassificationId, runId)
                        VALUES(@out_listingId, @out_productClassificationId, @out_runId);

                        SET @classificationIterator = @classificationIterator + 1;
                    END
                    SET @ins_rows_classifications = (SELECT COUNT(*) FROM listings_productClassification
                                                     WHERE runId = @out_runId);

                    COMMIT TRANSACTION;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_postNewListing');
    }
};