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
        Schema::table('users', function (Blueprint $table) {
            $table->string('addressLine1', 500)->nullable();
            $table->string('addressLine2', 500)->nullable();
            $table->integer('countryId')->unsigned();
            $table->string('postCode', 50)->nullable();
            $table->integer('accountTypeId')->unsigned();
            $table->integer('runId')->unsigned();
            $table->boolean('ACTIVE')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('addressLine1');
            $table->dropColumn('addressLine2');
            $table->dropColumn('countryId');
            $table->dropColumn('postCode');
            $table->dropColumn('accountTypeId');
            $table->dropColumn('runId');
            $table->dropColumn('ACTIVE');
        });
    }
};
