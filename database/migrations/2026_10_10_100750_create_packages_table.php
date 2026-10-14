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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->date('departure_date')->index();
            $table->unsignedSmallInteger('days');
            $table->boolean('includes_thaif')->default(false);
            $table->unsignedInteger('price');
            $table->string('airline');
            $table->string('madinah_hotel');
            $table->unsignedSmallInteger('madinah_stars');
            $table->string('makkah_hotel')->default('Grand Al Masya');
            $table->unsignedSmallInteger('makkah_stars')->default(4);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
