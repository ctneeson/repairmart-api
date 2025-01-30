<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id('listingId');
            $table->integer('userId')->unsigned();
            $table->integer('listingStatusId')->unsigned();
            $table->integer('manufacturerId')->unsigned();
            $table->string('listingTitle', 500);
            $table->string('listingDetail', 4000);
            $table->integer('listingBudgetCurrencyId')->unsigned()->nullable();
            $table->decimal('listingBudget', 10, 2)->nullable();
            $table->boolean('useDefaultLocation')->default(true);
            $table->string('overrideAddressLine1', 500)->nullable();
            $table->string('overrideAddressLine2', 500)->nullable();
            $table->integer('overrideCountryId')->unsigned()->nullable();
            $table->string('overridePostCode', 50)->nullable();
            $table->integer('listingExpiry')->unsigned();
            $table->integer('runId')->unsigned();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('ACTIVE')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
