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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');  // Foreign key to Assessments
            $table->unsignedBigInteger('reviewer_id');  // Foreign key to Users (students)
            $table->unsignedBigInteger('reviewee_id');  // Foreign key to Users (students)
            $table->text('review_text');  // Free text for reviews (with validation handled in controllers)
            $table->integer('rating')->unsigned()->nullable(false); // Ensure this line is present
            $table->integer('score')->unsigned()->nullable();  // Nullable, teacher will assign score later

            // $table->integer('rating')->unsigned()->nullable();  // Remove `after('score')`
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewee_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure the combination of assessment_id, reviewer_id, and reviewee_id is unique
            $table->unique(['assessment_id', 'reviewer_id', 'reviewee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
