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
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100);
            $table->unsignedDecimal('rating', 3, 1);
            $table->enum('age_restriction', ['None', '7+', '12+', '16+']);
            $table->string('description', 500);
            $table->dateTime('released_at');
            $table->timestamps();
        });

        // Keep rating below 10.1.
        DB::statement('ALTER TABLE movies ADD CONSTRAINT chk_rating CHECK (rating < 10.1);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
