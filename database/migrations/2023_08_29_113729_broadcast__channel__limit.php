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
        if (Schema::hasTable('broadcasts')) {
            Schema::table('broadcasts', function (Blueprint $table) {
                $table->string('channel', 255)->change();
            });
        } else {
            Schema::create('broadcasts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('movie_id');
                $table->string('channel', 255);
                $table->dateTime('broadcasted_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
