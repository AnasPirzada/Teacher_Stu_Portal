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
        Schema::table('reviews', function (Blueprint $table) {
            //
            // $table->unsignedTinyInteger('rating')->nullable()->after('review_text'); // You can choose an appropriate type based on your needs

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
             // Drop the rating column if the migration is rolled back
            //  $table->dropColumn('rating');
        });
    }
};
