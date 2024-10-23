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
        Schema::table('accounts', function (Blueprint $table) {
            $table->String('Email')->nullable();
            $table->String('PresentAddress')->nullable();
            $table->String('Country')->nullable();
            $table->String('Gender')->nullable();
            $table->String('Pin')->nullable();
            $table->String('CustomerId')->nullable();
            $table->String('KioskCount')->nullable();
            $table->String('FirstPass')->nullable();
        });   //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
